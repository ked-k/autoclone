<?php
namespace App\Models\Lab\SampleManagent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SampleReferral extends Model
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

    protected $fillable = [
        'sample_id',
        'test_id',
        'referralLab',
        'reason_id',
        'referral_code',
        'referral_type',
        'courier',
        'storage_condition',
        'transport_medium',
        'sample_integrity',
        'temperature_on_dispatch',
        'additional_notes',
        'date_referred',
        'reason',
        'status',
        'created_by',
        'creator_lab',
    ];
    public function referralable(): MorphTo
    {
        return $this->morphTo();
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
}
