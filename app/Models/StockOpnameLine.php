<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameLine extends Model
{
    use HasFactory;

    protected $fillable = ['stock_opname_id','item_id','system_qty','physical_qty','difference','reason'];

    public function opname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
