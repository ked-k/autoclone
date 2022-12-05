<?php

use App\Helpers\LoginActivity;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\UserPermissionsController;
use App\Http\Controllers\Auth\UserRolesAssignmentController;
use App\Http\Controllers\Auth\UserRolesController;
use App\Http\Controllers\FacilityInformationController;
use App\Http\Controllers\ResultReportController;
use App\Http\Controllers\SearchResultsController;
use App\Http\Livewire\Admin\CollectorComponent;
use App\Http\Livewire\Admin\CourierComponent;
use App\Http\Livewire\Admin\Dashboards\MainDashboardComponent;
use App\Http\Livewire\Admin\Dashboards\MasterDashboardComponent;
use App\Http\Livewire\Admin\Dashboards\UserDashboardComponent;
use App\Http\Livewire\Admin\DesignationComponent;
use App\Http\Livewire\Admin\FacilityComponent;
use App\Http\Livewire\Admin\KitComponent;
use App\Http\Livewire\Admin\LaboratoryComponent;
use App\Http\Livewire\Admin\PlatformComponent;
use App\Http\Livewire\Admin\RequesterComponent;
use App\Http\Livewire\Admin\SampleTypeComponent;
use App\Http\Livewire\Admin\StudyComponent;
use App\Http\Livewire\Admin\TestCategoryComponent;
use App\Http\Livewire\Admin\TestComponent;
use App\Http\Livewire\Admin\UserActivityComponent;
use App\Http\Livewire\Admin\UserComponent;
use App\Http\Livewire\Admin\UserProfileComponent;
use App\Http\Livewire\Lab\Lists\ParticipantListComponent;
use App\Http\Livewire\Lab\Lists\SamplesListComponent;
use App\Http\Livewire\Lab\Lists\TestsPerformedListComponent;
use App\Http\Livewire\Lab\SampleManagement\AssignTestsComponent;
use App\Http\Livewire\Lab\SampleManagement\AttachTestResultComponent;
use App\Http\Livewire\Lab\SampleManagement\SampleReceptionComponent;
use App\Http\Livewire\Lab\SampleManagement\SpecimenRequestComponent;
use App\Http\Livewire\Lab\SampleManagement\TestApprovalComponent;
use App\Http\Livewire\Lab\SampleManagement\TestReportsComponent;
use App\Http\Livewire\Lab\SampleManagement\TestRequestComponent;
use App\Http\Livewire\Lab\SampleManagement\TestReviewComponent;
use Illuminate\Support\Facades\Route;

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
Route::get('generatelabno', [AuthenticatedSessionController::class, 'generate']);
Route::get('user/account', UserProfileComponent::class)->name('user.account')->middleware('auth');
Route::group(['middleware' => ['auth', 'password_expired', 'suspended_user']], function () {
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
        Route::get('batch/{batch}/specimen-req', SpecimenRequestComponent::class)->middleware('permission:accession-samples')->name('specimen-request');
        Route::get('testAssignment', AssignTestsComponent::class)->middleware('permission:assign-test-requests')->name('test-request-assignment');
        Route::get('testRequests', TestRequestComponent::class)->middleware('permission:acknowledge-test-request')->name('test-request');
        Route::get('sample/{id}/testResults', AttachTestResultComponent::class)->middleware('permission:enter-results')->name('attach-test-results');
        Route::get('resultReview', TestReviewComponent::class)->middleware('permission:review-results')->name('test-review');
        Route::get('resultApproval', TestApprovalComponent::class)->middleware('permission:approve-results')->name('test-approval');
        Route::get('resultReports', TestReportsComponent::class)->middleware('permission:view-result-reports')->name('test-reports');
        Route::get('result/{id}/report', [ResultReportController::class, 'show'])->name('result-report');
        Route::get('result/{id}/attachment', [ResultReportController::class, 'download'])->name('attachment.download');
        Route::get('participants', ParticipantListComponent::class)->middleware('permission:view-participant-info')->name('participants');

        Route::get('samplesList', SamplesListComponent::class)->middleware('permission:view-participant-info')->name('samples-list');
        Route::get('testsPerformedList', TestsPerformedListComponent::class)->middleware('permission:view-participant-info')->name('tests-performed-list');

        Route::group(['middleware' => 'signed'], function () {
            Route::get('batch/{sampleReception}/searchResults', [SearchResultsController::class, 'batchSearchResults'])->name('batch-search-results');
            Route::get('participant/{participant}/searchResults', [SearchResultsController::class, 'participantSearchResults'])->name('participant-search-results');
            Route::get('sample/{sample}/searchResults', [SearchResultsController::class, 'sampleSearchResults'])->name('sample-search-results');
            Route::get('testRpt/{testResult}/searchResults', [SearchResultsController::class, 'testReportSearchResults'])->name('report-search-results');
            Route::get('combinedRpt/{sampleIds?}', [SearchResultsController::class, 'combinedTestReport'])->name('combined-test-report');
        });
    });

    Route::group(['prefix' => 'Dashboard'], function () {
        Route::get('/', MainDashboardComponent::class)->name('manager-dashboard');
        Route::get('master', MasterDashboardComponent::class)->name('master-dashboard');
        Route::get('user', UserDashboardComponent::class)->name('user-dashboard');
    });
});

require __DIR__.'/auth.php';
