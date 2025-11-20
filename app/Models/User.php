<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable,LogsActivity,CausesActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logFillable()
        ->useLogName('users')
        ->dontLogIfAttributesChangedOnly(['updated_at', 'password'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
        // Chain fluent methods for configuration options
    }

    /**
     * The attributes that are mass assignable.
     *\Auth::user()->actions;
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'surname',
        'first_name',
        'other_name',
        'name',
        'email',
        'password',
        'password_updated_at',
        'laboratory_id',
        'designation_id',
        'contact',
        'title',
        'avatar',
        'signature',
        'is_active',
        'color_scheme',
        'created_by',
        'emp_id',
        'creator_lab',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->surname.' '.$this->first_name.' '.$this->other_name,
        );
    }

    protected function passwordUpdatedAt(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)->format('d-m-Y H:i'),
            // set: fn ($value) =>  Carbon::parse($value)->format('Y-m-d'),
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

            self::updating(function ($model) {
                $model->creator_lab = auth()->user()->laboratory_id;
            });
        }
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
        : static::query()
            ->where('surname', 'like', '%'.$search.'%')
            ->orWhere('first_name', 'like', '%'.$search.'%')
            ->orWhere('other_name', 'like', '%'.$search.'%')
            ->orWhere('contact', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')
            ->orWhereHas('laboratory', function ($query) use ($search) {
                $query->where('laboratory_name', 'like', '%'.$search.'%');
            })
            ->orWhereHas('designation', function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
    }
}
