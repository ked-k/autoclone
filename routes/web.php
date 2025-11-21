<?php

use App\Helpers\LoginActivity;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Admin\KitComponent;
use App\Http\Livewire\Admin\TestComponent;
use App\Http\Livewire\Admin\UserComponent;
use App\Http\Livewire\Admin\StudyComponent;
use App\Http\Livewire\Admin\CourierComponent;
use App\Http\Livewire\Admin\FacilityComponent;
use App\Http\Livewire\Admin\PlatformComponent;
use App\Http\Livewire\Admin\CollectorComponent;
use App\Http\Livewire\Admin\RequesterComponent;
use App\Http\Controllers\ResultReportController;
use App\Http\Livewire\Admin\LaboratoryComponent;
use App\Http\Livewire\Admin\SampleTypeComponent;
use App\Http\Controllers\SearchResultsController;
use App\Http\Livewire\Admin\DesignationComponent;
use App\Http\Livewire\Admin\UserProfileComponent;
use App\Http\Controllers\Auth\UserRolesController;
use App\Http\Livewire\Admin\TestCategoryComponent;
use App\Http\Livewire\Admin\UserActivityComponent;
use App\Http\Livewire\Lab\Lists\SamplesListComponent;
use App\Http\Livewire\Lab\Reports\TatReportComponent;
use App\Http\Livewire\Reports\GeneralReportComponent;
use App\Http\Controllers\FacilityInformationController;
use App\Http\Controllers\Auth\UserPermissionsController;
use App\Http\Livewire\Lab\Lists\NimsPackageListComponent;
use App\Http\Livewire\Lab\Lists\ParticipantListComponent;
use App\Http\Livewire\Admin\Reports\SystemReportComponent;
use App\Http\Livewire\Lab\Lists\TestsAmendedListComponent;
use App\Http\Livewire\Lab\Reports\MultipleReportComponent;
use App\Http\Livewire\Lab\SampleStorage\FreezersComponent;
use App\Http\Livewire\Lab\Lists\ReferralReceptionComponent;
use App\Http\Livewire\Lab\Reports\ResultTatReportComponent;
use App\Http\Livewire\Lab\Reports\TestCountReportComponent;
use App\Http\Controllers\Auth\UserRolesAssignmentController;
use App\Http\Livewire\Lab\Lists\TestsPerformedListComponent;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Livewire\Lab\Reports\TestsPerLabReportComponent;
use App\Http\Livewire\Admin\Dashboards\MainDashboardComponent;
use App\Http\Livewire\Admin\Dashboards\UserDashboardComponent;
use App\Http\Livewire\Admin\Reports\SystemReportViewComponent;
use App\Http\Livewire\Admin\Reports\SystemReportItemsComponent;
use App\Http\Livewire\Lab\SampleManagement\TestReviewComponent;
use App\Http\Livewire\Admin\Dashboards\MasterDashboardComponent;
use App\Http\Livewire\Lab\Reports\PendingSamplesReportComponent;
use App\Http\Livewire\Lab\Reports\TestStudyCountReportComponent;
use App\Http\Livewire\Lab\SampleManagement\AssignTestsComponent;
use App\Http\Livewire\Lab\SampleManagement\TestReportsComponent;
use App\Http\Livewire\Lab\SampleManagement\TestRequestComponent;
use App\Http\Livewire\Lab\Lists\OutgoingReferralManagerComponent;
use App\Http\Livewire\Lab\SampleManagement\StoreSamplesComponent;
use App\Http\Livewire\Lab\SampleManagement\TestApprovalComponent;
use App\Http\Livewire\Lab\SampleManagement\TestRejectedComponent;
use App\Http\Livewire\Lab\SampleStorage\FreezerLocationComponent;
use App\Http\Livewire\Lab\SampleManagement\SampleAliquotsComponent;
use App\Http\Livewire\Lab\SampleManagement\PaternitySpecimenRequest;
use App\Http\Livewire\Lab\SampleManagement\RejectedResultsComponent;
use App\Http\Livewire\Lab\SampleManagement\ResultAmendmentComponent;
use App\Http\Livewire\Lab\SampleManagement\SampleReceptionComponent;
use App\Http\Livewire\Lab\SampleManagement\SpecimenRequestComponent;
use App\Http\Livewire\Lab\SampleManagement\AttachTestResultComponent;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', [AuthenticatedSessionController::class, 'home'])->middleware('guest')->name('home');
// Route::get('generatelabno', [AuthenticatedSessionController::class, 'generate']);
Route::get('user/account', UserProfileComponent::class)->name('user.account')->middleware('auth');
Route::group(['middleware' => ['auth', 'suspended_user']], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::group(['middleware' => ['permission:access-settings'], 'prefix' => 'settings'], function () {
            Route::get('testCategories', TestCategoryComponent::class)->name('categories');
            Route::get('sampleTypes', SampleTypeComponent::class)->name('sampletypes');
            Route::get('test', TestComponent::class)->name('tests');
            Route::get('designations', DesignationComponent::class)->name('designations');
            Route::get('laboratories', LaboratoryComponent::class)->name('laboratories');
            Route::get('facilities', FacilityComponent::class)->name('facilities');
            Route::get('requesters', RequesterComponent::class)->name('requesters');
            Route::get('sampleCollectors', CollectorComponent::class)->name('collectors');
            Route::get('kits', KitComponent::class)->name('kits');
            Route::get('platforms', PlatformComponent::class)->name('platforms');
            Route::get('studies', StudyComponent::class)->name('studies');
            Route::get('couriers', CourierComponent::class)->name('couriers');
            Route::get('quality/reports', SystemReportComponent::class)->name('qualityReports');
            Route::get('quality/reports/{code}/items', SystemReportItemsComponent::class)->name('qualityReportItems');
            Route::get('quality/reports/{code}/view', SystemReportViewComponent::class)->name('qualityReportView');
        });

        Route::group(['middleware' => ['permission:manage-users'], 'prefix' => 'usermgt'], function () {
            Route::get('users', UserComponent::class)->name('usermanagement');
            Route::resource('user-roles', UserRolesController::class);
            Route::resource('user-permissions', UserPermissionsController::class);
            Route::resource('user-roles-assignment', UserRolesAssignmentController::class);
            Route::resource('facilityInformation', FacilityInformationController::class);
            Route::get('activityTrail', UserActivityComponent::class)->name('useractivity');
            Route::get('loginActivity', function () {
                $logs = LoginActivity::logActivityLists();

                return view('super-admin.logActivity', compact('logs'));
            })->name('logs');
        });
    });

    Route::get('user/myActivity', UserActivityComponent::class)->name('myactivity');

    Route::group(['prefix' => 'samplemgt'], function () {
        Route::get('reception', SampleReceptionComponent::class)->middleware('permission:create-reception-info')->name('samplereception');
        Route::get('nims', NimsPackageListComponent::class)->middleware('permission:create-reception-info')->name('nimsamplereception');
        Route::get('/referral-reception/{batch}', ReferralReceptionComponent::class)->name('referral-requests.show');
        Route::get('/referral-outgoing/{batch}', OutgoingReferralManagerComponent::class)->name('referral-requests.outgoing');
// Route::get('/referral-accession/{batch}', ReferralSampleAccessionComponent::class)->name('referral.accession');
        Route::get('batch/{batch}/specimen-req', SpecimenRequestComponent::class)->middleware('permission:accession-samples')->name('specimen-request');
        Route::get('batch/{batch}/paternity-req', PaternitySpecimenRequest::class)->middleware('permission:accession-samples')->name('paternity-test-reception');
        Route::get('testAssignment', AssignTestsComponent::class)->middleware('permission:assign-test-requests')->name('test-request-assignment');
        Route::get('testRequests', TestRequestComponent::class)->middleware('permission:acknowledge-test-request')->name('test-request');
        Route::get('sample/{id}/testResults', AttachTestResultComponent::class)->middleware('permission:enter-results', 'signed')->name('attach-test-results');
        Route::get('sample/{id}/aliquots', SampleAliquotsComponent::class)->middleware(['permission:enter-results', 'signed'])->name('attach-aliquots');
        Route::get('sample/{id}/store', StoreSamplesComponent::class)->middleware(['permission:enter-results', 'signed'])->name('store-sample');
        Route::get('resultReview', TestReviewComponent::class)->middleware('permission:review-results')->name('test-review');
        Route::get('resultRejected', TestRejectedComponent::class)->middleware('permission:review-results')->name('tests-rejected');
        Route::get('resultApproval', TestApprovalComponent::class)->middleware('permission:approve-results')->name('test-approval');

        Route::get('resultAmendment/{tracker}', ResultAmendmentComponent::class)->name('result-amendment');
        Route::get('amendedResults/{type}', TestsAmendedListComponent::class)->name('amended-results');
        Route::get('result/{id}/original-report', [ResultReportController::class, 'viewOriginallyAmendedResult'])->name('print-original-report');

        Route::get('resultReports', TestReportsComponent::class)->middleware('permission:view-result-reports')->name('test-reports');
        Route::get('rejectedResults', RejectedResultsComponent::class)->middleware('permission:enter-results')->name('rejected-results');
        Route::get('result/{id}/report', [ResultReportController::class, 'show'])->name('result-report');
        Route::get('result/{id}/print-report', [ResultReportController::class, 'print'])->name('print-result-report');
        Route::get('result/{session_id}/print-multi-report', [ResultReportController::class, 'printMultiple'])->name('print-result-multi');
        Route::get('result/{id}/attachment', [ResultReportController::class, 'download'])->name('attachment.download');
        Route::get('participants', ParticipantListComponent::class)->middleware('permission:view-participant-info')->name('participants');

        Route::get('samplesList', SamplesListComponent::class)->middleware('permission:view-participant-info')->name('samples-list');
        Route::get('samplesPendingList', PendingSamplesReportComponent::class)->middleware('permission:view-participant-info')->name('samples-pending-list');
        Route::get('samplesResultsMultiple', MultipleReportComponent::class)->middleware('permission:view-participant-info')->name('multiple-result-list');
        Route::get('samplesCount', GeneralReportComponent::class)->middleware('permission:view-participant-info')->name('samples-count');
        Route::get('testsPerformedList', TestsPerformedListComponent::class)->middleware('permission:view-participant-info')->name('tests-performed-list');

        Route::get('crs/patient/load', [ResultReportController::class, 'getCrsPatient'])->name('loadcrsPatient');

        Route::group(['middleware' => 'signed'], function () {
            Route::get('batch/{sampleReception}/searchResults', [SearchResultsController::class, 'batchSearchResults'])->name('batch-search-results');
            Route::get('participant/{participant}/searchResults', [SearchResultsController::class, 'participantSearchResults'])->name('participant-search-results');
            Route::get('sample/{sample}/searchResults', [SearchResultsController::class, 'sampleSearchResults'])->name('sample-search-results');
            Route::get('testRpt/{testResult}/searchResults', [SearchResultsController::class, 'testReportSearchResults'])->name('report-search-results');
            Route::get('combinedSamplesTestRpt/{sampleIds?}', [SearchResultsController::class, 'combinedSampleTestReport'])->name('combined-sample-test-report');
            Route::get('combinedTestResultsRpt/{resultIds?}', [SearchResultsController::class, 'combinedTestResultsReport'])->name('combined-test-results-report');
            Route::get('comboTestResultsRpt/{resultIds?}', [SearchResultsController::class, 'comboReport'])->name('combo-report');
        });


    });

    Route::group(['prefix' => 'report'], function () {
        Route::get('testCountReport', TestCountReportComponent::class)->middleware('permission:view-participant-info')->name('tests-count-report');
        Route::get('testStudyCountReport', TestStudyCountReportComponent::class)->middleware('permission:view-participant-info')->name('tests-study-count-report');

        Route::get('tat', TatReportComponent::class)->name('tests-tat-report');
        Route::get('test/tat', ResultTatReportComponent::class)->name('result-tat-report');
        Route::get('tests/done', TestsPerLabReportComponent::class)->name('result-tat-done-report');
    });

    Route::group(['prefix' => 'samplestg'], function () {
        Route::get('freezerLocations', FreezerLocationComponent::class)->name('freezer-location');
        Route::get('freezers', FreezersComponent::class)->name('freezers');
    });

    Route::group(['prefix' => 'Dashboard'], function () {
        Route::get('/', MainDashboardComponent::class)->name('manager-dashboard');
        Route::get('master', MasterDashboardComponent::class)->name('master-dashboard');
        Route::get('user', UserDashboardComponent::class)->name('user-dashboard');
    });
});

require __DIR__ . '/auth.php';
