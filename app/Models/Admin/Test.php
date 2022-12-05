<?php

namespace App\Models\Admin;

use App\Models\TestAssignment;
use App\Models\TestCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'short_code',
        'price',
        'tat',
        'reference_range_min',
        'reference_range_max',
        'precautions',
        'result_type',
        'absolute_results',
        'measurable_result_uom',
        'comments',
        'status',
        'created_by',
        'creator_lab',
    ];

    protected $casts = ['absolute_results' => 'array', 'comments' => 'array'];

    public function category()
    {
        return $this->belongsTo(TestCategory::class, 'category_id', 'id');
    }

    public function testAssignment()
    {
        return $this->hasMany(TestAssignment::class, 'test_id', 'id');
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
            ->where('name', 'like', '%'.$search.'%')
            ->orWhere('short_code', 'like', '%'.$search.'%')
            ->orWhere('price', 'like', '%'.$search.'%')
            ->orWhere('result_type', 'like', '%'.$search.'%')
            ->orWhereHas('category', function ($query) use ($search) {
                $query->where('category_name', 'like', '%'.$search.'%');
            });
    }
}
