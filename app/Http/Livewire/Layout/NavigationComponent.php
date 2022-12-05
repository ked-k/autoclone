<?php

namespace App\Http\Livewire\Layout;

use App\Models\Admin\Test;
use App\Models\Collector;
use App\Models\Courier;
use App\Models\Designation;
use App\Models\Facility;
use App\Models\Kit;
use App\Models\Laboratory;
use App\Models\Permission;
use App\Models\Platform;
use App\Models\Requester;
use App\Models\Role;
use App\Models\Sample;
use App\Models\SampleReception;
use App\Models\SampleType;
use App\Models\Study;
use App\Models\TestAssignment;
use App\Models\TestCategory;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NavigationComponent extends Component
{
    public $navItem;

    public $link;

    public $batchesCount;

    public $participantCount;

    public $samplesCount;

    public $testAssignedCount;

    public $testRequestsCount;

    public $testsPendindReviewCount;

    public $testsPendindApprovalCount;

    public $testReportsCount;

    public $usersCount;

    public $rolesCount;

    public $permissionsCount;

    public $laboratoryCount;

    public $designationCount;

    public $facilityCount;

    public $studyCount;

    public $requesterCount;

    public $collectorCount;

    public $courierCount;

    public $platformCount;

    public $kitCount;

    public $sampleTypeCount;

    public $testCategoryCount;

    public $testCount;

    protected $listeners = ['updateNav'];

    public function mount()
    {
        if (Auth::user()->hasPermission(['create-reception-info|review-reception-info'])) {
            $this->batchesCount = SampleReception::where('creator_lab', auth()->user()->laboratory_id)->whereRaw('samples_accepted>samples_handled')->count();
        }
        if (Auth::user()->hasPermission(['view-participant-info'])) {
            $this->participantCount = Sample::where('creator_lab', auth()->user()->laboratory_id)->distinct()->count('participant_id');
            $this->samplesCount = Sample::where('creator_lab', auth()->user()->laboratory_id)->count();
        }
        if (Auth::user()->hasPermission(['create-reception-info|review-reception-info'])) {
            $this->testAssignedCount = TestAssignment::where('assignee', auth()->user()->id)->whereIn('status', ['Assigned'])->count();
        }
        if (Auth::user()->hasPermission(['enter-results'])) {
            $this->testRequestsCount = Sample::where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => 'Testing'])->whereIn('status', ['Accessioned', 'Processing'])->count();
        }
        if (Auth::user()->hasPermission(['review-results'])) {
            $this->testsPendindReviewCount = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Pending Review')->count();
        }
        if (Auth::user()->hasPermission(['approve-results'])) {
            $this->testsPendindApprovalCount = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Reviewed')->count();
        }
        if (Auth::user()->hasPermission(['view-result-reports'])) {
            $this->testReportsCount = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Approved')->count();
            $this->testsPerformedCount = TestResult::where('creator_lab', auth()->user()->laboratory_id)->where('status', 'Approved')->count();
        }
        if (Auth::user()->hasPermission(['manage-users'])) {
            $this->usersCount = User::where(['is_active' => 1])->count();
            $this->rolesCount = Role::count();
            $this->permissionsCount = Permission::count();
            $this->laboratoryCount = Laboratory::where('is_active', 1)->count();
        }

        if (Auth::user()->hasPermission(['access-settings'])) {
            $this->designationCount = Designation::where('is_active', 1)->count();
            $this->facilityCount = Facility::where('is_active', 1)->whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->count(); //depends
            $this->studyCount = Study::where('is_active', 1)->whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->count(); //depends
            $this->requesterCount = Requester::where('is_active', 1)->whereIn('study_id', auth()->user()->laboratory->associated_studies ?? [])->count(); //depends
            $this->collectorCount = Collector::where('is_active', 1)->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])->count(); //depends
            $this->courierCount = Courier::where('is_active', 1)->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])->count(); //depends

            $this->platformCount = Platform::where('creator_lab', auth()->user()->laboratory_id)->where('is_active', 1)->count();
            $this->kitCount = Kit::where('creator_lab', auth()->user()->laboratory_id)->where('is_active', 1)->count();
            $this->sampleTypeCount = SampleType::where('creator_lab', auth()->user()->laboratory_id)->where('status', 1)->count();
            $this->testCategoryCount = TestCategory::where('creator_lab', auth()->user()->laboratory_id)->count();
            $this->testCount = Test::where('creator_lab', auth()->user()->laboratory_id)->where('status', 1)->count();
        }
    }

    public function updateNav($target)
    {
        if ($target == 'testsPendindReviewCount') {
            $this->testsPendindReviewCount--;
            $this->navItem = 'samplemgt';
            $this->link = 'review';
        } elseif ($target == 'testsPendindApprovalCount') {
            $this->testsPendindApprovalCount--;
            $this->testReportsCount++;
            $this->navItem = 'samplemgt';
            $this->link = 'approve';
        }
    }

    public function render()
    {
        return view('livewire.layout.navigation-component');
    }
}
