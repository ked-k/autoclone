<?php

namespace App\Http\Livewire\Admin\Dashboards;

use App\Models\Collector;
use App\Models\Courier;
use App\Models\Facility;
use App\Models\Laboratory;
use App\Models\Requester;
use App\Models\Sample;
use App\Models\SampleReception;
use App\Models\Study;
use App\Models\TestResult;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class MasterDashboardComponent extends Component
{
    public $laboratory_id;

    public $associatedFacilities;

    public $associatedStudies = [];

    public $laboratories;

    public function mount()
    {
        $this->laboratories = Laboratory::where('is_active', 1)->latest()->get();
    }

    // public function updatedLaboratoryId(){

    //     $this->emit('refreshCharts',['lab_id'=>$this->laboratory_id ]);

    // }
    public function filterData()
    {
        //SAMPLES
        $count['participantCount'] = Sample::select('*')
                                ->when($this->laboratory_id != 0, function ($query) {
                                    $query->where('creator_lab', $this->laboratory_id);
                                }, function ($query) {
                                    return $query;
                                })->distinct()->count('participant_id');

        $count['batchesCount'] = SampleReception::select('*')->whereRaw('samples_accepted=samples_handled')
                            ->when($this->laboratory_id != 0, function ($query) {
                                $query->where('creator_lab', $this->laboratory_id);
                            }, function ($query) {
                                return $query;
                            })->count();

        $count['samplesCount'] = Sample::select('*')
                                ->when($this->laboratory_id != 0, function ($query) {
                                    $query->where('creator_lab', $this->laboratory_id);
                                }, function ($query) {
                                    return $query;
                                })->count();

        $count['samplesTodayCount'] = Sample::select('*')
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('creator_lab', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->where('status', 'Tests Done')->whereDay('updated_at', '=', date('d'))->count();

        $count['samplesThisWeekCount'] = Sample::select('*')
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('creator_lab', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->where('status', 'Tests Done')->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        $count['samplesThisMonthCount'] = Sample::select('*')
                                        ->when($this->laboratory_id != 0, function ($query) {
                                            $query->where('creator_lab', $this->laboratory_id);
                                        }, function ($query) {
                                            return $query;
                                        })->where('status', 'Tests Done')->whereMonth('updated_at', '=', date('m'))->count();

        $count['samplesThisYearCount'] = Sample::select('*')
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('creator_lab', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->where('status', 'Tests Done')->whereYear('updated_at', '=', date('Y'))->count();

        //TESTS
        $count['testsPerformedCount'] = TestResult::select('*')
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('creator_lab', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->where('status', 'Approved')->count();

        $count['testsTodayCount'] = TestResult::select('*')
                                ->when($this->laboratory_id != 0, function ($query) {
                                    $query->where('creator_lab', $this->laboratory_id);
                                }, function ($query) {
                                    return $query;
                                })->where('status', 'Approved')->whereDay('created_at', '=', date('d'))->count();

        $count['testsThisWeekCount'] = TestResult::select('*')
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('creator_lab', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->where('status', 'Approved')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        $count['testsThisMonthCount'] = TestResult::select('*')
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('creator_lab', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->where('status', 'Approved')->whereMonth('created_at', '=', date('m'))->count();

        $count['testsThisYearCount'] = TestResult::select('*')
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('creator_lab', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->where('status', 'Approved')->whereYear('created_at', '=', date('Y'))->count();

        //USERS
        $count['usersActiveCount'] = User::select('*')->where(['is_active' => 1])
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('laboratory_id', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->count();

        $count['usersSuspendedCount'] = User::select('*')->where(['is_active' => 0])
                                    ->when($this->laboratory_id != 0, function ($query) {
                                        $query->where('laboratory_id', $this->laboratory_id);
                                    }, function ($query) {
                                        return $query;
                                    })->count();

        //LABS
        $count['laboratoryCount'] = Laboratory::where('is_active', 1)->count();

        if ($this->laboratory_id) {
            $lab = Laboratory::where('id', $this->laboratory_id)->first();
            $this->associatedFacilities = $lab->associated_facilities;
            $this->associatedStudies = $lab->associated_studies;
        }

        //FACILITIES
        $count['facilityActiveCount'] = Facility::select('*')
                                                ->when($this->laboratory_id != 0, function ($query) {
                                                    $query->whereIn('id', $this->associatedFacilities ?? []);
                                                }, function ($query) {
                                                    return $query;
                                                })->where('is_active', 1)->count();

        $count['facilitySuspendedCount'] = Facility::select('*')
                                                ->when($this->laboratory_id != 0, function ($query) {
                                                    $query->whereIn('id', $this->associatedFacilities ?? []);
                                                }, function ($query) {
                                                    return $query;
                                                })->where('is_active', 0)->count();

        //STUDIES
        $count['studyActiveCount'] = Study::select('*')
                                        ->when($this->laboratory_id != 0, function ($query) {
                                            $query->whereIn('id', $this->associatedStudies ?? []);
                                        }, function ($query) {
                                            return $query;
                                        })->where('is_active', 1)->count();

        $count['studySuspendedCount'] = Study::select('*')
                                            ->when($this->laboratory_id != 0, function ($query) {
                                                $query->whereIn('id', $this->associatedStudies ?? []);
                                            }, function ($query) {
                                                return $query;
                                            })->where('is_active', 0)->count();

        //REQUESTERS
        $count['requesterActiveCount'] = Requester::select('*')
                                                ->when($this->laboratory_id != 0, function ($query) {
                                                    $query->whereIn('study_id', $this->associatedStudies ?? []);
                                                }, function ($query) {
                                                    return $query;
                                                })->where('is_active', 1)->count();

        $count['requesterSuspendedCount'] = Requester::select('*')
                                                    ->when($this->laboratory_id != 0, function ($query) {
                                                        $query->whereIn('study_id', $this->associatedStudies ?? []);
                                                    }, function ($query) {
                                                        return $query;
                                                    })->where('is_active', 0)->count();

        //PHLEBOTOMISTS
        $count['collectorActiveCount'] = Collector::select('*')
                                                ->when($this->laboratory_id != 0, function ($query) {
                                                    $query->whereIn('facility_id', $this->associatedFacilities ?? []);
                                                }, function ($query) {
                                                    return $query;
                                                })->where('is_active', 1)->count();

        $count['collectorSuspendedCount'] = Collector::select('*')
                                                    ->when($this->laboratory_id != 0, function ($query) {
                                                        $query->whereIn('facility_id', $this->associatedFacilities ?? []);
                                                    }, function ($query) {
                                                        return $query;
                                                    })->where('is_active', 0)->count();

        //COURIERS
        $count['courierActiveCount'] = Courier::select('*')
                                            ->when($this->laboratory_id != 0, function ($query) {
                                                $query->whereIn('facility_id', $this->associatedFacilities ?? []);
                                            }, function ($query) {
                                                return $query;
                                            })->where('is_active', 1)->count();

        $count['courierSuspendedCount'] = Courier::select('*')
                                                ->when($this->laboratory_id != 0, function ($query) {
                                                    $query->whereIn('facility_id', $this->associatedFacilities ?? []);
                                                }, function ($query) {
                                                    return $query;
                                                })->where('is_active', 0)->count();

        return $count;
    }

    public function render()
    {
        $dataCounts = $this->filterData();

        return view('livewire.admin.dashboards.master-dashboard-component', $dataCounts);
    }
}
