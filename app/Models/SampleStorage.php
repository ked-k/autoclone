<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SampleStorage extends Model
{
    use HasFactory,LogsActivity;

    protected $fillable = ['barcode', 'sample_id', 'freezer_id', 'section_id', 'rack_id', 'drawer_id', 'box_id', 'box_column', 'box_row', 'creator_lab', 'created_by'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logFillable()
        ->useLogName('Sample Storage')
        ->dontLogIfAttributesChangedOnly(['updated_at'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'parent_id', 'id');
    }

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'creator_lab', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function freezer()
    {
        return $this->belongsTo(Freezer::class, 'freezer_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        if (Auth::check()) {
            self::creating(function ($model) {
                $model->created_by = auth()->id();
                $model->creator_lab = auth()->user()->laboratory_id;
            });
        }
    }
}
