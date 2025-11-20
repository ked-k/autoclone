<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SampleReception extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logFillable()
            ->useLogName('sample_reception')
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }

    protected $fillable = ['batch_no', 'date_delivered', 'samples_delivered', 'courier_id', 'facility_id', 'received_by', 'samples_accepted', 'samples_rejected', 'samples_handled', 'rejection_reason', 'courier_signed', 'created_by', 'creator_lab',
        'reviewed_by', 'date_reviewed', 'comment', 'status'];

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class, 'courier_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }

    public function sample()
    {
        return $this->hasMany(Sample::class, 'sample_reception_id', 'id');
    }

    public function rejectedSamples()
    {
        return $this->hasMany(Sample::class, 'sample_reception_id', 'id')->where('status', 'Rejected');
    }

    protected function createdAt(): Attribute
    {
        return new Attribute(
            get: fn($value) => Carbon::parse($value)->format('d-m-Y H:i'),
            // set: fn ($value) =>  Carbon::parse($value)->format('Y-m-d'),
        );
    }

    public static function boot()
    {
        parent::boot();
        if (Auth::check()) {
            self::creating(function ($model) {
                $model->created_by    = auth()->id();
                $model->creator_lab   = auth()->user()->laboratory_id;
                $model->reviewed_by   = auth()->id();
                $model->date_reviewed = now();
            });
        }
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
        : static::query()
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->where('batch_no', 'like', '%' . $search . '%');
    }

    public static function targetSearch($search)
    {
        return empty(trim($search)) ? static::query()
        : static::query()
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->where('batch_no', trim($search));
    }
    protected $casts = [
        'date_delivered' => 'datetime',
        'created_at'     => 'datetime',
        'date_reviewed'  => 'datetime',
    ];
}
