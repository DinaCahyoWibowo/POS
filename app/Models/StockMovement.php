<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = ['type','reference_type','reference_id','note','created_by'];

    public function lines()
    {
        return $this->hasMany(StockMovementLine::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
