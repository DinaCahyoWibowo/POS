<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name','brand_id','category_id','base_unit_id','description','cost_price','sell_price','image'];
    // code is auto-generated and not fillable

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function itemUnits()
    {
        return $this->hasMany(ItemUnit::class);
    }

    public function stockMovements()
    {
        return $this->hasManyThrough(StockMovementLine::class, StockMovement::class, 'id', 'item_id', 'id', 'id');
    }

    /**
     * Calculate current stock in base units by summing stock movement lines for this item
     */
    public function currentStock()
    {
        // sum qty from stock_movement_lines where item_id = this id
        $res = \DB::table('stock_movement_lines')->where('item_id', $this->id)->sum('qty');
        return (float) $res;
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            // generate code as CATEGORYCODE + BRANDCODE + number (padded)
            if (empty($item->code)) {
                $cat = null; $brand = null;
                if ($item->category_id) $cat = Category::find($item->category_id);
                if ($item->brand_id) $brand = Brand::find($item->brand_id);

                $catCode = $cat ? strtoupper(Str::slug($cat->code ?? $cat->name, '')) : 'XX';
                $brandCode = $brand ? strtoupper(Str::slug($brand->code ?? $brand->name, '')) : 'YY';

                $count = Item::where('category_id', $item->category_id)->where('brand_id', $item->brand_id)->count();
                $number = $count + 1;
                $item->code = $catCode . $brandCode . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
