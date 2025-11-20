<?php
namespace App\Models;

use App\Models\Laboratory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Sample extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logFillable()
            ->useLogName('samples')
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }

    protected $fillable = ['sample_reception_id', 'participant_id', 'visit', 'sample_type_id', 'sample_no', 'sample_identity', 'parent_id', 'lab_no', 'volume', 'requested_by', 'date_requested', 'collected_by', 'date_collected',
        'study_id', 'sample_is_for', 'priority', 'tests_requested', 'test_count', 'tests_performed', 'date_acknowledged', 'request_acknowledged_by', 'status', 'is_isolate', 'created_by', 'creator_lab'];

    protected $casts = [
        'tests_requested' => 'array',
        'tests_performed' => 'array',
        'referred_tests'  => 'array',
    ];
    // In Sample model
    public function calculateTAT()
    {
        if (! $this->date_collected || ! $this->testResults->count()) {
            return null;
        }

        $firstResultDate  = $this->testResults->min('created_at');
        $lastApprovedDate = $this->testResults->where('status', 'Approved')->max('approved_at');

        return [
            'collection_to_reception' => $this->sampleReception?->created_at
            ? $this->sampleReception->created_at->diffInDays($this->date_collected)
            : null,

            'reception_to_processing' => $firstResultDate
            ? $firstResultDate->diffInDays($this->sampleReception->created_at)
            : null,

            'processing_to_approval'  => $lastApprovedDate && $firstResultDate
            ? $lastApprovedDate->diffInDays($firstResultDate)
            : null,

            'total_tat'               => $lastApprovedDate
            ? $lastApprovedDate->diffInDays($this->date_collected)
            : null,
        ];
    }
    public function getReferredTestsAttribute($value)
    {
        return json_decode($value ?? '[]', true);
    }

    public function setReferredTestsAttribute($value)
    {
        $this->attributes['referred_tests'] = json_encode($value);
    }
    public function getTestsReferredCountAttribute()
    {
        return is_array($this->referred_tests) ? count($this->referred_tests) : 0;
    }
    public function getTestsRequestedCountAttribute()
    {
        return is_array($this->tests_requested) ? count($this->tests_requested) : 0;
    }
    public function getTestsPerformedCountAttribute()
    {
        return is_array($this->tests_performed) ? count($this->tests_performed) : 0;
    }
    public function accessioner()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function sampleReception()
    {
        return $this->belongsTo(SampleReception::class, 'sample_reception_id', 'id');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id', 'id');
    }

    public function testResult()
    {
        return $this->hasMany(TestResult::class, 'sample_id', 'id');
    }

    public function sampleType()
    {
        return $this->belongsTo(SampleType::class, 'sample_type_id', 'id');
    }

    public function study()
    {
        return $this->belongsTo(Study::class, 'study_id', 'id');
    }

    public function requester()
    {
        return $this->belongsTo(Requester::class, 'requested_by', 'id');
    }

    public function collector()
    {
        return $this->belongsTo(Collector::class, 'collected_by', 'id');
    }

    public function testAssignment()
    {
        return $this->hasMany(TestAssignment::class, 'sample_id', 'id');
    }

    public function aliquotingAssignment()
    {
        return $this->hasOne(AliquotingAssignment::class, 'sample_id', 'id');
    }

    public function aliquots()
    {
        return $this->hasMany(SamplesAliquot::class, 'parent_id', 'id');
    }

    public function storage()
    {
        return $this->hasOne(SampleStorage::class, 'sample_id', 'id');
    }
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'creator_lab', 'id');
    }

    public static function boot()
    {
        parent::boot();
        if (Auth::check()) {
            self::creating(function ($model) {
                $model->created_by  = auth()->id();
                $model->creator_lab = auth()->user()->laboratory_id;
            });
            self::updating(function ($model) {
                $model->creator_lab = auth()->user()->laboratory_id;
            });
        }
    }

    public static function search($search, $status)
    {
        return empty($search) ? static::query()
        : static::query()
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->where(
                function ($query) use ($search, $status) {
                    $query->whereIn('status', $status)
                        ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => 'Testing'])
                        ->whereHas('participant', function ($query) use ($search) {
                            $query->where('identity', 'like', '%' . $search . '%');
                        });
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->whereIn('status', $status)
                        ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => 'Testing'])
                        ->where('lab_no', 'like', '%' . $search . '%');
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->whereIn('status', $status)
                        ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => 'Testing'])
                        ->where('sample_identity', 'like', '%' . $search . '%');
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->whereIn('status', $status)
                        ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => 'Testing'])
                        ->whereHas('study', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->whereIn('status', $status)
                        ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => 'Testing'])
                        ->whereHas('sampleReception', function ($query) use ($search) {
                            $query->where('batch_no', 'like', '%' . $search . '%');
                        });
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->whereIn('status', $status)
                        ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => 'Testing'])
                        ->whereHas('requester', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                }
            );
    }

    public static function targetSearch($search)
    {
        return empty(trim($search)) ? static::query()
        : static::query()
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->where('sample_identity', trim($search))
            ->where('sample_no', trim($search))
            ->orWhereHas('sampleReception', function ($query) use ($search) {
                $query->where('batch_no', $search);
            })
            ->orWhere('lab_no', trim($search));
    }
}
