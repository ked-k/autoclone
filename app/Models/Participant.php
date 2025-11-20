<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Participant extends Model
{
    use HasFactory,LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logFillable()
        ->useLogName('participants')
        ->dontLogIfAttributesChangedOnly(['updated_at'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }

    protected $fillable = ['participant_no', 'identity', 'age', 'months', 'gender', 'contact', 'address', 'nok_contact', 'nok_address',

        'clinical_notes', 'title', 'nin_number', 'surname', 'first_name', 'other_name', 'nationality', 'district', 'dob', 'email', 'birth_place', 'religious_affiliation', 'occupation', 'civil_status', 'nok', 'nok_relationship',
        'facility_id', 'study_id', 'created_by', 'creator_lab', ];

    public function sample()
    {
        return $this->hasMany(Sample::class, 'participant_id', 'id');
    }

    public function testResult()
    {
        return $this->hasManyThrough(TestResult::class, Sample::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }

    public function study()
    {
        return $this->belongsTo(Study::class, 'study_id', 'id');
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->title.' '.$this->surname.' '.$this->first_name.' '.$this->other_name,
        );
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

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()
                ->where('identity', 'like', '%'.$search.'%')
                ->orWhere('contact', 'like', '%'.$search.'%')
                ->orWhere('address', 'like', '%'.$search.'%')
                ->orWhereHas('facility', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('study', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                });
    }

    public static function targetSearch($search)
    {
        return empty(trim($search)) ? static::query()
            : static::query()
                ->where('identity', trim($search))->withCount(['sample', 'testResult'])->with('facility', 'study');
    }
}
