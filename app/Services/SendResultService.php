<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\TestResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendResultService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Send a structured test result to the external system.
     */
    public function sendToExternalSystem(int $id)
    {
        $testResult = TestResult::with([
            'test',
            'sample',
            'kit',
            'sample.participant',
            'sample.sampleReception',
            'sample.sampleType',
            'sample.study',
            'sample.requester',
            'sample.collector'
        ])->find($id);

        if (!$testResult) {
            return response()->json([
                'success' => false,
                'message' => 'Test result not found'
            ], 404);
        }

        $payload = $this->buildStructuredPayload($testResult);
        // dd($payload);
        return $this->sendToAPI(
            $payload,
            optional($testResult->sample)->sample_identity
        );
    }

    /**
     * Build structured payload for external system.
     */
    private function buildStructuredPayload(TestResult $testResult): array
    {
        $sample = optional($testResult->sample);
        $participant = optional($sample->participant);
        $collector = optional($sample->collector);
        $test = optional($testResult->test);
        $kit = optional($testResult->kit);

        return [
            'message_id'   => uniqid('lims_'),
            'timestamp'    => now()->toIso8601String(),
            'message_type' => 'laboratory_result',
            'version'      => '1.0',

            'laboratory_info' => [
                'lab_id'           => auth()->user()->institution_id ?? null,
                'lab_name'         => env('INSTITUTION_NAME'),
                'reporting_system' => 'LIMS',
            ],

            'patient' => [
                'patient_id'          => $participant->identity,
                'internal_patient_id' => $participant->id,
                'patient_number'      => $participant->participant_no,
                'demographics' => [
                    'age'          => $participant->age,
                    'gender'       => $participant->gender,
                    'date_of_birth'=> $participant->dob,
                    'contact'      => $participant->contact,
                    'address'      => $participant->address,
                ],
            ],

            'order_info' => [
                'order_id'   => $sample->sample_no,
                'lab_number' => $sample->lab_no,
                'sample_id'  => $sample->sample_identity,
                'order_date' => $sample->date_requested,
                'priority'   => $sample->priority,
            ],

            'sample_info' => [
                'sample_id'      => $sample->sample_identity,
                'sample_type'    => optional($sample->sampleType)->type,
                'collection_date'=> $sample->date_collected,
                'collector'      => $collector->name,
                'volume'         => $sample->volume,
                'batch_number'   => optional($sample->sampleReception)->batch_no,
            ],

            'test_results' => [
                [
                    'test_id'          => $test->id,
                    'test_name'        => $test->name,
                    'test_code'        => $test->short_code,
                    'result'           => $testResult->result,
                    'result_type'      => $test->result_type,
                    'reference_range'  => [
                        'min' => $test->reference_range_min,
                        'max' => $test->reference_range_max,
                    ],
                    'units'          => $test->measurable_result_uom,
                    'status'         => $testResult->status,
                    'performed_by'   => $testResult->performer->name??null,
                    'performed_date' => optional($testResult->created_at)->toIso8601String(),
                    'verified_date'  => optional($testResult->approved_at)->toIso8601String(),
                    'verified_by'    => $testResult->approved_by,
                ],
            ],

            'kit_info' => [
                'kit_id'      => $kit->id,
                'kit_name'    => $kit->name,
                'lot_number'  => $testResult->verified_lot,
                'expiry_date' => $testResult->kit_expiry_date,
            ],

            'approval_info' => [
                'reviewer_comment' => $testResult->reviewer_comment,
                'reviewed_by' => $testResult->reviewer->name??null,
                'approver_comment' => $testResult->approver_comment,
                'approver_by' => $testResult->approver->name??null,
                'reviewed_at'      => optional($testResult->reviewed_at)->toIso8601String(),
                'approved_at'      => optional($testResult->approved_at)->toIso8601String(),
            ],

            'metadata' => [
                'tracking_number' => $testResult->tracker,
                'download_count'  => $testResult->download_count,
                'amended'         => (bool) $testResult->amended_state,
            ],

            'result_date' => optional($testResult->approved_at)->format('Y-m-d H:i:s')
                ?? now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Send payload to external system.
     */
    private function sendToAPI(array $payload, ?string $sampleId)
    {
        $baseUrl = rtrim(env('CENTRAL_INSTANCE_URL', 'https://nimsdev.africacdc.org'), '/');
        $url = "https://nimsdev.africacdc.org/api/v1/SampleReferralCrossBorder/referral/sample/{$sampleId}/add/structured-results";

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type'           => 'application/json',
                    'X-Institution-API-Key'  => env('INSTITUTION_API_KEY')??'qrokk2a5tZIoq9AOvc8LbTA9da886ApY9fZtE9uJfBzbLEdTNO7Qo7dluy47Hfau',
                    'Accept'                 => 'application/json',
                ],
                'json'    => $payload,
                'timeout' => 30,
            ]);

            $responseBody = json_decode($response->getBody(), true);

            Log::info('Result sent to external system', [
                'sample_identifier' => $sampleId,
                'message_id'        => $payload['message_id'],
                'status_code'       => $response->getStatusCode(),
                'external_response' => $responseBody,
            ]);

            $this->markResultAsSent(
                $payload['test_results'][0]['test_id'],
                $payload['message_id']
            );

            return response()->json([
                'success'            => true,
                'message'            => 'Result sent successfully',
                'external_system_id' => $responseBody['data']['id'] ?? null,
                'message_id'         => $payload['message_id'],
            ]);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorResponse = $e->getResponse()
                ? json_decode($e->getResponse()->getBody(), true)
                : null;

            Log::error('Failed to send result to external system', [
                'sample_identifier' => $sampleId,
                'message_id'        => $payload['message_id'],
                'error'             => $e->getMessage(),
                'response_status'   => optional($e->getResponse())->getStatusCode(),
                'error_details'     => $errorResponse,
            ]);

            return response()->json([
                'success'       => false,
                'message'       => 'Failed to send result to external system',
                'error'         => $e->getMessage(),
                'error_details' => $errorResponse,
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error sending result', [
                'sample_identifier' => $sampleId,
                'message_id'        => $payload['message_id'],
                'error'             => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark local record as sent.
     */
    private function markResultAsSent(?int $testResultId, string $messageId): void
    {
        if (!$testResultId) {
            Log::warning('Missing testResultId – cannot mark as sent.', [
                'message_id' => $messageId
            ]);
            return;
        }

        try {
            // DB::statement("
            // ALTER TABLE `test_results` ADD `external_system_sent` INT NULL AFTER `received_by`, ADD `external_message_id` VARCHAR(40) NULL AFTER `external_system_sent`, ADD `sent_to_external_at` DATETIME NULL AFTER `external_message_id`;");
            TestResult::where('id', $testResultId)
                ->update([
                    'external_system_sent' => true,
                    'external_message_id'  => $messageId,
                    'sent_to_external_at'  => now(),
                ]);

        } catch (\Exception $e) {
            Log::warning('Failed to mark result as sent locally', [
                'test_result_id' => $testResultId,
                'message_id'     => $messageId,
                'error'          => $e->getMessage(),
            ]);
        }
    }



    public function sendFHIRFormat($id)
    {
        $testResult = TestResult::with(['test', 'sample', 'kit', 'sample.participant', 'sample.sampleReception', 'sample.sampleType'])->where('id', $id)->first();

        $fhirPayload = [
            'resourceType' => 'DiagnosticReport',
            'id' => 'dr-'.$testResult->id,
            'status' => $this->mapStatusToFHIR($testResult->status),
            'category' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/v2-0074',
                        'code' => 'LAB',
                        'display' => 'Laboratory',
                    ],
                ],
            ],
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => $testResult->test->short_code ?: 'generic-test',
                        'display' => $testResult->test->name,
                    ],
                ],
            ],
            'subject' => [
                'reference' => 'Patient/'.$testResult->sample->participant->identity,
                'display' => $testResult->sample->participant->participant_no,
            ],
            'effectiveDateTime' => $testResult->sample->date_collected,
            'issued' => $testResult->approved_at,
            'performer' => [
                [
                    'reference' => 'Organization/'.config('app.lab_id'),
                    'display' => config('app.lab_name'),
                ],
            ],
            'result' => [
                [
                    'reference' => 'Observation/obs-'.$testResult->id,
                    'display' => $testResult->test->name,
                ],
            ],
            'conclusion' => $testResult->result,
        ];

        return $this->sendToAPI($fhirPayload);
    }

    public function sendSimpleFormat($id)
    {
        $testResult = TestResult::with(['test', 'sample', 'kit', 'sample.participant', 'sample.sampleReception', 'sample.sampleType'])->where('id', $id)->first();

        $simplePayload = [
            'report_id' => $testResult->id,
            'report_date' => $testResult->approved_at,
            'patient' => [
                'patient_id' => $testResult->sample->participant->identity,
                'patient_number' => $testResult->sample->participant->participant_no,
                'age' => $testResult->sample->participant->age,
                'gender' => $testResult->sample->participant->gender,
            ],
            'sample' => [
                'sample_id' => $testResult->sample->sample_identity,
                'sample_number' => $testResult->sample->sample_no,
                'lab_number' => $testResult->sample->lab_no,
                'sample_type' => $testResult->sample->sampleType->type,
                'collection_date' => $testResult->sample->date_collected,
            ],
            'test' => [
                'test_name' => $testResult->test->name,
                'test_code' => $testResult->test->short_code,
                'result' => $testResult->result,
                'reference_range' => $testResult->test->reference_range_min.' - '.$testResult->test->reference_range_max,
                'units' => $testResult->test->measurable_result_uom,
            ],
            'laboratory' => [
                'lab_name' => config('app.lab_name'),
                'approved_by' => $testResult->approved_by,
                'approved_date' => $testResult->approved_at,
            ],
        ];

        return $this->sendToAPI($simplePayload);
    }
}
