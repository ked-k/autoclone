<?php

namespace App\Services;

use Barryvdh\DomPDF\PDF;
use App\Models\TestResult;

class SendFileResultService
{
   public function sendToExternalSystem($id)
{
    $testResult = TestResult::with(['test', 'sample', 'kit', 'sample.participant', 'sample.sampleReception', 'sample.sampleType', 'sample.study', 'sample.requester', 'sample.collector'])->where('id', $id)->first();

    // Generate the structured data
    $resultData = $this->prepareResultData($testResult);

    // Create a PDF file from the result data
    $pdfPath = $this->generateResultPDF($resultData);

    // Send to external system
    return $this->sendResultWithFile($testResult, $pdfPath, $resultData);
}

private function prepareResultData($testResult)
{
    return [
        'message_id' => uniqid('lims_'),
        'timestamp' => now()->toISOString(),
        'laboratory_info' => [
            'lab_id' => config('app.lab_id'),
            'lab_name' => config('app.lab_name'),
            'reporting_system' => 'LIMS'
        ],

        'patient' => [
            'patient_id' => $testResult->sample->participant->identity,
            'patient_number' => $testResult->sample->participant->participant_no,
            'age' => $testResult->sample->participant->age,
            'gender' => $testResult->sample->participant->gender,
            'contact' => $testResult->sample->participant->contact,
            'address' => $testResult->sample->participant->address
        ],

        'sample' => [
            'sample_id' => $testResult->sample->sample_identity,
            'sample_number' => $testResult->sample->sample_no,
            'lab_number' => $testResult->sample->lab_no,
            'sample_type' => $testResult->sample->sampleType->type,
            'collection_date' => $testResult->sample->date_collected
        ],

        'test_results' => [
            [
                'test_name' => $testResult->test->name,
                'result' => $testResult->result,
                'reference_range' => $testResult->test->reference_range_min . ' - ' . $testResult->test->reference_range_max,
                'units' => $testResult->test->measurable_result_uom,
                'status' => $testResult->status,
                'performed_date' => $testResult->created_at,
                'verified_date' => $testResult->approved_at
            ]
        ],

        'approval_info' => [
            'reviewer_comment' => $testResult->reviewer_comment,
            'approver_comment' => $testResult->approver_comment,
        ]
    ];
}

private function generateResultPDF($resultData)
{
    $pdf = PDF::loadView('reports.result-pdf', compact('resultData'));

    $filename = 'result_' . $resultData['sample']['sample_number'] . '_' . time() . '.pdf';
    $filePath = storage_path('app/temp/' . $filename);

    // Ensure temp directory exists
    if (!file_exists(dirname($filePath))) {
        mkdir(dirname($filePath), 0755, true);
    }

    $pdf->save($filePath);

    return $filePath;
}

private function sendResultWithFile($testResult, $pdfPath, $resultData)
{
    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post(config('services.external_system.api_url') . '/api/samples/' . $testResult->sample->sample_identity . '/results', [
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.external_system.api_token'),
                'Accept' => 'application/json',
            ],
            'multipart' => [
                [
                    'name' => 'result_date',
                    'contents' => $testResult->approved_at ?? now()->format('Y-m-d')
                ],
                [
                    'name' => 'result_comment',
                    'contents' => $this->generateResultComment($resultData)
                ],
                [
                    'name' => 'result_file',
                    'contents' => fopen($pdfPath, 'r'),
                    'filename' => basename($pdfPath),
                    'headers' => [
                        'Content-Type' => 'application/pdf'
                    ]
                ]
            ],
            'timeout' => 30
        ]);

        // Clean up temporary file
        unlink($pdfPath);

        \Log::info('Result sent to external system', [
            'sample_identifier' => $testResult->sample->sample_identity,
            'status' => $response->getStatusCode()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Result sent successfully',
            'external_response' => json_decode($response->getBody())
        ]);

    } catch (\Exception $e) {
        // Clean up temporary file on error too
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        \Log::error('Failed to send result to external system', [
            'error' => $e->getMessage(),
            'sample_identifier' => $testResult->sample->sample_identity
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to send result: ' . $e->getMessage()
        ], 500);
    }
}

private function generateResultComment($resultData)
{
    $comment = "Laboratory Results Report\n";
    $comment .= "Patient: {$resultData['patient']['patient_number']} ({$resultData['patient']['age']}y, {$resultData['patient']['gender']})\n";
    $comment .= "Sample: {$resultData['sample']['sample_number']} - {$resultData['sample']['sample_type']}\n";
    $comment .= "Test: {$resultData['test_results'][0]['test_name']} - Result: {$resultData['test_results'][0]['result']}\n";
    $comment .= "Reference Range: {$resultData['test_results'][0]['reference_range']}\n";
    $comment .= "Verified on: {$resultData['test_results'][0]['verified_date']}";

    if (!empty($resultData['approval_info']['approver_comment'])) {
        $comment .= "\nApprover Note: {$resultData['approval_info']['approver_comment']}";
    }

    return $comment;
}
}
