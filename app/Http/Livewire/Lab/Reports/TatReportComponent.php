<?php
namespace App\Http\Livewire\Lab\Reports;

use App\Models\TestResult;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class TatReportComponent extends Component
{
    public $timeframe = 'monthly'; // daily, weekly, monthly, quarterly, yearly
    public $startDate;
    public $endDate;
    public $groupedBy   = 'test'; // test, sample, study, user
    public $showDetails = false;
    public $tatData     = [];
    public $periods     = [];
    public $allGroups   = [];
    public $averageTat;
    public $isPrinting = false;

    protected $listeners = ['printReport'];

    public function mount()
    {
        $this->startDate = now()->subYear()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
    }

    public function generateReport()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate'   => 'required|date|after_or_equal:startDate',
        ]);

        // Generate all periods for the selected timeframe
        $this->periods = $this->generatePeriods();

        $query = TestResult::with(['sample', 'test', 'sample.study', 'performer'])
            ->where('status', 'Approved')
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->whereNotNull('approved_at')
        // ->whereNotNull('sample.date_collected')
            ->whereBetween('approved_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);

        $results = $query->get();

        $this->tatData   = [];
        $this->allGroups = [];
        $totalTat        = 0;
        $count           = 0;

        // Initialize all groups with empty periods
        foreach ($results as $result) {
            $groupKey = $this->getGroupKey($result);

            if (! isset($this->tatData[$groupKey])) {
                $this->tatData[$groupKey] = [
                    'name'      => $this->getGroupName($result),
                    'periods'   => $this->initializePeriods(),
                    'total_tat' => 0,
                    'count'     => 0,
                    'details'   => [],
                ];
                $this->allGroups[] = $this->tatData[$groupKey]['name'];
            }
        }

        // Process results
        foreach ($results as $result) {
            $tat = $this->calculateTAT($result);
            if (! $tat) {
                continue;
            }

            $groupKey = $this->getGroupKey($result);
            $timeKey  = $this->getTimeKey($result->approved_at);

            if (isset($this->tatData[$groupKey]['periods'][$timeKey])) {
                $this->tatData[$groupKey]['periods'][$timeKey]['tat_sum'] += $tat;
                $this->tatData[$groupKey]['periods'][$timeKey]['count']++;

                $this->tatData[$groupKey]['total_tat'] += $tat;
                $this->tatData[$groupKey]['count']++;

                // Store details
                $this->tatData[$groupKey]['details'][] = [
                    'sample_id'       => $result->sample_id,
                    'lab_no'          => $result->sample->lab_no,
                    'test_name'       => $result->test->name,
                    'collection_date' => $result->sample->date_collected,
                    'approval_date'   => $result->approved_at,
                    'tat'             => $tat,
                ];

                $totalTat += $tat;
                $count++;
            }
        }

        // Calculate averages
        foreach ($this->tatData as &$group) {
            $group['average_tat'] = $group['count'] ? round($group['total_tat'] / $group['count'], 1) : 0;

            foreach ($group['periods'] as &$period) {
                $period['average_tat'] = $period['count'] ? round($period['tat_sum'] / $period['count'], 1) : 'N/A';
            }
        }

        $this->averageTat = $count ? round($totalTat / $count, 1) : 0;
    }

    protected function initializePeriods()
    {
        $periods = [];
        foreach ($this->periods as $period) {
            $periods[$period] = [
                'tat_sum'     => 0,
                'count'       => 0,
                'average_tat' => 'N/A',
            ];
        }
        return $periods;
    }

    protected function generatePeriods()
    {
        $start   = Carbon::parse($this->startDate);
        $end     = Carbon::parse($this->endDate);
        $periods = [];

        switch ($this->timeframe) {
            case 'daily':
                $period = CarbonPeriod::create($start, '1 day', $end);
                foreach ($period as $date) {
                    $periods[] = $date->format('Y-m-d');
                }
                break;

            case 'weekly':
                $current = $start->copy()->startOfWeek();
                while ($current <= $end) {
                    $periods[] = $current->format('Y-\WW');
                    $current->addWeek();
                }
                break;

            case 'monthly':
                $current = $start->copy()->startOfMonth();
                while ($current <= $end) {
                    $periods[] = $current->format('Y-m');
                    $current->addMonth();
                }
                break;

            case 'quarterly':
                $current = $start->copy()->startOfQuarter();
                while ($current <= $end) {
                    $periods[] = 'Q' . ceil($current->month / 3) . ' ' . $current->year;
                    $current->addQuarter();
                }
                break;

            case 'yearly':
                $current = $start->copy()->startOfYear();
                while ($current <= $end) {
                    $periods[] = $current->format('Y');
                    $current->addYear();
                }
                break;
        }

        return array_unique($periods);
    }

    protected function calculateTAT($result)
    {
        if (! $result->approved_at || ! $result->sample->date_collected) {
            return null;
        }

        $collectionDate = Carbon::parse($result->sample->sampleReception->date_delivered ?? $result->sample->date_collected);
        $approvalDate   = Carbon::parse($result->approved_at);

        return $approvalDate->diffInDays($collectionDate);
    }

    protected function getGroupKey($result)
    {
        switch ($this->groupedBy) {
            case 'sample':
                return 'sample-' . $result->sample_id;
            case 'study':
                return 'study-' . ($result->sample->study_id ?? 0);
            case 'user':
                return 'user-' . $result->approved_by;
            case 'test':
            default:
                return 'test-' . $result->test_id;
        }
    }

    protected function getGroupName($result)
    {
        switch ($this->groupedBy) {
            case 'sample':
                return $result->sample->sample_identity . ' (' . $result->sample->lab_no . ')';
            case 'study':
                return $result->sample->study->name ?? 'Unknown Study';
            case 'user':
                return $result->performer->name ?? 'Unknown User';
            case 'test':
            default:
                return $result->test->name;
        }
    }

    protected function getTimeKey($date)
    {
        $date = Carbon::parse($date);

        switch ($this->timeframe) {
            case 'daily':
                return $date->format('Y-m-d');
            case 'weekly':
                return $date->format('Y-\WW');
            case 'monthly':
                return $date->format('Y-m');
            case 'quarterly':
                return 'Q' . ceil($date->month / 3) . ' ' . $date->year;
            case 'yearly':
                return $date->format('Y');
            default:
                return $date->format('Y-m');
        }
    }

    public function printReport()
    {
        $this->isPrinting = true;
        $this->dispatchBrowserEvent('print-ready');
    }

    public function exportCsv()
    {
        $fileName = 'tat-report-' . now()->format('Ymd-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Header row
            $header = [$this->groupedBy === 'test' ? 'Test' : ucfirst($this->groupedBy)];
            foreach ($this->periods as $period) {
                $header[] = $period;
            }
            $header[] = 'Average TAT';
            fputcsv($file, $header);

            // Data rows
            foreach ($this->tatData as $group) {
                $row = [$group['name']];
                foreach ($this->periods as $period) {
                    $row[] = $group['periods'][$period]['average_tat'] !== 'N/A'
                    ? $group['periods'][$period]['average_tat']
                    : 'N/A';
                }
                $row[] = $group['average_tat'];
                fputcsv($file, $row);
            }

            // Average row
            $averageRow = ['Overall Average'];
            foreach ($this->periods as $period) {
                $periodAvg   = 0;
                $periodCount = 0;

                foreach ($this->tatData as $group) {
                    if ($group['periods'][$period]['count'] > 0) {
                        $periodAvg += $group['periods'][$period]['average_tat'];
                        $periodCount++;
                    }
                }

                $averageRow[] = $periodCount ? round($periodAvg / $periodCount, 1) : 'N/A';
            }
            $averageRow[] = $this->averageTat;
            fputcsv($file, $averageRow);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.lab.reports.tat-report-component');
    }
}
