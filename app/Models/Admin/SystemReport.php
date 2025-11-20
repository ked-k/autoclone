<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemReport extends Model
{
    use HasFactory;

    protected $fillable=[
        'ref_code', 
        'status',
        'comments', 
        'report_date',
        'submitted_at', 
        'reviewer_comment', 
        'reviewed_at', 
        'facility_id', 
        'reviewed_by', 
        'created_by', 
    ];

    public function items()
    {
        return $this->hasMany(SystemReportItem::class, 'system_report_id', 'id');
    }

    public $guarded = [];

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
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->where('ref_code', 'like', '%'.$search.'%');
    }
}
