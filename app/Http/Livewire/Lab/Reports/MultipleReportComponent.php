<?php
namespace App\Http\Livewire\Lab\Reports;

use App\Models\Study;
use App\Models\TestResult;
use Livewire\Component;

class MultipleReportComponent extends Component
{

    public $study_id = 0;     // Assuming this is set from somewhere, e.g., a form input or route parameter
    public $search_type;      // Default search type, can be 'study', 'sample', etc.
    public $identifiers = ''; // This will hold the input identifiers, e.g., "5" or "ABC-123, XYZ-456"
    public $results     = []; // To hold the results for printing
    public $perPage     = 10;

    public function getResultsForPrinting($study_id, $identifierType, $identifiers)
    {
        $identifiers = is_array($identifiers)
        ? $identifiers
        : array_map('trim', explode(',', $identifiers));
        // dd($identifiers); // Debugging line, remove in production
        $query = TestResult::with([
            'test',
            'sample',
            'kit',
            'sample.participant',
            'sample.sampleReception',
            'sample.sampleType:id,type',
            'sample.study:id,name',
            'sample.requester',
            'sample.collector:id,name',
        ]);
        // ->whereHas('sample', function ($q) use ($study_id) {
        //     $q->where('study_id', $study_id);
        // })
        // ->where('creator_lab', auth()->user()->laboratory_id)
        // ->whereIn('status', ['Approved', 'Reviewed']); // Only approved results

        // Handle different identifier types
        switch ($identifierType) {
            case 'single':
                $query->where('id', $identifiers);
                break;

            case 'participant':
                $query->whereHas('sample.participant', function ($q) use ($identifiers) {
                    $q->whereIn('identity', (array) $identifiers);
                });
                // dd($query->get()); // Debugging line, remove in production
                break;

            case 'sample':
                $query->whereHas('sample', function ($q) use ($identifiers) {
                    $q->whereIn('sample_identity', (array) $identifiers);
                });
                break;
            case 'lab_no':
                $query->whereHas('sample', function ($q) use ($identifiers) {
                    $q->whereIn('lab_no', (array) $identifiers);
                });
                break;

            case 'batch':
                $query->whereHas('sample.sampleReception', function ($q) use ($identifiers) {
                    $q->whereIn('batch_no', (array) $identifiers);
                });
                break;

            case 'custom_list':
                $query->where(function ($q) use ($identifiers) {
                    $q->whereHas('sample', function ($q) use ($identifiers) {
                        $q->whereIn('sample_identity', $identifiers)
                            ->orWhereIn('lab_no', $identifiers);
                    })
                        ->orWhereHas('sample.participant', function ($q) use ($identifiers) {
                            $q->whereIn('identity', $identifiers);
                        })
                        ->orWhereHas('sample.sampleReception', function ($q) use ($identifiers) {
                            $q->whereIn('batch_no', $identifiers);
                        });
                });
                break;
        }

        return $query->get();
    }
    public $testResult;
    public function printResults()
    {

        // $sample = Sample::where('lab_no', '25AAB542')->first();
        // // dd($sample);
        // $result = TestResult::where('sample_id', $sample->id)->first();
        // dd($result);
        $this->validate([
            'study_id'    => 'required|exists:studies,id',
            'search_type' => 'required|string',
            'identifiers' => 'required|string',
        ]);
        $input = $this->identifiers; // Could be "5" or "ABC-123, XYZ-456"

        // Normalize input to array
        $identifiers = is_array($input)
        ? $input
        : array_map('trim', explode(',', $input));

        $this->results = $results = $this->getResultsForPrinting($this->study_id, $this->search_type, $identifiers);
        // dd($this->results); // Debugging line, remove in production
        if ($this->results) {
            $this->testResult = $results->first();
        }

    }
    public function render()
    {
        $data['studies'] = Study::all();
        return view('livewire.lab.reports.multiple-report-component', $data);
    }
}
