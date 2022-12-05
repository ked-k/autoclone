<?php

namespace App\Models;

use App\Models\Admin\Test;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TestAssignment extends Model
{
    use HasFactory,LogsActivity;

    protected $fillable = ['sample_id', 'test_id', 'assignee', 'assigned_by', 'creator_lab', 'status'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logFillable()
        ->useLogName('test-assignment')
        ->dontLogIfAttributesChangedOnly(['updated_at'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee', 'id');
    }

    public function assigned_by()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class, 'sample_id', 'id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'id');
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
                $model->assigned_by = auth()->id();
                $model->creator_lab = auth()->user()->laboratory_id;
            });
        }
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()
                ->where('creator_lab', auth()->user()->laboratory_id)
                ->orWhereHas('test', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('sample', function ($query) use ($search) {
                    $query->where('sample_identity', 'like', '%'.$search.'%');
                })
                ->orWhereHas('assignee', function ($query) use ($search) {
                    $query->where('surname', 'like', '%'.$search.'%');
                });
    }
}
