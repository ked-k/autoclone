<?php
namespace App\Http\Livewire\Lab\Reports;

use App\Models\Laboratory;
use App\Models\Sample;
use App\Models\TestResult;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TestsPerLabReportComponent extends Component
{
    public $startYear;
    public $endYear;
    public $selectedLabs = [];
    public $reportType   = 'tests'; // 'tests' or 'samples'
    public $testData     = [];
    public $sampleData   = [];
    public $years        = [];
    public $allLabs      = [];
    public $totalCount   = 0;

    public function mount()
    {
        $currentYear     = now()->year;
        $this->startYear = $currentYear - 5;
        $this->endYear   = $currentYear;

        $this->allLabs      = Laboratory::orderBy('laboratory_name')->get();
        $this->selectedLabs = $this->allLabs->pluck('id')->toArray();

        $this->generateReport();
    }

    public function generateReport()
    {
        $this->validate([
            'startYear' => 'required|integer|min:2000|max:2100',
            'endYear'   => 'required|integer|min:' . $this->startYear . '|max:2100',
        ]);

        // Generate all years in range
        $this->years = range($this->startYear, $this->endYear);

        // Generate test data
        $this->generateTestData();

        // Generate sample data
        $this->generateSampleData();
    }

    protected function generateTestData()
    {
        $results = TestResult::query()
            ->select([
                'creator_lab',
                DB::raw('YEAR(approved_at) as year'),
                DB::raw('COUNT(*) as count'),
            ])
            ->where('status', 'Approved')
            ->whereNotNull('approved_at')
            ->whereIn('creator_lab', $this->selectedLabs)
            ->whereBetween(DB::raw('YEAR(approved_at)'), [$this->startYear, $this->endYear])
            ->groupBy('creator_lab', DB::raw('YEAR(approved_at)'))
            ->orderBy('creator_lab')
            ->orderBy('year')
            ->get();

        $this->testData   = [];
        $this->totalCount = 0;

        foreach ($this->allLabs as $lab) {
            if (! in_array($lab->id, $this->selectedLabs)) {
                continue;
            }

            $this->testData[$lab->id] = [
                'name'  => $lab->laboratory_name,
                'years' => array_fill_keys($this->years, 0),
                'total' => 0,
            ];
        }

        foreach ($results as $result) {
            if (isset($this->testData[$result->creator_lab]['years'][$result->year])) {
                $this->testData[$result->creator_lab]['years'][$result->year] = $result->count;
                $this->testData[$result->creator_lab]['total'] += $result->count;
                $this->totalCount += $result->count;
            }
        }
    }

    protected function generateSampleData()
    {
        $results = Sample::query()
            ->select([
                'creator_lab',
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count'),
            ])
            ->whereIn('creator_lab', $this->selectedLabs)
            ->whereBetween(DB::raw('YEAR(created_at)'), [$this->startYear, $this->endYear])
            ->groupBy('creator_lab', DB::raw('YEAR(created_at)'))
            ->orderBy('creator_lab')
            ->orderBy('year')
            ->get();

        $this->sampleData = [];
        $this->totalCount = 0;

        foreach ($this->allLabs as $lab) {
            if (! in_array($lab->id, $this->selectedLabs)) {
                continue;
            }

            $this->sampleData[$lab->id] = [
                'name'  => $lab->laboratory_name,
                'years' => array_fill_keys($this->years, 0),
                'total' => 0,
            ];
        }

        foreach ($results as $result) {
            if (isset($this->sampleData[$result->creator_lab]['years'][$result->year])) {
                $this->sampleData[$result->creator_lab]['years'][$result->year] = $result->count;
                $this->sampleData[$result->creator_lab]['total'] += $result->count;
                $this->totalCount += $result->count;
            }
        }
    }

    public function render()
    {
        return view('livewire.lab.reports.tests-per-lab-report', [
            'reportData' => $this->reportType === 'tests' ? $this->testData : $this->sampleData,
        ]);
    }

    public function exportCsv()
    {
        $fileName = ($this->reportType === 'tests' ? 'tests' : 'samples') . '-per-lab-' . now()->format('Ymd-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $data       = $this->reportType === 'tests' ? $this->testData : $this->sampleData;
        $totalCount = array_reduce($data, fn($carry, $item) => $carry + $item['total'], 0);

        $callback = function () use ($data, $totalCount) {
            $file = fopen('php://output', 'w');

            // Header row
            $header = ['Laboratory'];
            foreach ($this->years as $year) {
                $header[] = $year;
            }
            $header[] = 'Total';
            fputcsv($file, $header);

            // Data rows
            foreach ($data as $lab) {
                $row = [$lab['name']];
                foreach ($this->years as $year) {
                    $row[] = $lab['years'][$year];
                }
                $row[] = $lab['total'];
                fputcsv($file, $row);
            }

            // Total row
            $totalRow = ['Total'];
            foreach ($this->years as $year) {
                $yearTotal = 0;
                foreach ($data as $lab) {
                    $yearTotal += $lab['years'][$year];
                }
                $totalRow[] = $yearTotal;
            }
            $totalRow[] = $totalCount;
            fputcsv($file, $totalRow);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
