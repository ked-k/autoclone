<?php

namespace App\Http\Livewire\Admin\Dashboards;

use App\Models\Sample;
use App\Models\SampleReception;
use App\Models\TestAssignment;
use App\Models\TestResult;
use Illuminate\Support\Carbon;
use Livewire\Component;

class UserDashboardComponent extends Component
{
    public $view;

    public function render()
    {
        //TOTAL SAMPLE RECEPTION
        $data['samplesDelivered'] = SampleReception::where('created_by', auth()->user()->id)
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->sum('samples_delivered');

        $data['samplesAccepted'] = SampleReception::where('created_by', auth()->user()->id)
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->sum('samples_accepted');

        $data['samplesRejected'] = SampleReception::where('created_by', auth()->user()->id)
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->sum('samples_rejected');

        $data['sampleAccessioned'] = Sample::where('created_by', auth()->user()->id)
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->count();

        //SAMPLES
        $data['sampleAccessioned'] = Sample::where('created_by', auth()->user()->id)
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->count();

        $data['sampleForTesting'] = Sample::where(['created_by' => auth()->user()->id, 'sample_is_for' => 'Testing'])
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->count();

        $data['participantCount'] = Sample::select('participant_id')->where('created_by', auth()->user()->id)
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })->distinct('participant_id')
        ->count();

        //TOTAL TESTS ASSIGNED
        $data['testAssignedCount'] = TestAssignment::where('assignee', auth()->user()->id)
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->count();

        $data['testDoneAssignedCount'] = TestAssignment::where('assignee', auth()->user()->id)->whereIn('status', ['Test Done'])
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->count();

        //TESTS PERFORMED
        $data['completedTestResultCount'] = TestResult::where('performed_by', auth()->user()->id)
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->count();

        $data['testsPendindReviewCount'] = TestResult::where('performed_by', auth()->user()->laboratory_id)->where('status', 'Pending Review')
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->count();

        $data['testsPendindApprovalCount'] = TestResult::where('performed_by', auth()->user()->laboratory_id)->where('status', 'Reviewed')
        ->when($this->view == 'today', function ($query) {
            $query->whereDay('created_at', '=', date('d'));
        })
        ->when($this->view == 'week', function ($query) {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        })
        ->when($this->view == 'month', function ($query) {
            $query->whereMonth('created_at', '=', date('m'));
        })
        ->when($this->view == 'year', function ($query) {
            $query->whereYear('created_at', '=', date('Y'));
        })
        ->count();

        return view('livewire.admin.dashboards.user-dashboard-component', $data);
    }
}
