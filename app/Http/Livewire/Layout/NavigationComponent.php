<?php
namespace App\Http\Livewire\Layout;

use App\Models\AliquotingAssignment;
use App\Models\Laboratory;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Sample;
use App\Models\SampleReception;
use App\Models\TestAssignment;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class NavigationComponent extends Component
{
    public $navItem;
    public $link;

    // Count variables
    public $batchesCount              = 0;
    public $participantCount          = 0;
    public $samplesCount              = 0;
    public $testAssignedCount         = 0;
    public $AliquotingAssignedCount   = 0;
    public $rejectedResultsCount      = 0;
    public $testRequestsCount         = 0;
    public $testsPendindReviewCount   = 0;
    public $testsPendindApprovalCount = 0;
    public $testReportsCount          = 0;
    public $testsRejectedCount        = 0;
    public $testsPerformedCount       = 0;

    public $usersCount        = 0;
    public $rolesCount        = 0;
    public $permissionsCount  = 0;
    public $laboratoryCount   = 0;
    public $designationCount  = 0;
    public $facilityCount     = 0;
    public $studyCount        = 0;
    public $requesterCount    = 0;
    public $collectorCount    = 0;
    public $courierCount      = 0;
    public $platformCount     = 0;
    public $kitCount          = 0;
    public $sampleTypeCount   = 0;
    public $testCategoryCount = 0;
    public $testCount         = 0;

    protected $listeners = ['updateNav', 'loadCounts' => 'loadCounts'];
    public $activeTab    = 'samples';
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
    public function sampleData()
    {
        return Sample::where('creator_lab', auth()->user()->laboratory_id);
    }
    public function mount()
    {
        // $this->navItem = 'samplemgt';
        // $this->link = 'dashboard';
        // $this->loadCounts();
    }
    public function loadCounts()
    {
        $user  = auth()->user();
        $labId = $user->laboratory_id;

        // Permission checks
        $canAccessionSamples = $user->hasPermission(['accession-samples']);
        $canViewParticipant  = $user->hasPermission(['view-participant-info']);
        $canEnterResults     = $user->hasPermission(['enter-results']);
        $canAssignTest       = $user->hasPermission(['assign-test-requests']);
        $canReviewResults    = $user->hasPermission(['review-results']);
        $canApproveResults   = $user->hasPermission(['approve-results']);
        $canViewReports      = $user->hasPermission(['view-result-reports']);
        $canManageUsers      = $user->hasPermission(['manage-users']);
        $canAccessSettings   = $user->hasPermission(['access-settings']);

        // 1. Batches count (complex query)
        if ($canAccessionSamples) {
            $this->batchesCount = SampleReception::where('creator_lab', $labId)
                ->where(function (Builder $query) {
                    $query->whereRaw('samples_accepted != samples_handled')
                        ->orWhereHas('sample', function (Builder $query) {
                            $query->whereNull('tests_requested')
                                ->orWhere('test_count', 0);
                        });
                })->count();
        }

        // 2. Participant and samples counts
        if ($canViewParticipant) {
            $sampleQuery            = Sample::where('creator_lab', $labId);
            $this->participantCount = $sampleQuery->distinct()->count('participant_id');
            $this->samplesCount     = $sampleQuery->count();
        }

        // 3. Assigned tests and aliquoting
        if ($canEnterResults) {
            $this->testAssignedCount = TestAssignment::where('assignee', $user->id)
                ->where('status', 'Assigned')->count();

            $this->AliquotingAssignedCount = AliquotingAssignment::where('assignee', $user->id)
                ->where('status', 'Assigned')->count();

            $this->rejectedResultsCount = TestResult::where([
                'status'       => 'Rejected',
                'performed_by' => $user->id,
                'creator_lab'  => $labId,
            ])->count();
        }

        // 4. Test requests count
        if ($canAssignTest) {
            $this->testRequestsCount = Sample::where('creator_lab', $labId)
                ->whereIn('sample_is_for', ['Testing', 'Aliquoting', 'Storage'])
                ->whereIn('status', ['Accessioned', 'Processing'])
                ->where(function ($query) {
                    $query->whereNotNull('tests_requested')
                        ->where('test_count', '>', 0)
                        ->orWhere(function ($query) {
                            $query->where('sample_is_for', 'Storage')
                                ->whereNull('tests_requested');
                        });
                })->count();
        }

        // 5. Grouped TestResult counts by status (combine review, approve, reports)
        if ($canEnterResults || $canReviewResults || $canApproveResults || $canViewReports) {
            $resultCounts = TestResult::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = 'Pending Review' THEN 1 ELSE 0 END) as pending_review,
            SUM(CASE WHEN status = 'Reviewed' THEN 1 ELSE 0 END) as pending_approval,
            SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved
        ")->where('creator_lab', $labId)->first();

            $this->testsRejectedCount        = $resultCounts->rejected;
            $this->testsPendindReviewCount   = $resultCounts->pending_review;
            $this->testsPendindApprovalCount = $resultCounts->pending_approval;
            $this->testReportsCount          = $resultCounts->approved;
            $this->testsPerformedCount       = $resultCounts->total;
        } else {
            // If no permission for results reports
            $this->testReportsCount    = 0;
            $this->testsPerformedCount = 0;
        }

        // 6. Management counts (cached)
        if ($canManageUsers) {
            $this->usersCount       = cache()->remember('users_count', 600, fn() => User::where('is_active', 1)->count());
            $this->rolesCount       = cache()->remember('roles_count', 600, fn() => Role::count());
            $this->permissionsCount = cache()->remember('permissions_count', 600, fn() => Permission::count());
            $this->laboratoryCount  = cache()->remember('labs_count', 600, fn() => Laboratory::where('is_active', 1)->count());
        }

        // 7. Settings counts (cached & filtered)
        // if ($canAccessSettings) {
        //     $associatedFacilities = $user->laboratory->associated_facilities ?? [];
        //     $associatedStudies    = $user->laboratory->associated_studies ?? [];

        //     $this->designationCount = cache()->remember('designation_count', 600, fn() => Designation::where('is_active', 1)->count());
        //     $this->facilityCount    = Facility::where('is_active', 1)->whereIn('id', $associatedFacilities)->count();
        //     $this->studyCount       = Study::where('is_active', 1)->whereIn('id', $associatedStudies)->count();
        //     $this->requesterCount   = Requester::where('is_active', 1)->whereIn('study_id', $associatedStudies)->count();
        //     $this->collectorCount   = Collector::where('is_active', 1)->whereIn('facility_id', $associatedFacilities)->count();
        //     $this->courierCount     = Courier::where('is_active', 1)->whereIn('facility_id', $associatedFacilities)->count();

        //     $this->platformCount     = Platform::where('creator_lab', $labId)->where('is_active', 1)->count();
        //     $this->kitCount          = Kit::where('creator_lab', $labId)->where('is_active', 1)->count();
        //     $this->sampleTypeCount   = SampleType::where('creator_lab', $labId)->where('status', 1)->count();
        //     $this->testCategoryCount = cache()->remember("test_category_count_{$labId}", 600, fn() => TestCategory::where('creator_lab', $labId)->count());
        //     $this->testCount         = Test::where('creator_lab', $labId)->where('status', 1)->count();
        // }
    }

    // public function loadCounts()
    // {
    //     $user  = Auth::user();
    //     $labId = $user->laboratory_id;

    //     // Permission: accession-samples
    //     if ($user->hasPermission(['accession-samples'])) {
    //         $this->batchesCount = Cache::remember("lab:{$labId}:batchesCount", now()->addMinutes(5), function () use ($labId) {
    //             return SampleReception::where('creator_lab', $labId)
    //                 ->where(function (Builder $query) {
    //                     $query->whereRaw('samples_accepted != samples_handled')
    //                         ->orWhereHas('sample', function (Builder $query) {
    //                             $query->whereNull('tests_requested')
    //                                 ->orWhere('test_count', 0);
    //                         });
    //                 })->count();
    //         });
    //     }

    //     // Permission: view-participant-info
    //     if ($user->hasPermission(['view-participant-info'])) {
    //         $this->participantCount = Cache::remember("lab:{$labId}:participantCount", now()->addMinutes(5), function () {
    //             return $this->sampleData()->distinct()->count('participant_id');
    //         });

    //         $this->samplesCount = Cache::remember("lab:{$labId}:samplesCount", now()->addMinutes(5), function () {
    //             return $this->sampleData()->count();
    //         });
    //     }

    //     // Permission: enter-results
    //     if ($user->hasPermission(['enter-results'])) {
    //         $userId = $user->id;

    //         $this->testAssignedCount = Cache::remember("user:{$userId}:testAssignedCount", now()->addMinutes(5), fn() =>
    //             TestAssignment::where('assignee', $userId)->where('status', 'Assigned')->count()
    //         );

    //         $this->AliquotingAssignedCount = Cache::remember("user:{$userId}:AliquotingAssignedCount", now()->addMinutes(5), fn() =>
    //             AliquotingAssignment::where('assignee', $userId)->where('status', 'Assigned')->count()
    //         );

    //         $this->rejectedResultsCount = Cache::remember("user:{$userId}:rejectedResultsCount", now()->addMinutes(5), fn() =>
    //             TestResult::where([
    //                 'status'       => 'Rejected',
    //                 'performed_by' => $userId,
    //                 'creator_lab'  => $labId,
    //             ])->count()
    //         );
    //     }

    //     // Permission: assign-test-requests
    //     if ($user->hasPermission(['assign-test-requests'])) {
    //         $this->testRequestsCount = Cache::remember("lab:{$labId}:testRequestsCount", now()->addMinutes(5), function () {
    //             return $this->sampleData()
    //                 ->whereIn('sample_is_for', ['Testing', 'Aliquoting', 'Storage'])
    //                 ->whereIn('status', ['Accessioned', 'Processing'])
    //                 ->where(function ($query) {
    //                     $query->whereNotNull('tests_requested')->where('test_count', '>', 0)
    //                         ->orWhere(function ($query) {
    //                             $query->where('sample_is_for', 'Storage')->whereNull('tests_requested');
    //                         });
    //                 })->count();
    //         });
    //     }

    //     // Permissions: result processing
    //     if ($user->hasPermission(['enter-results', 'review-results', 'approve-results', 'view-result-reports'])) {
    //         $resultSummary = Cache::remember("lab:{$labId}:resultSummary", now()->addMinutes(5), fn() =>
    //             TestResult::selectRaw("
    //             COUNT(*) as total,
    //             SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected,
    //             SUM(CASE WHEN status = 'Pending Review' THEN 1 ELSE 0 END) as pending_review,
    //             SUM(CASE WHEN status = 'Reviewed' THEN 1 ELSE 0 END) as pending_approval,
    //             SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved
    //         ")->where('creator_lab', $labId)->first()
    //         );

    //         $this->testsRejectedCount        = $resultSummary->rejected;
    //         $this->testsPendindReviewCount   = $resultSummary->pending_review;
    //         $this->testsPendindApprovalCount = $resultSummary->pending_approval;
    //         $this->testReportsCount          = $resultSummary->approved;
    //         $this->testsPerformedCount       = $resultSummary->total;
    //     }

    //     // Permission: manage-users
    //     if ($user->hasPermission(['manage-users'])) {
    //         $this->usersCount = Cache::remember("usersCount", now()->addMinutes(10), fn() =>
    //             User::where('is_active', 1)->count()
    //         );

    //         $this->rolesCount = Cache::remember("rolesCount", now()->addMinutes(10), fn() =>
    //             Role::count()
    //         );

    //         $this->permissionsCount = Cache::remember("permissionsCount", now()->addMinutes(10), fn() =>
    //             Permission::count()
    //         );

    //         $this->laboratoryCount = Cache::remember("labsCount", now()->addMinutes(10), fn() =>
    //             Laboratory::where('is_active', 1)->count()
    //         );
    //     }

    //     // Optionally enable the below section if needed
    //     // if ($user->hasPermission(['access-settings'])) {
    //     //     $facilities = $user->laboratory->associated_facilities ?? [];
    //     //     $studies    = $user->laboratory->associated_studies ?? [];

    //     //     $this->designationCount = Cache::remember("designationCount", now()->addMinutes(10), fn() =>
    //     //         Designation::where('is_active', 1)->count()
    //     //     );

    //     //     $this->facilityCount = Facility::where('is_active', 1)->whereIn('id', $facilities)->count();
    //     //     $this->studyCount    = Study::where('is_active', 1)->whereIn('id', $studies)->count();
    //     //     $this->requesterCount = Requester::where('is_active', 1)->whereIn('study_id', $studies)->count();
    //     //     $this->collectorCount = Collector::where('is_active', 1)->whereIn('facility_id', $facilities)->count();
    //     //     $this->courierCount   = Courier::where('is_active', 1)->whereIn('facility_id', $facilities)->count();

    //     //     $this->platformCount     = Platform::where('creator_lab', $labId)->where('is_active', 1)->count();
    //     //     $this->kitCount          = Kit::where('creator_lab', $labId)->where('is_active', 1)->count();
    //     //     $this->sampleTypeCount   = SampleType::where('creator_lab', $labId)->where('status', 1)->count();
    //     //     $this->testCategoryCount = TestCategory::where('creator_lab', $labId)->count();
    //     //     $this->testCount         = Test::where('creator_lab', $labId)->where('status', 1)->count();
    //     // }
    // }

    public function updateNav($target)
    {
        if ($target == 'testsPendindReviewCount') {
            $this->testsPendindReviewCount--;
            $this->navItem = 'samplemgt';
            $this->link    = 'review';
        } elseif ($target == 'testsPendindApprovalCount') {
            $this->testsPendindApprovalCount--;
            $this->testReportsCount++;
            $this->navItem = 'samplemgt';
            $this->link    = 'approve';
        }
    }

    public function render()
    {
        return view('livewire.layout.navigation-component');
    }
}
