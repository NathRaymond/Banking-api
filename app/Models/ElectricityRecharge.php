<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityRecharge extends Model
{
    use HasFactory;
    protected $table = 'electricity_recharge';
    protected $guarded = [];
}
