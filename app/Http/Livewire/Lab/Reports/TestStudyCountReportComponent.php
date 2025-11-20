<?php
namespace App\Http\Livewire\Lab\Reports;

use App\Models\Admin\Test;
use App\Models\Sample;
use App\Models\Study;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Carbon\Carbon;
use Livewire\Component;

class TestStudyCountReportComponent extends Component
{
    public $startDate;
    public $endDate;
    public $selectedTests = [];
    public $selectedStudy;
    public $showZeroCounts = false;
    public $tests          = [];
    public $studies        = [];
    public $reportData     = [];
    public $quarterColumns = [];
    public $quarterTotals  = [];
    public $reportType     = 'study_count';
    public $chartType      = 'bar';
    public $chartTitle     = 'Tests Requested Per Quarter';

    // Properties for the chart
    protected $listeners = [
        'onColumnClick' => 'handleOnColumnClick',
    ];

    public function mount()
    {
        $this->tests     = Test::orderBy('name')->get();
        $this->studies   = Study::orderBy('name')->get();
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate   = now()->endOfYear()->format('Y-m-d');
        $this->generateReport();
    }

    public function generateReport()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate'   => 'required|date|after_or_equal:startDate',
        ]);

        $query = Sample::query()
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->where('sample_is_for', 'Testing')
            ->whereDate('date_collected', '>=', $this->startDate)
            ->whereDate('date_collected', '<=', $this->endDate)
            ->whereNotNull('tests_requested');

        if ($this->selectedStudy) {
            $query->where('study_id', $this->selectedStudy);
        }

        $samples = $query->get();

        // Initialize structures
        $this->quarterColumns = $this->getQuarterColumns();
        $this->quarterTotals  = array_fill_keys($this->quarterColumns, ['test_count' => 0, 'study_count' => []]);
        $report               = [];
        $testTotals           = [];

        // Initialize tests
        foreach ($this->tests as $test) {
            $report[$test->id] = [
                'name'         => $test->name,
                'quarters'     => array_fill_keys($this->quarterColumns, 0),
                'study_counts' => array_fill_keys($this->quarterColumns, [])
            ];
        }

        // Process samples
        foreach ($samples as $sample) {
            $testIds = $sample->tests_requested;
            $quarter = 'Q' . Carbon::parse($sample->date_collected)->quarter . ' ' . Carbon::parse($sample->date_collected)->year;
            $studyId = $sample->study_id;

            if (! in_array($quarter, $this->quarterColumns)) {
                continue;
            }

            foreach ($testIds as $testId) {
                if (! empty($this->selectedTests) && ! in_array($testId, $this->selectedTests)) {
                    continue;
                }

                if (isset($report[$testId])) {
                    // Update test counts
                    $report[$testId]['quarters'][$quarter]++;
                    $this->quarterTotals[$quarter]['test_count']++;

                    // Update study counts
                    if ($studyId !== null) {
                        if (! in_array($studyId, $report[$testId]['study_counts'][$quarter])) {
                            $report[$testId]['study_counts'][$quarter][] = $studyId;
                        }
                        if (! in_array($studyId, $this->quarterTotals[$quarter]['study_count'])) {
                            $this->quarterTotals[$quarter]['study_count'][] = $studyId;
                        }
                    }

                    // Update test totals
                    if (! isset($testTotals[$testId])) {
                        $testTotals[$testId] = ['test_count' => 0, 'studies' => []];
                    }
                    $testTotals[$testId]['test_count']++;
                    if ($studyId !== null && ! in_array($studyId, $testTotals[$testId]['studies'])) {
                        $testTotals[$testId]['studies'][] = $studyId;
                    }
                }
            }
        }

        // Prepare final report data
        $this->reportData = [];
        foreach ($report as $testId => $testData) {
            $testCount  = $testTotals[$testId]['test_count'] ?? 0;
            $studyCount = isset($testTotals[$testId]['studies']) ? count($testTotals[$testId]['studies']) : 0;

            if (! $this->showZeroCounts && $testCount === 0 && $studyCount === 0) {
                continue;
            }

            $this->reportData[] = [
                'test_id'           => $testId,
                'test_name'         => $testData['name'],
                'quarters'          => $testData['quarters'],
                'study_counts'      => array_map(function ($studies) {
                    return count($studies);
                }, $testData['study_counts']),
                'test_count_total'  => $testCount,
                'study_count_total' => $studyCount,
            ];
        }

        usort($this->reportData, fn($a, $b) => $a['test_name'] <=> $b['test_name']);
    }

    protected function getQuarterColumns()
    {
        $start    = Carbon::parse($this->startDate);
        $end      = Carbon::parse($this->endDate);
        $quarters = [];

        $current = $start->copy()->startOfQuarter();

        while ($current <= $end) {
            $quarter    = 'Q' . $current->quarter . ' ' . $current->year;
            $quarters[] = $quarter;
            $current->addQuarter();
        }

        return array_unique($quarters);
    }

    public function handleOnColumnClick($column)
    {
        // Optional: Handle chart column clicks
    }

    public function render()
    {
        // Initialize an empty chart model
        $columnChartModel = null;

        if (count($this->reportData) > 0) {
            // Create the appropriate chart model
            if ($this->chartType === 'pie') {
                $chartModel = LivewireCharts::pieChartModel();
            } else {
                $chartModel = LivewireCharts::multiColumnChartModel();
            }

            // Configure chart basics
            $chartModel->setTitle($this->chartTitle)
                ->setAnimated(true)
                ->withOnColumnClickEventName('onColumnClick')
                ->setDataLabelsEnabled(true)
                ->setLegendVisibility(true)
                ->setColumnWidth(30);

            // Add series data
            foreach ($this->reportData as $row) {
                $color = '#' . substr(md5($row['test_name']), 0, 6);

                foreach ($this->quarterColumns as $quarter) {
                    $value = $this->reportType === 'test_count'
                    ? $row['quarters'][$quarter]
                    : $row['study_counts'][$quarter];

                    $chartModel->addSeriesColumn($row['test_name'], $quarter, $value, $color);
                }
            }

            $columnChartModel = $chartModel;
        }

        $displayTotals = array_map(function ($quarterData) {
            return $this->reportType === 'test_count'
            ? $quarterData['test_count']
            : count($quarterData['study_count']);
        }, $this->quarterTotals);

        return view('livewire.lab.reports.test-study-count-report-component', [
            'columnChartModel' => $columnChartModel,
            'displayTotals'    => $displayTotals,
        ]);
    }

}
