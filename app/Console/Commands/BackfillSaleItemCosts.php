<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SaleItem;
use App\Models\ItemUnit;
use Illuminate\Support\Facades\DB;

class BackfillSaleItemCosts extends Command
{
    protected $signature = 'pos:backfill-sale-costs {--chunk=200}';
    protected $description = 'Backfill sale_items unit_factor, cost_price_per_unit and cost_total using current item_units data';

    public function handle()
    {
        $this->info('Starting backfill of sale_items cost fields...');
        $chunk = (int) $this->option('chunk');

        SaleItem::whereNull('cost_price_per_unit')
            ->orWhereNull('cost_total')
            ->orWhereNull('unit_factor')
            ->orderBy('id')
            ->chunkById($chunk, function($items) {
                foreach ($items as $si) {
                    try {
                        $iu = ItemUnit::where('item_id', $si->item_id)
                            ->where('unit_id', $si->unit_id)
                            ->first();
                        $factor = $iu ? (float)$iu->factor : 1.0;
                        $costPerBase = (float)$si->cost_price;
                        $costPricePerUnit = $costPerBase * $factor;
                        $costTotal = $costPricePerUnit * (float)$si->qty;

                        $si->unit_factor = $factor;
                        $si->cost_price_per_unit = round($costPricePerUnit, 2);
                        $si->cost_total = round($costTotal, 2);
                        $si->save();
                    } catch (\Exception $e) {
                        $this->error('Failed on sale_item id: '.$si->id.' - '.$e->getMessage());
                    }
                }
                $this->info('Processed chunk up to id: '.optional($items->last())->id);
            });

        $this->info('Backfill complete.');
        return 0;
    }
}
