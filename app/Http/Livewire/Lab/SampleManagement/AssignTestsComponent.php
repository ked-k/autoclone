<?php
namespace App\Http\Livewire\Lab\SampleManagement;

use App\Exports\SamplesExport;
use App\Models\Admin\SampleReferralReason;
use App\Models\Admin\Test;
use App\Models\AliquotingAssignment;
use App\Models\Facility;
use App\Models\Laboratory;
use App\Models\Lab\SampleManagent\SampleReferral;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Study;
use App\Models\TestAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Component;
use Livewire\WithPagination;

class AssignTestsComponent extends Component
{
    use WithPagination;

    public $perPage = 50;

    public $search = '';

    public $orderBy = 'lab_no';

    public $orderAsc = true;

    public $sample;

    public $sample_is_for = 'Testing';

    public $tests_requested;

    public $aliquots_requested;

    public $request_acknowledged_by;

    public $sample_id;

    public $test_id;

    public $assignee;

    public $assignedTests;

    public $backlog;
    public $labNo;
    public $sampleId;
    public $facility_id = 0;
    public $job         = '';

    public $sampleType;

    public $created_by = 0;

    public $from_date = '';

    public $to_date  = '';
    public $study_id = 0;

    protected $paginationTheme = 'bootstrap';

    public $referralLab_id;
    public $referralLab_type;
    public $reason_id;
    public $referral_code;
    public $referral_type = 'External';
    public $courier;
    public $storage_condition;
    public $transport_medium;
    public $sample_integrity;
    public $temperature_on_dispatch;
    public $additional_notes;
    public $date_referred;
    public $reason;
    public $status = 'Pending';

    public $labs;
    public $reasons;
    public $refer_samples = false;
    public $referredTests = [];

    public $sampleIds = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function export()
    {
        if (count($this->sampleIds) > 0) {
            return (new SamplesExport($this->sampleIds))->download('Samples_' . date('Y-m-d') . '_' . now()->toTimeString() . '.xlsx');
        } else {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error', 'message' => 'Oops! No Samples selected for export!']);
        }
    }
    public function mount()
    {
        $this->tests_requested    = collect([]);
        $this->aliquots_requested = collect([]);
        $this->assignedTests      = [];

        $this->labs          = collect([]);
        $this->reasons       = collect([]);
        $this->date_referred = now()->format('Y-m-d\TH:i'); // datetime-local format
    }

    public function activateTest($id)
    {
        $this->test_id = $id;
    }

    public function UpdatedAssignee()
    {
        $this->backlog = TestAssignment::where(['assignee' => $this->assignee, 'status' => 'Assigned'])->count();
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function viewTests(Sample $sample)
    {
        $this->reset(['tests_requested']);
        $this->sample = null;
        $this->sample = $sample;

        $this->assignedTests   = TestAssignment::where('sample_id', $sample->id)->get()->pluck('test_id')->toArray();
        $this->referredTests   = SampleReferral::where('sample_id', $sample->id)->get()->pluck('test_id')->toArray();
        $tests                 = Test::whereIn('id', array_diff($sample->tests_requested, $this->assignedTests ?? []))->get();
        $this->tests_requested = $tests;
        $this->test_id         = $tests[0]->id;
        $this->sample_id       = $sample->id;
        // dd($this->sample->lab_no);
        $this->labNo                   = $this->sample->lab_no;
        $this->sampleId                = $this->sample->sample_identity;
        $this->request_acknowledged_by = $sample->request_acknowledged_by;

        // $this->dispatchBrowserEvent('view-tests');
    }

    public function viewAliquots(Sample $sample)
    {
        $this->reset(['aliquots_requested']);
        $this->sample                  = $sample;
        $aliquots                      = SampleType::whereIn('id', (array) $sample->tests_requested)->orderBy('type', 'asc')->get();
        $this->aliquots_requested      = $aliquots;
        $this->sample_id               = $sample->id;
        $this->request_acknowledged_by = $sample->request_acknowledged_by;

        // $this->dispatchBrowserEvent('view-aliquots');
    }
    public function updatedReferSamples()
    {
        if ($this->refer_samples) {
            $this->reasons = SampleReferralReason::all();
            $this->updatedReferralType();
        }
    }
    public function updatedReferralType()
    {
        if ($this->referral_type == 'External') {
            $this->labs = Facility::all();
        } else {
            $this->labs = Laboratory::all();
        }
    }
    public function addReferral($test_id)
    {
        $this->validate([
            'reason_id'      => 'required',
            'referralLab_id' => 'required',
            'referral_type'  => 'required|string',
            'date_referred'  => 'required|date',
        ]);

        $facility = $this->referral_type === 'External'
        ? Facility::findOrFail($this->referralLab_id)
        : Laboratory::findOrFail($this->referralLab_id);

        $referral            = new SampleReferral();
        $referral->sample_id = $this->sample_id;
        $referral->test_id   = $test_id;
        // $referral->referralLab             = $this->referralLab;
        $referral->referralable()->associate($facility);
        $referral->reason_id               = $this->reason_id;
        $referral->referral_code           = $this->referral_code;
        $referral->referral_type           = $this->referral_type;
        $referral->courier                 = $this->courier;
        $referral->storage_condition       = $this->storage_condition;
        $referral->transport_medium        = $this->transport_medium;
        $referral->sample_integrity        = $this->sample_integrity;
        $referral->temperature_on_dispatch = $this->temperature_on_dispatch;
        $referral->additional_notes        = $this->additional_notes;
        $referral->date_referred           = $this->date_referred;
        $referral->reason                  = $this->reason;
        // $referral->status = $this->status;
        $referral->save();
        // $sample                   = Sample::find($this->sample_id);
        $sample   = $this->sample;
        $referred = $sample->referred_tests; // already decoded array via accessor
        if (! in_array($test_id, $referred)) {
            $referred[]             = $test_id;
            $sample->referred_tests = $referred; // triggers the mutator and encodes
            $sample->save();
        }

    }
    public function assignTest()
    {
        $this->validate([
            'assignee' => 'required|integer',
        ]);

        $isExist = TestAssignment::select('*')
            ->where('sample_id', $this->sample_id)
            ->where('test_id', $this->test_id)
            ->exists();

        if ($isExist) {
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Test already Assigned to someone!']);
        } else {
            DB::transaction(function () {

                $test_assignment            = new TestAssignment();
                $test_assignment->sample_id = $this->sample_id;
                $test_assignment->test_id   = $this->test_id;
                $test_assignment->assignee  = $this->assignee;
                $test_assignment->save();
                if ($this->refer_samples) {
                    $this->addReferral($this->test_id);
                    array_push($this->referredTests, $this->test_id);
                    $test_assignment->update(['is_referred' => true]);
                }
                $this->resetInputs();
                array_push($this->assignedTests, $this->test_id);
                if (array_diff($this->sample->tests_requested, $this->assignedTests) == []) {
                    $this->sample->update(['status' => 'Assigned']);
                    $this->dispatchBrowserEvent('close-modal');
                    $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test Assignment completed successfully!']);
                } else {
                    $this->tests_requested = $this->tests_requested->where('id', '!=', $this->test_id)->values();
                    $this->test_id         = $this->tests_requested[0]->id;
                    $this->reset(['assignee', 'backlog']);
                    $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test Assigned successfully!']);
                }

                $details = [
                    'subject'    => 'Auto-Lab Test',
                    'greeting'   => 'Hello, I hope this email finds you well',
                    'body'       => 'You have been assigned a new test Lab No#' . $test_assignment->sample->lab_no . ', Please log in and take the necessary actions.',
                    'actiontext' => 'Click Here for more details',
                    'actionurl'  => URL::signedRoute('test-request'),
                    'user_id'    => $this->assignee,
                ];
                // try {
                //     $email = SendGeneralNotificationJob::dispatch($details);
                // } catch (\Throwable $th) {
                // }
            });

        }
    }

    public function assignAllTests()
    {
        $this->validate([
            'assignee' => 'required|integer',
        ]);
        $assigned = [];
        foreach ($this->tests_requested as $test) {
            $myTest = TestAssignment::updateOrCreate(
                ['sample_id' => $this->sample_id, 'test_id' => $test->id],
                ['assignee' => $this->assignee]
            );
            array_push($this->assignedTests, $test->id);
            $assigned = ['Lab No' => $myTest->sample->lab_no];
            if ($this->refer_samples) {
                $this->addReferral($test->id);
                array_push($this->referredTests, $test->id);
                $myTest->update(['is_referred' => true]);

            }
        }
        $this->resetInputs();

        $labNos  = json_encode($assigned);
        $details = [
            'subject'    => 'Auto-Lab Test',
            'greeting'   => 'Hello, I hope this email finds you well',
            'body'       => 'You have been assigned a multiple tests #' . $labNos . ', Please log in and take the necessary actions.',
            'actiontext' => 'Click Here for more details',
            'actionurl'  => URL::signedRoute('test-request'),
            'user_id'    => $this->assignee,
        ];
        // try {
        //     $email = SendGeneralNotificationJob::dispatch($details);
        // } catch (\Throwable $th) {
        // }
        if (array_diff($this->sample->tests_requested, $this->assignedTests) == []) {
            $this->sample->update(['status' => 'Assigned']);
            $this->reset(['assignee', 'backlog']);
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test Assignment completed successfully!']);
        } else {
            $this->tests_requested = $this->tests_requested->where('id', '!=', $this->test_id)->values();
            $this->test_id         = $this->tests_requested[0]->id;
            $this->reset(['assignee', 'backlog']);
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test Assigned successfully!']);
        }
    }

    public function assignAliquotingTasks()
    {
        $this->validate([
            'assignee' => 'required|integer',
        ]);
        $isExist = AliquotingAssignment::select('*')
            ->where('sample_id', $this->sample_id)
            ->exists();

        if ($isExist) {
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Aliquoting Task already Assigned to someone!']);
        } else {
            $aliquoting_assignment            = new AliquotingAssignment();
            $aliquoting_assignment->sample_id = $this->sample_id;
            $aliquoting_assignment->assignee  = $this->assignee;
            $aliquoting_assignment->save();

            $sample = Sample::where('id', $this->sample_id)->first();
            $sample->update(['status' => 'Assigned']);

            $this->reset(['assignee', 'backlog']);
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Aliquoting Task assigned successfully!']);
        }
    }

    public function acknowledgeRequest()
    {
        // $this->sample->request_acknowledged_by = Auth::id();
        // $this->sample->date_acknowledged = now();
        // $this->sample->status = 'Processing';
        $this->sample->update([
            'request_acknowledged_by' => Auth::id(),
            'date_acknowledged'       => now(),
            'status'                  => 'Processing',
        ]);
        $this->request_acknowledged_by = $this->sample->request_acknowledged_by;

        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Sample Updated successfully!']);
    }

    public function close()
    {
        $this->reset(['sample_id', 'assignee', 'test_id', 'sampleId', 'labNo']);
        $this->resetInputs();
        $this->tests_requested    = collect([]);
        $this->aliquots_requested = collect([]);
    }

    public function resetInputs()
    {
        $this->reset([
            'referralLab_id',
            'reason_id',
            'referral_code',
            'referral_type',
            'courier',
            'storage_condition',
            'transport_medium',
            'sample_integrity',
            'temperature_on_dispatch',
            'additional_notes',
            'date_referred',
            'reason',
        ]);
    }
    public function getSamples()
    {
        $samples = Sample::search($this->search, ['Accessioned', 'Processing'])
            ->whereIn('status', ['Accessioned', 'Processing'])
            ->when($this->sample_is_for != 'Storage', function ($query) {
                $query->where('test_count', '>', 0)
                    ->whereNotNull('tests_requested');
            }, function ($query) {
                return $query;
            })
            ->when($this->facility_id != 0, function ($query) {
                $query->whereHas('participant', function ($query) {
                    $query->where('facility_id', $this->facility_id);
                });
            }, function ($query) {
                return $query;
            })
            ->when($this->study_id != 0, function ($query) {
                $query->where('study_id', $this->study_id);
            }, function ($query) {
                return $query;
            })
            ->when($this->created_by != 0, function ($query) {
                $query->where('created_by', $this->created_by);
            }, function ($query) {
                return $query;
            })
            ->when($this->job != '', function ($query) {
                $query->where('sample_is_for', $this->job);
            }, function ($query) {
                return $query;
            })
            ->when($this->sampleType != 0, function ($query) {
                $query->where('sample_type_id', $this->sampleType);
            }, function ($query) {
                return $query;
            })
            ->when($this->from_date != '' && $this->to_date != '', function ($query) {
                $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
            }, function ($query) {
                return $query;
            })
            ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => $this->sample_is_for])
            ->with(['participant', 'sampleType:id,type', 'study:id,name', 'requester:id,name', 'collector:id,name', 'sampleReception'])
            ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);
        $this->sampleIds = $samples->pluck('id')->toArray();
        return $samples;
    }

    public function getSampleTasks()
    {
        $sampleTasks = Sample::where(['creator_lab' => auth()->user()->laboratory_id])
            ->whereIn('status', ['Accessioned', 'Processing'])->get();

        $counts['forTestingCount'] = $sampleTasks->filter(function ($sample) {
            return $sample->sample_is_for === 'Testing';
        })->count();

        $counts['forAliquotingCount'] = $sampleTasks->filter(function ($sample) {
            return $sample->sample_is_for == 'Aliquoting';
        })->count();

        $counts['forStorageCount'] = $sampleTasks->filter(function ($sample) {
            return $sample->sample_is_for == 'Storage';
        })->count();

        return $counts;
    }

    public function render()
    {

        $data['samples']            = $this->getSamples();
        $data['users']              = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->get();
        $data['tests']              = $this->tests_requested;
        $data['aliquots']           = $this->aliquots_requested;
        $data['forTestingCount']    = $this->getSampleTasks()['forTestingCount'];
        $data['forAliquotingCount'] = $this->getSampleTasks()['forAliquotingCount'];
        $data['forStorageCount']    = $this->getSampleTasks()['forStorageCount'];
        $data['users']              = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->latest()->get();
        $data['facilities']         = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->get();
        $data['sampleTypes']        = SampleType::where('creator_lab', auth()->user()->laboratory_id)->orderBy('type', 'asc')->get();
        $data['jobs']               = Sample::select('sample_is_for')->distinct()->get();
        $data['studies']            = Study::whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->where('facility_id', $this->facility_id)->get();

        return view('livewire.lab.sample-management.assign-tests-component', $data);
    }
}
