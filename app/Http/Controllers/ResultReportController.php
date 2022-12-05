<?php

namespace App\Http\Controllers;

use App\Models\TestResult;
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
        $testResult = TestResult::with(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->where('id', $id)->first();
        $pdf = PDF::loadView('reports.sample-management.downloadReport', compact('testResult'));
        $pdf->setPaper('a4', 'portrait');   //horizontal

        return  $pdf->stream($testResult->sample->participant->identity.rand().'.pdf');
        // $pdf->getDOMPdf()->set_option('isPhpEnabled', true);

        // return $pdf->download($testResult->sample->participant->identity.rand().'.pdf');
    }

    public function download($id)
    {
        $result = TestResult::findOrFail($id);
        $file = storage_path('app/').$result->attachment;

        if (file_exists($file)) {
            return Response::download($file);
        } else {
            echo 'File not found.';
        }
    }
}
