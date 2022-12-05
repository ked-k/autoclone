<?php

namespace App\Http\Livewire\Admin\Dashboards;

use App\Models\Sample;
use App\Models\TestResult;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UserDashboardChartsComponent extends Component
{
    public $labels;

    public $currentYearSampleData;

    public $previousYearSampleData;

    public $monthTestLabels;

    public $currentYearTestData;

    public $previousYearTestData;

    public function mount()
    {
        $this->loadCharts();
    }

    public function loadCharts(): void
    {
        $monthLabels['month'] = collect([]);

        $currentYearSamples = Sample::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y'))
        ->where(['creator_lab' => auth()->user()->laboratory_id, 'created_by' => auth()->user()->id])
        ->orderBy('created_at', 'asc')
        ->groupBy(DB::raw('Month(created_at)'))
            ->get();

        $previousYearSamples = Sample::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y') - 1)
        ->where(['creator_lab' => auth()->user()->laboratory_id, 'created_by' => auth()->user()->id])
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
        ->where(['creator_lab' => auth()->user()->laboratory_id, 'created_by' => auth()->user()->id])
        ->orderBy('created_at', 'asc')
        ->groupBy(DB::raw('Month(created_at)'))
            ->get();

        $previousYearTests = TestResult::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y') - 1)
        ->where(['creator_lab' => auth()->user()->laboratory_id, 'created_by' => auth()->user()->id])
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
    }

    public function render()
    {
        return view('livewire.admin.dashboards.user-dashboard-charts-component');
    }
}
