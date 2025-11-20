<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Sample;
use App\Models\SampleReception;
use App\Models\TestResult;

class SearchResultsController extends Controller
{
    public function batchSearchResults(SampleReception $sampleReception)
    {
        $sampleReception->load(['facility', 'courier', 'receiver', 'reviewer', 'sample', 'sample.participant',
            'sample.sampleType', 'sample.requester', 'sample.collector', 'sample.study', 'sample.testResult', ]);

        return view('reports.sample-management.batch-details', compact('sampleReception'));
    }

    public function sampleSearchResults($sampleId)
    {
        $sample = Sample::where('id',$sampleId)->orWhere('sample_identity',$sampleId)->firstOrFail();
        if ($sample->sample_is_for == 'Testing') {
            $sample->load(['sampleReception', 'sampleReception.facility', 'sampleReception.courier', 'sampleReception.receiver', 'participant',
                'sampleType', 'requester', 'collector', 'study', 'testResult', 'testResult.test', ]);
        } elseif ($sample->sample_is_for == 'Aliquoting') {
            $sample->load(['sampleReception', 'sampleReception.facility', 'sampleReception.courier', 'sampleReception.receiver', 'participant',
                'aliquots', 'aliquots.aliquotType', 'requester', 'collector', 'study', 'aliquotingAssignment', 'aliquotingAssignment.performer', ]);
        }

        return view('reports.sample-management.sample-details', compact('sample'));
    }

    public function participantSearchResults(Participant $participant)
    {
        $participant->load(['facility', 'study', 'sample', 'sample.sampleReception', 'sample.sampleReception.courier', 'sample.sampleReception.receiver', 'sample.sampleType', 'sample.requester', 'sample.collector', 'sample.study', 'sample.testResult', 'sample.testResult.test'])->loadCount(['sample', 'testResult']);

        return view('reports.sample-management.participant-details', compact('participant'));
    }

    public function testReportSearchResults(TestResult $testResult)
    {
        $testResult->load(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name']);

        return view('reports.sample-management.test-report', compact('testResult'));
    }

    public function combinedSampleTestReport($sampleIds)
    {
        $samples = Sample::with(['sampleReception', 'sampleReception.facility', 'sampleReception.courier', 'sampleReception.receiver', 'participant',
            'sampleType', 'requester', 'collector', 'study', 'testResult', 'testResult.test', ])->whereIn('id', strpos($sampleIds, '-') ? explode('-', $sampleIds) : [$sampleIds])->get();
        $qrCodeContent = implode('|', $samples->pluck('sample_identity')->toArray());

        return view('reports.sample-management.combined-test-report', compact('samples', 'qrCodeContent'));
    }

    public function combinedTestResultsReport($resultIds)
    {
        $samples = Sample::whereHas('testResult', function ($query) use ($resultIds) {
            $query->whereIn('id', array_unique(explode('-', $resultIds)));
        })->with(['sampleReception', 'sampleReception.facility', 'sampleReception.courier', 'sampleReception.receiver', 'participant',
            'sampleType', 'requester', 'collector', 'study', 'testResult' => function ($query) use ($resultIds) {
                $query->whereIn('id', strpos($resultIds, '-') ? explode('-', $resultIds) : [$resultIds]);
            }, 'testResult.test', ])->get();

        $qrCodeContent = implode('|', $samples->pluck('sample_identity')->toArray());

        return view('reports.sample-management.combined-test-report', compact('samples', 'qrCodeContent'));
    }

    public function comboReport($resultIds)
    {
        $testResults=TestResult::whereIn('id',array_unique(explode('-', $resultIds)))->with(['kit','performer','reviewer','approver','test', 'sample', 'sample.participant', 'sample.sampleReception',  'sample.participant.facility:id,name','sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester'])->get();
        $qrCodeContent = implode('|', $testResults->pluck('tracker')->toArray());

        return view('reports.sample-management.combo-report', compact('testResults', 'qrCodeContent'));
    }
}
