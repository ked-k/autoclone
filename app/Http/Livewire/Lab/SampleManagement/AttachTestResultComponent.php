<?php
namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\Admin\Test;
use App\Models\Kit;
use App\Models\Lab\SampleManagent\SampleReferral;
use App\Models\Sample;
use App\Models\TestAssignment;
use App\Models\TestResult;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Component;
use Livewire\WithFileUploads;

class AttachTestResultComponent extends Component
{
    use WithFileUploads;

    public $requestedTests;

    public $tests_performed = [];

    public $sample;

    public $sample_id;

    public $test_id;

    public $result;

    public $link;

    public $attachment;

    public $attachmentPath;

    public $performed_by;

    public $comment;

    public $testParameters = [];

    public $status;

    public $sample_identity;

    public $lab_no;

    public $kit_expiry_date;

    public $verified_lot;

    public $kit_id;

    public $activeTest;
    public $today;
    public $tat_comment;
    public $enterOnlyAssigned;
    public $referred_tests;
    public $active_referral;
    public function mount($id)
    {
        $today                 = Carbon::now();
        $sample                = Sample::with('participant')->findOrFail($id);
        $this->referred_tests  = SampleReferral::with('referralable')->where('sample_id', $id)->whereIn('test_id', $sample->referred_tests)->get();
        $this->sample          = $sample;
        $this->sample_id       = $sample->id;
        $this->sample_identity = $sample->sample_identity;
        $this->lab_no          = $sample->lab_no;
        $testsPendingResults   = array_diff($sample->tests_requested, $sample->tests_performed ?? []);
        // $testsPendingResults   = array_diff($sample->tests_requested, $sample->tests_performed ?? []);

        if (auth()->user()->hasPermission('enter-unassigned-results')) {
            $this->enterOnlyAssigned = false;
        } else {
            $this->enterOnlyAssigned = true;
        }
        if (count($testsPendingResults) > 0) {
            $this->requestedTests = Test::whereIn('id', (array) $testsPendingResults)
                ->when($this->enterOnlyAssigned, function ($query) {
                    $query->whereHas('testAssignment', function (Builder $query) {
                        $query->where(['assignee' => auth()->user()->id, 'sample_id' => $this->sample_id, 'status' => 'Assigned']);
                    });
                })
                ->orderBy('name', 'asc')->get();
            //Set the first test in the collection as active and load its parameters if present
            $this->test_id    = $this->requestedTests[0]->id ?? null;
            $this->activeTest = $this->requestedTests->where('id', $this->test_id)->first();
            if (! $this->activeTest) {
                return redirect()->route('test-request')->with('error', 'No tests available for this sample or you do not have permission to enter results for unassigned tests.');
            } elseif ($this->activeTest && count($this->referred_tests) > 0) {
                $this->active_referral = $this->referred_tests->where('sample_id', $this->sample_id)->where('test_id', $this->test_id)->first();
                // dd($this->active_referral);
            }
            if ($this->activeTest->result_type == 'Multiple' && $this->activeTest?->sub_tests) {
                foreach ($this->activeTest->sub_tests as $testName) {
                    $this->testResults[] = [
                        'test'    => $testName,
                        'result'  => '', // Initially empty
                        'comment' => '', // Initially empty
                    ];
                }
                // dd( $this->testResults);
            }
            if ($this->activeTest && $this->activeTest->parameters != null) {
                foreach ($this->activeTest->parameters as $key => $parameter) {
                    $this->testParameters[$parameter] = '';
                }
            }
        } else {
            $this->requestedTests = collect([]);
            $this->testParameters = [];
            $this->testParameters = [];
            $this->reset('test_id');
        }

        $this->tests_performed = (array) $sample->tests_performed;
        $this->performed_by    = auth()->user()->id;
    }

    public $testResults = [];
    public function storeTestResults()
    {
        $this->validate([
            'performed_by' => 'required|integer',
        ]);

        if ($this->result == null && $this->link == null && $this->attachment == null && $this->testResults == null) {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error', 'message' => 'Please enter/Attach results!']);
        } else {

            if ($this->attachment != null) {
                $this->validate([
                    'attachment' => 'mimes:pdf,xls,xlsx,csv,doc,docx|max:5000',
                ]);
                $attachmentName       = date('YmdHis') . '.' . $this->attachment->extension();
                $this->attachmentPath = $this->attachment->storeAs('attachmentResults', $attachmentName);
            } else {
                if ($this->activeTest->result_type == 'File') {
                    $this->validate([
                        'attachment' => ['required'],
                    ]);
                } else {
                    $this->attachmentPath = null;
                }
            }

            $this->testParameters = array_filter($this->testParameters, function ($value) {
                return $value != '';
            });

            if ($this->activeTest->parameters != null) {
                if (count($this->testParameters) == count($this->activeTest->parameters)) {
                    // dd('no parameters');
                    $this->saveResults();

                } else {
                    $this->dispatchBrowserEvent('not-found', ['type' => 'error', 'message' => 'Please include parameter values for this result!']);
                    $this->validate([
                        'testParameters' => ['required'],
                    ]);
                }
            } else {
                $this->saveResults();
            }
        }
    }
    public $myTest;
    public function saveResults()
    {
        try {
            DB::transaction(function () {

                $testResult = new TestResult();

                $testResult->sample_id = $this->sample_id;
                $testResult->test_id   = $this->test_id;
                if ($this->link != null) {
                    $testResult->result = $this->link;
                } else {
                    $test = Test::findOrfail($this->test_id);
                    if ($test->result_type == 'Measurable') {
                        $testResult->result = $this->result . '' . $test->measurable_result_uom;
                    }
                    if ($test->result_type == 'Multiple') {
                        $testResult->result = json_encode($this->testResults);

                    } else {
                        $testResult->result = $this->result;
                    }
                }

                $testResult->attachment      = $this->attachmentPath;
                $testResult->performed_by    = $this->performed_by;
                $testResult->comment         = $this->comment;
                $testResult->parameters      = count($this->testParameters) ? $this->testParameters : null;
                $testResult->kit_id          = $this->kit_id;
                $testResult->verified_lot    = $this->verified_lot;
                $testResult->tat_comment     = $this->tat_comment;
                $testResult->kit_expiry_date = $this->kit_expiry_date;
                $testResult->status          = 'Pending Review';
                $testResult->save();
                array_push($this->tests_performed, "{$testResult->test_id}");
                $testAssignment = TestAssignment::where(['sample_id' => $this->sample_id, 'test_id' => $this->test_id])
                    ->when($this->enterOnlyAssigned, function ($query) {
                        $query->where('assignee', auth()->user()->id);
                    })->first();
                $this->sample->update(['tests_performed' => $this->tests_performed]);
                $testAssignment->update(['status' => 'Test Done']);

                if (count(array_diff($this->sample->tests_requested, $this->sample->tests_performed)) == 0) {
                    $this->sample->update(['status' => 'Tests Done']);
                    redirect()->route('test-request');
                }

                if ($this->enterOnlyAssigned && TestAssignment::where(['sample_id' => $this->sample_id, 'assignee' => auth()->user()->id, 'status' => 'Assigned'])->count() == 0) {
                    redirect()->route('test-request');
                }

            });

            $this->requestedTests = $this->requestedTests->where('id', '!=', $this->test_id)->values();
            $this->test_id        = $this->requestedTests[0]->id ?? null;
            $this->testParameters = [];

            //Set the first test in the collection as active and load its parameters if present
            $this->activeTest = $this->requestedTests->where('id', $this->test_id)->first();
            if ($this->activeTest && $this->activeTest->parameters != null) {
                foreach ($this->activeTest->parameters as $key => $parameter) {
                    $this->testParameters[$parameter] = '';
                }
            }

            $details = [
                'subject'    => 'Auto-Lab Test',
                'greeting'   => 'Hello, I hope this email finds you well',
                'body'       => 'You have a pending test Lab No#' . $this->sample?->lab_no . ' to review, Please log in and take the necessary actions.',
                'actiontext' => 'Click Here for more details',
                'actionurl'  => URL::signedRoute('test-request'),
                'user_id'    => $this->sample->laboratory->test_reviewer ?? 1,
            ];
            // try {
            //     $email = SendGeneralNotificationJob::dispatch($details);
            // } catch (\Throwable $th) {
            // }
            $this->resetResultInputs();
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test Results Recorded successfully!']);

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error', 'message' => 'An error occurred while saving the test results: ' . $e->getMessage()]);
        }
    }

    public function activateResultInput($id)
    {
        $this->resetResultInputs();
        $this->activeTest = $this->requestedTests->where('id', $id)->first();
        $this->test_id    = $id;
        if ($this->activeTest->result_type == 'Multiple' && $this->activeTest->sub_tests) {
            foreach ($this->activeTest->sub_tests as $testName) {
                $this->testResults[] = [
                    'test'    => $testName,
                    'result'  => '', // Initially empty
                    'result'  => '', // Initially empty
                    'CtValue' => '', // Initially empty
                    'comment' => '', // Initially empty
                ];
            }
            // dd( $this->testResults);
        }
    }

    public function resetResultInputs()
    {
        $this->reset(['result', 'link', 'attachment', 'comment', 'attachmentPath', 'kit_id', 'verified_lot', 'kit_expiry_date']);
    }

    public function close()
    {
        $this->resetResultInputs();
    }

    public function render()
    {
        $data['users']          = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->get();
        $data['testsRequested'] = $this->requestedTests ?? collect();
        $data['kits']           = Kit::where('is_active', 1)->get();

        return view('livewire.lab.sample-management.attach-test-result-component', $data);
    }
}
