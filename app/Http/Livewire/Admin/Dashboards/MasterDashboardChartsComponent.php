<?php

namespace App\Http\Livewire\Admin\Dashboards;

use App\Models\Sample;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MasterDashboardChartsComponent extends Component
{
    public $laboratory_id;

    public $labels;

    public $currentYearSampleData;

    public $previousYearSampleData;

    public $monthTestLabels;

    public $currentYearTestData;

    public $previousYearTestData;

    public $usersActiveCount;

    public $usersSuspendedCount;

    // protected $listeners = ['refreshCharts'];

    public function mount()
    {
        $this->loadCharts();
    }

    public function loadCharts(): void
    {
        $monthLabels['month'] = collect([]);

        $currentYearSamples = Sample::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y'))
        ->when($this->laboratory_id != 0, function ($query) {
            $query->where('creator_lab', $this->laboratory_id);
        }, function ($query) {
            return $query;
        })
        ->orderBy('created_at', 'asc')
        ->groupBy(DB::raw('Month(created_at)'))
            ->get();

        $previousYearSamples = Sample::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y') - 1)
        ->when($this->laboratory_id != 0, function ($query) {
            $query->where('creator_lab', $this->laboratory_id);
        }, function ($query) {
            return $query;
        })
        ->orderBy('created_at', 'asc')
        ->groupBy(DB::raw('Month(created_at)'))
        ->get();

        $currentYearSamples->map(function ($month) use ($monthLabels) {
            $monthLabels['month']->push($month->month_name);
        });

        $previousYearSamples->map(function ($month) use ($monthLabels) {
            $monthLabels['month']->push($month->month_name);
        });

        $monthLabels['month'] = array_unique($monthLabels['month']->toArray(), SORT_REGULAR);
        $this->labels = $monthLabels;
        $this->currentYearSampleData = $currentYearSamples;
        $this->previousYearSampleData = $previousYearSamples;

        //Tests chart data
        $monthTestLabels['month'] = collect([]);

        $currentYearTests = TestResult::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y'))
        ->when($this->laboratory_id != 0, function ($query) {
            $query->where('creator_lab', $this->laboratory_id);
        }, function ($query) {
            return $query;
        })
        ->where('status', 'Approved')
        ->orderBy('created_at', 'asc')
        ->groupBy(DB::raw('Month(created_at)'))
            ->get();

        $previousYearTests = TestResult::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y') - 1)
        ->when($this->laboratory_id != 0, function ($query) {
            $query->where('creator_lab', $this->laboratory_id);
        }, function ($query) {
            return $query;
        })
        ->where('status', 'Approved')
        ->orderBy('created_at', 'asc')
        ->groupBy(DB::raw('Month(created_at)'))
        ->get();

        $currentYearTests->map(function ($month) use ($monthTestLabels) {
            $monthTestLabels['month']->push($month->month_name);
        });

        $previousYearTests->map(function ($month) use ($monthTestLabels) {
            $monthTestLabels['month']->push($month->month_name);
        });

        $monthTestLabels['month'] = array_unique($monthTestLabels['month']->toArray(), SORT_REGULAR);
        $this->monthTestLabels = $monthTestLabels;
        $this->currentYearTestData = $currentYearTests;
        $this->previousYearTestData = $previousYearTests;

        //USERS
        $this->usersActiveCount = User::select('*')->where(['is_active' => 1])
        ->when($this->laboratory_id != 0, function ($query) {
            $query->where('laboratory_id', $this->laboratory_id);
        }, function ($query) {
            return $query;
        })->count();

        $this->usersSuspendedCount = User::select('*')->where(['is_active' => 0])
                ->when($this->laboratory_id != 0, function ($query) {
                    $query->where('laboratory_id', $this->laboratory_id);
                }, function ($query) {
                    return $query;
                })->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboards.master-dashboard-charts-component');
    }
}
