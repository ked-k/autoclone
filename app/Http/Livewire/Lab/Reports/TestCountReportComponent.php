<?php
namespace App\Http\Livewire\Lab\Reports;

use App\Models\Admin\Test;
use App\Models\Sample;
use App\Models\Study;
use Illuminate\Support\Carbon;
use Livewire\Component;

class TestCountReportComponent extends Component
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
    public $search         = '';

    public function mount()
    {
        $this->tests = Test::search($this->search)
            ->where('creator_lab', auth()->user()->laboratory_id)->orderBy('name')->get();
        $this->studies = Study::orderBy('name')->get();

        // Set default date range to current year
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

        // Apply study filter
        if ($this->selectedStudy) {
            $query->where('study_id', $this->selectedStudy);
        }

        $samples = $query->get();

        // Initialize report structure
        $report               = [];
        $this->quarterColumns = $this->getQuarterColumns();
        $this->quarterTotals  = array_fill_keys($this->quarterColumns, 0);
        $testTotals           = [];

        // Initialize all tests with zero counts for all quarters
        foreach ($this->tests as $test) {
            $report[$test->id] = [
                'name'     => $test->name,
                'quarters' => array_fill_keys($this->quarterColumns, 0),
            ];
        }

        // Process samples
        foreach ($samples as $sample) {
            $testIds        = $sample->tests_requested;
            $collectionDate = Carbon::parse($sample->date_collected);
            $quarter        = 'Q' . $collectionDate->quarter . ' ' . $collectionDate->year;

            // Skip if quarter not in our columns
            if (! in_array($quarter, $this->quarterColumns)) {
                continue;
            }

            foreach ($testIds as $testId) {
                // Apply test filter
                if (! empty($this->selectedTests)) {
                    if (! in_array($testId, $this->selectedTests)) {
                        continue;
                    }
                }

                if (isset($report[$testId])) {
                    $report[$testId]['quarters'][$quarter]++;
                    $this->quarterTotals[$quarter]++;

                    // Initialize test total if not set
                    if (! isset($testTotals[$testId])) {
                        $testTotals[$testId] = 0;
                    }
                    $testTotals[$testId]++;
                }
            }
        }

        // Prepare final report data
        $this->reportData = [];
        foreach ($report as $testId => $testData) {
            // Skip tests with zero counts if not showing them
            if (! $this->showZeroCounts && (! isset($testTotals[$testId]) || $testTotals[$testId] === 0)) {
                continue;
            }

            $this->reportData[] = [
                'test_id'   => $testId,
                'test_name' => $testData['name'],
                'quarters'  => $testData['quarters'],
                'total'     => $testTotals[$testId] ?? 0,
            ];
        }

        // Sort by test name
        usort($this->reportData, function ($a, $b) {
            return $a['test_name'] <=> $b['test_name'];
        });
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
    public function render()
    {

        return view('livewire.lab.reports.test-count-report-component');
    }
}
