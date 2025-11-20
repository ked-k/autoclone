<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemReportItem extends Model
{
    protected $fillable=['status'];
    use HasFactory;
}
