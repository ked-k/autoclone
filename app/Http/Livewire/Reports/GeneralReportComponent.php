<?php

namespace App\Http\Livewire\Reports;

use App\Exports\SamplesExport;
use App\Models\Facility;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Study;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class GeneralReportComponent extends Component
{
    use WithPagination;

    public $facility_id = 0;

    public $study_id = 0;

    public $job = '';

    public $sampleType;

    public $created_by = 0;

    public $from_date = '';

    public $to_date = '';

    public $perPage = 10;

    public $orderBy = 'id';

    public $orderAsc = 0;

    public $export;

    public $studies;

    public $sampleIds = [];

    protected $paginationTheme = 'bootstrap';

    public $recall_id;

    public $reception_id;

    public $sample_identity;

    public $sample_id;

    public $freezer_location;

    public $freezer;

    public $temp;

    public $section_id;

    public $rack_id;

    public $drawer_id;

    public $box_id;

    public $box_row;

    public $box_column;

    public $barcode;

    public $stored_by;

    public $date_stored;

    public $sample;

    public $search;

    public $edit_id;
    public $sample_study_id;
    public $sample_facility_id;

    public $lab_no;
    public $return_type;
    public $group_by;
    public $status;
    public $samples;

    public function updatedFacilityId()
    {
        if ($this->facility_id != 0) {
            $this->studies = Study::whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->where('facility_id', $this->facility_id)->get();
        }
    }

    public function mount()
    {
        $this->samples = collect([]);
        $this->studies = Study::whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->get();
    }

    public function export()
    {
        if (count($this->sampleIds) > 0) {
            return (new SamplesExport($this->sampleIds))->download('Samples_' . date('Y-m-d') . '_' . now()->toTimeString() . '.xlsx');
        } else {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error', 'message' => 'Oops! No Samples selected for export!']);
        }
    }

    public function filterSamples()
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $samples = Sample::select('*')->where('samples.creator_lab', auth()->user()->laboratory_id)->with(['sampleType'])
            ->when($this->facility_id != 0, function ($query) {
                $query->whereHas('participant', function ($query) {
                    $query->where('facility_id', $this->facility_id);
                });
            }, function ($query) {
                return $query;
            })
            ->when($this->study_id != 0, function ($query) {
                $query->where('study_id', $this->study_id);
            }, function ($query) {
                return $query;
            })
            ->when($this->created_by != 0, function ($query) {
                $query->where('created_by', $this->created_by);
            }, function ($query) {
                return $query;
            })
            ->when($this->job != '', function ($query) {
                $query->where('sample_is_for', $this->job);
            }, function ($query) {
                return $query;
            })
            ->when($this->sampleType != 0, function ($query) {
                $query->where('sample_type_id', $this->sampleType);
            }, function ($query) {
                return $query;
            })
            ->when($this->from_date != '' && $this->to_date != '', function ($query) {
                $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
            }, function ($query) {
                return $query;
            });

        $this->sampleIds = $samples->pluck('id')->toArray();

        return $samples;
        DB::statement("SET sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");
    }

    public function listSamplesCounts()
    {
        if ($this->return_type == 'Count') {

            $this->samples = $samples = $this->filterSamples()
                ->select('sample_types.type as sample_type_name', 'sample_type_id', DB::raw('count(*) as total_samples'))
                ->leftJoin('sample_types', 'samples.sample_type_id', '=', 'sample_types.id')
                ->when($this->group_by == 'type', function ($query) {
                    $query->groupBy('sample_type_id', 'sample_types.type');
                })
                ->get();
                $fileName = 'All sample count';
                $headers = [
                    'Content-type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=$fileName",
                    'Pragma' => 'no-cache',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires' => '0',
                ];
        
                $columns = ['#', 'Sample_Type', 'Total Samples'];
        
                $callback = function () use ($samples, $columns) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
        
                    foreach ($samples as  $key => $sample) {
                        $row['count'] = $key+1;
                        $row['Sample_Type'] = $sample->sample_type_name??'N/A';
                        $row['total'] = $sample->total_samples??'N/A';
                        fputcsv($file, [
                            $row['count'],
                            $row['Sample_Type'],
                            $row['total'],
                        ]);
                    }
        
                    fclose($file);
                };
        
                return response()->stream($callback, 200, $headers);

        }
    }


    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function cancel()
    {
        $this->reset(['recall_id', 'sample_id', 'edit_id', 'sample_identity']);
    }

    public function render()
    {
        $users = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->latest()->get();
        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->get();
        $sampleTypes = SampleType::where('creator_lab', auth()->user()->laboratory_id)->orderBy('type', 'asc')->get();
        $jobs = Sample::select('sample_is_for')->distinct()->get();
        // $samples = $this->filterSamples()->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        // ->paginate($this->perPage);

        return view('livewire.reports.general-report-component', compact('facilities', 'jobs', 'sampleTypes', 'users'));
    }

}
