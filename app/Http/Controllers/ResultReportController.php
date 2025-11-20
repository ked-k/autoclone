<?php
namespace App\Http\Controllers;

use App\Models\Lab\SampleManagement\TestResultAmendment;
use App\Models\TestResult;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Response;
use PDF;

class ResultReportController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TestResults  $testResults
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $testResult = TestResult::with(['test', 'sample', 'kit', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->where('id', $id)->first();
        // dd($testResult);
        // return View('reports.sample-management.downloadReport', compact('testResult'));
        $pdf = PDF::loadView('reports.sample-management.downloadReport', compact('testResult'));
        $pdf->setPaper('a4', 'portrait'); //horizontal
        $pdf->getDOMPdf()->set_option('isPhpEnabled', true);

        return $pdf->stream($testResult->sample->sample_identity . '.pdf');

        // return $pdf->download($testResult->sample->participant->identity.rand().'.pdf');
    }

    public function print($id)
    {
        $testResult = TestResult::with(['test', 'sample', 'kit', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->where('id', $id)->first();
            // return response()->json([
            //     'success' => true,
            //     'data' => $testResult,
            //     'message' => 'Referral requests fetched successfully',
            // ]);
        //return View('reports.sample-management.downloadReport', compact('testResult'));
        return View('reports.sample-management.print-report', compact('testResult'));

    }

    public function printResults()
    {
        $type  = request('search_type'); // E.g., 'study'
        $input = request('identifiers'); // Could be "5" or "ABC-123, XYZ-456"

        // Normalize input to array
        $identifiers = is_array($input)
        ? $input
        : array_map('trim', explode(',', $input));

        $results = TestResult::getResultsForPrinting($type, $identifiers);
        // dd($results);
        return view('reports.sample-management.print-multiple-report', compact('results'));
    }

    public function printMultiplen($ids)
    {
        $testResults = TestResult::with(['test', 'sample', 'kit', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->whereIn('id', $ids)->first();
        //return View('reports.sample-management.downloadReport', compact('testResult'));
        return View('reports.sample-management.print-multiple-report', compact('testResults'));

    }

    public function printMultiple($ids)
    {
        // Convert the comma-separated string of IDs into an array
        $idsArray = explode(',', $ids);
        // Retrieve the IDs from the session
        $combinedResultsList = session('combinedResultsList');
        // Query the TestResults based on the IDs
        $testResults = TestResult::with([
            'test',
            'sample',
            'kit',
            'sample.participant',
            'sample.sampleReception',
            'sample.sampleType:id,type',
            'sample.study:id,name',
            'sample.requester',
            'sample.collector:id,name',
        ])
            ->whereIn('id', $combinedResultsList) // Use the exploded array of IDs
            ->get();                              // You should use `get()` instead of `first()` since you're fetching multiple results

        // Return the view with the test results
        return view('reports.sample-management.print-multiple-report', compact('testResults'));
    }

    public function viewOriginallyAmendedResult($id)
    {
        // dd($id);
        $testResult = json_decode(TestResultAmendment::where('test_result_id', $id)->first()->original_results ?? null);
        // $testResult?->original_results ?? [];
        //return View('reports.sample-management.downloadReport', compact('testResult'));
        return View('reports.sample-management.print-original-report', compact('testResult'));

    }

    public function download($id)
    {
        $result = TestResult::findOrFail($id);
        $file   = storage_path('app/') . $result->attachment;

        if (file_exists($file)) {
            return Response::download($file);
        } else {
            echo 'File not found.';
        }
    }

    public function getCrsPatient()
    {
        $endpoint   = "http://crs.brc.online/api/get-patient/";
        $client     = new Client();
        $patient_no = 'BRC-10118P';
        $token      = "ABC";

        $response = $client->request('GET', $endpoint, ['query' => [
            'pat_no' => $patient_no,
            // 'key2' => $value,
        ]]);

        $participant = json_decode($response->getBody(), true);

        foreach ($participant as $value) {
            return $value['given_name'];
        }

    }
}
