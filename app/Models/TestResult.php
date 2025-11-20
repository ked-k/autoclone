<?php
namespace App\Models;

use App\Models\Admin\Test;
use App\Models\Lab\SampleManagement\TestResultAmendment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TestResult extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logFillable()
            ->useLogName('test_results')
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }

    protected $fillable = [
        'sample_id',
        'test_id',
        'result',
        'attachment',
        'parameters',
        'performed_by',
        'comment',
        'reviewed_by',
        'reviewer_comment',
        'approved_by',
        'approver_comment',
        'reviewed_at',
        'approved_at',
        'status',
        'tracker',
        'download_count',
        'created_by',
        'creator_lab',
        'kit_id',
        'platform_id',
        'verified_lot',
        'kit_expiry_date',
        'tat_comment',
        'amended_state',
        'amendment_type',
        'original_results',
        'amendment_comment',
        'amended_by',
        'amended_at',
        'external_system_sent',
        'external_message_id',
        'sent_to_external_at',
    ];
    public function sample()
    {
        return $this->belongsTo(Sample::class, 'sample_id', 'id');
    }

    public function testResultAmendment()
    {
        return $this->hasMany(TestResultAmendment::class, 'test_result_id', 'id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'id');
    }

    public function kit()
    {
        return $this->belongsTo(Kit::class, 'kit_id', 'id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by', 'id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'creator_lab', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
    public function amendedBy()
    {
        return $this->belongsTo(User::class, 'amended_by', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    protected function createdAt(): Attribute
    {
        return new Attribute(
            get: fn($value) => Carbon::parse($value)->format('d-m-Y H:i'),
            // set: fn ($value) =>  Carbon::parse($value)->format('Y-m-d'),
        );
    }

    protected $casts = [
        'parameters' => 'array',
        'results'    => 'array',
    ];

    public static function boot()
    {
        parent::boot();
        if (Auth::check()) {
            self::creating(function ($model) {
                $model->created_by  = auth()->id();
                $model->creator_lab = auth()->user()->laboratory_id;
                $model->tracker     = '#' . time() . rand(10, 99);
            });

            self::updating(function ($model) {
                $model->creator_lab = auth()->user()->laboratory_id;
            });
        }
    }

    public function calculateTAT()
    {
        if (! $this->approved_at || ! $this->sample->date_collected) {
            return null;
        }

        $collectionDate = Carbon::parse($this->sample->date_collected);
        $approvalDate   = Carbon::parse($this->approved_at);

        return $approvalDate->diffInDays($collectionDate);
    }

    public static function resultSearch($search, $status)
    {
        return empty($search) ? static::query()
        : static::query()
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->where(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereHas('sample.participant', function ($query) use ($search) {
                            $query->where('identity', 'like', '%' . $search . '%');
                        });
                }
            )
            ->where(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereHas('sample', function ($query) use ($search) {
                            $query->where('sample_identity', 'like', '%' . $search . '%');
                        });
                }
            )
            ->where(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereHas('sample', function ($query) use ($search) {
                            $query->where('sample_no', 'like', '%' . $search . '%');
                        });
                }
            )
            ->where(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereHas('sample', function ($query) use ($search) {
                            $query->where('lab_no', 'like', '%' . $search . '%');
                        });
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereDate('created_at', date('Y-m-d', strtotime($search)));
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->where('tracker', 'like', '%' . $search . '%');
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereHas('sample.study', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereHas('sample.sampleReception', function ($query) use ($search) {
                            $query->where('batch_no', 'like', '%' . $search . '%');
                        });
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query
                        ->where('status', $status)
                        ->whereHas('sample', function ($query) use ($search) {
                            $query->where('lab_no', $search);
                        });
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereHas('sample.requester', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                }
            )
            ->orWhere(
                function ($query) use ($search, $status) {
                    $query->where('status', $status)
                        ->whereHas('test', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                }
            );
    }

    public static function targetSearch($search)
    {
        return empty(trim($search)) ? static::query()
        : static::query()
            ->where(['creator_lab' => auth()->user()->laboratory_id,
                'status'               => 'Approved',
                'tracker'              => trim($search),
            ]);
    }
}
