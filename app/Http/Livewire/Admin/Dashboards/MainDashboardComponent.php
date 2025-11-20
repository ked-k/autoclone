<?php

namespace App\Http\Livewire\Admin\Dashboards;

use App\Models\Collector;
use App\Models\Courier;
use App\Models\Facility;
use App\Models\Laboratory;
use App\Models\Requester;
use App\Models\Sample;
use App\Models\SampleReception;
use App\Models\Study;
use App\Models\TestResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MainDashboardComponent extends Component
{
    public $labels;

    public $currentYearSampleData;

    public $previousYearSampleData;

    public $monthTestLabels;

    public $currentYearTestData;

    public $previousYearTestData;

    public function countData()
    {
        //SAMPLES
        $count['participantCount'] = Sample::where('creator_lab', auth()->user()->laboratory_id)->distinct()->count('participant_id');
        $count['batchesCount'] = SampleReception::where('creator_lab', auth()->user()->laboratory_id)->whereRaw('samples_accepted=samples_handled')->count();
        $count['samplesCount'] = Sample::where('creator_lab', auth()->user()->laboratory_id)->count();
        $count['samplesTodayCount'] = Sample::where('creator_lab', auth()->user()->laboratory_id)->whereDay('created_at', '=', date('d'))->count();
        $count['samplesThisWeekCount'] = Sample::where('creator_lab', auth()->user()->laboratory_id)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $count['samplesThisMonthCount'] = Sample::where('creator_lab', auth()->user()->laboratory_id)->whereMonth('created_at', '=', date('m'))->count();
        $count['samplesThisYearCount'] = Sample::where('creator_lab', auth()->user()->laboratory_id)->whereYear('created_at', '=', date('Y'))->count();

        //TESTS
        $count['testsPerformedCount'] = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Approved')->count();
        $count['testsTodayCount'] = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Approved')->whereDay('created_at', '=', date('d'))->count();
        $count['testsThisWeekCount'] = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Approved')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $count['testsThisMonthCount'] = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Approved')->whereMonth('created_at', '=', date('m'))->count();
        $count['testsThisYearCount'] = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Approved')->whereYear('created_at', '=', date('Y'))->count();

        //USERS
        $count['usersActiveCount'] = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->count();
        $count['usersSuspendedCount'] = User::where(['is_active' => 0, 'laboratory_id' => auth()->user()->laboratory_id])->count();

        //LABS
        $count['laboratoryCount'] = Laboratory::where('is_active', 1)->count();

        //FACILITIES
        $count['facilityActiveCount'] = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->where('is_active', 1)->count();
        $count['facilitySuspendedCount'] = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->where('is_active', 0)->count();

        //STUDIES
        $count['studyActiveCount'] = Study::where('is_active', 1)->whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->count();
        $count['studySuspendedCount'] = Study::where('is_active', 0)->whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->count();

        //REQUESTERS
        $count['requesterActiveCount'] = Requester::where('is_active', 1)->whereIn('study_id', auth()->user()->laboratory->associated_studies ?? [])->count();
        $count['requesterSuspendedCount'] = Requester::where('is_active', 0)->whereIn('study_id', auth()->user()->laboratory->associated_studies ?? [])->count();

        //PHLEBOTOMISTS
        $count['collectorActiveCount'] = Collector::where('is_active', 1)->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])->count();
        $count['collectorSuspendedCount'] = Collector::where('is_active', 0)->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])->count();

        //COURIERS
        $count['courierActiveCount'] = Courier::where('is_active', 1)->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])->count();
        $count['courierSuspendedCount'] = Courier::where('is_active', 0)->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])->count();

        return $count;
    }

    public function render()
    {
        // $currentYearSamples = Sample::select(DB::raw('COUNT(*) as count'), DB::raw('MONTHNAME(created_at) as month_name'))
        //             ->whereYear('created_at', date('Y'))
        //             ->groupBy(DB::raw('Month(created_at)'))->orderBy('created_at', 'asc')
        //             ->pluck('count', 'month_name');

        // $previousYearSamples = Sample::select(DB::raw('COUNT(*) as count'), DB::raw('MONTHNAME(created_at) as month_name'))
        // ->whereYear('created_at', date('Y') - 1)
        // ->groupBy(DB::raw('Month(created_at)'))->orderBy('created_at', 'asc')
        // ->pluck('count', 'month_name');

        //Samples chart data
        $monthLabels['month'] = collect([]);

        $currentYearSamples = Sample::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y'))
        ->where('creator_lab', auth()->user()->laboratory_id)
        ->orderBy('created_at', 'asc')
        ->groupBy(DB::raw('Month(created_at)'))
            ->get();

        $previousYearSamples = Sample::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y') - 1)
        ->where('creator_lab', auth()->user()->laboratory_id)
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
        ->where('creator_lab', auth()->user()->laboratory_id)
        ->orderBy('created_at', 'asc')
        ->groupBy(DB::raw('Month(created_at)'))
            ->get();

        $previousYearTests = TestResult::select(DB::raw('COUNT(*) as count'), DB::raw('date_format(created_at,"%b") as month_name'))
        ->whereYear('created_at', date('Y') - 1)
        ->where('creator_lab', auth()->user()->laboratory_id)
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

        $dataCounts = $this->countData();

        return view('livewire.admin.dashboards.main-dashboard-component', $dataCounts);
    }
}
