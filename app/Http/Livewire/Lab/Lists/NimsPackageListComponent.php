<?php

namespace App\Http\Livewire\Lab\Lists;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class NimsPackageListComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $orderBy = 'created_at';
    public $orderAsc = false;
    public $statusFilter = 'incoming';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'orderBy' => ['except' => 'created_at'],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        // You might want to get the institution from authenticated user
        $this->institution = auth()->user()->institution ?? null;
    }

    public function refresh()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function getRequestsProperty()
    {
        try {
            // Make API call to your endpoint
            $key =env('INSTITUTION_API_KEY');
            $response = Http::withHeaders([
                'X-Institution-API-Key' => $key, // Adjust based on your auth
                'Accept' => 'application/json',
            ])->get(url(env('CENTRAL_INSTANCE_URL').'/api/v1/SampleReferralCrossBorder/referral/incoming', ['type' => $this->statusFilter]));
                // dd($response->body());
            if ($response->successful()) {
                $data = $response->json();
                $requests = collect($data['data'] ?? []);

                // Apply search filter
                if ($this->search) {
                    $requests = $requests->filter(function ($request) {
                        return stripos($request['request_no'], $this->search) !== false ||
                               stripos($request['requester_institution']['name'], $this->search) !== false ||
                               stripos($request['status'], $this->search) !== false;
                    });
                }

                // Apply sorting
                $requests = $this->orderAsc
                    ? $requests->sortBy($this->orderBy)
                    : $requests->sortByDesc($this->orderBy);

                // Paginate manually
                return $this->paginateCollection($requests, $this->perPage);

            } else {
                       $this->dispatchBrowserEvent('not-found', ['type' => 'error',  'message' => 'Failed to fetch referral requests. ']);
                session()->flash('error', 'Failed to fetch referral requests.');
                return collect([]);
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error',  'message' => 'Error connecting to API: ' . $e->getMessage()]);

            // session()->flash('error', 'Error connecting to API: ' . $e->getMessage());
            return collect([]);
        }
    }
    private function paginateCollection($items, $perPage)
    {
        $page = Paginator::resolveCurrentPage();
        $total = $items->count();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }
    private function paginateColllection($items, $perPage)
    {
        $page = Paginator::resolveCurrentPage();
        $total = $items->count();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' =>Paginator::resolveCurrentPath()]
        );
    }

    public function getStatusBadgeClass($status)
    {
        return match(strtolower($status)) {
            'completed', 'result added' => 'bg-success',
            'pending', 'submitted' => 'bg-warning',
            'rejected', 'cancelled' => 'bg-danger',
            'dispatched', 'delivered' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    public function viewRequest($requestNo)
    {
        // Redirect to view single request page
        return redirect()->route('referral-requests.show', $requestNo);
    }

    public function render()
    {

        return view('livewire.lab.lists.nims-package-list-component', [
            'requests' => $this->requests,
        ]);
    }
}
