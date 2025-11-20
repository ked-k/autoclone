<?php
namespace App\Http\Livewire\Lab\Reports;

use App\Models\TestResult;
use Carbon\Carbon;
use Livewire\Component;

class ResultTatReportComponent extends Component
{
    public $timeframe = 'monthly'; // daily, weekly, monthly, quarterly, yearly
    public $startDate;
    public $endDate;
    public $groupedBy   = 'test'; // test, sample, study, user
    public $showDetails = true;

    public function mount()
    {
        // $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->startDate = now()->subYear()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
    }

    protected function getAverageTAT($results, $stage)
    {
        $validTimes = array_filter(array_column(array_column($results, 'tat_details'), $stage));
        return count($validTimes) > 0 ? round(array_sum($validTimes) / count($validTimes), 1) : 0;
    }

    public function calculateDetailedTAT($result)
    {
        $sample = $result->sample;

        $receptionDate   = $sample->sampleReception?->created_at ? Carbon::parse($sample->sampleReception?->created_at) : null;
        $acknowledgeDate = $sample->date_acknowledged ? Carbon::parse($sample->date_acknowledged) : null;
        $resultDate      = $result->created_at ? Carbon::parse($result->created_at) : null;
        $reviewDate      = $result->reviewed_at ? Carbon::parse($result->reviewed_at) : null;
        $approvalDate    = $result->approved_at ? Carbon::parse($result->approved_at) : null;

        return [
            'reception_to_acknowledge' => $acknowledgeDate && $receptionDate
            ? $acknowledgeDate->diffInHours($receptionDate)
            : null,

            'acknowledge_to_result'    => $resultDate && $acknowledgeDate
            ? $resultDate->diffInHours($acknowledgeDate)
            : null,

            'result_to_review'         => $reviewDate && $resultDate
            ? $reviewDate->diffInHours($resultDate)
            : null,

            'review_to_approval'       => $approvalDate && $reviewDate
            ? $approvalDate->diffInHours($reviewDate)
            : null,

            'total_tat'                => $approvalDate && $receptionDate
            ? $approvalDate->diffInHours($receptionDate)
            : null,
        ];
    }

    public function render()
    {
        $results = TestResult::with([
            'sample',
            'sample.sampleReception',
            'test',
            'sample.study',
            'performer',
            'reviewer',
            'approver',
        ])
            ->where('status', 'Approved')
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->whereNotNull('approved_at')
            ->whereBetween('approved_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])->get();

        $data['resultTat'] = $results->map(function ($result) {
            return [
                'test_name'       => $result->test->name,
                'sample_identity' => $result->sample->sample_identity,
                'lab_no'          => $result->sample->lab_no,
                'study'           => $result->sample->study?->name,
                'performer'       => $result->performer?->name,
                'reviewer'        => $result->reviewer?->name,
                'approver'        => $result->approver?->name,
                'tat_details'     => $this->calculateDetailedTAT($result),
            ];
        });
        return view('livewire.lab.reports.result-tat-report-component', $data);
    }
}
