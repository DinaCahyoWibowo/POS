<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillStockMovementNotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:backfill-stock-notes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace stock movement notes like "Sale #<id>" with the sale code (Sale SYYYYMMDDNNNN)';

    public function handle()
    {
        $this->info('Starting backfill of stock movement notes...');

        $saleClass = \App\Models\Sale::class;

        $query = DB::table('stock_movements')
            ->where('reference_type', $saleClass)
            ->where('note', 'like', 'Sale #%');

        $total = $query->count();
        $this->info("Found {$total} stock movement(s) to check/update.");

        if ($total === 0) {
            $this->info('Nothing to do.');
            return 0;
        }

        $updated = 0;

        $query->orderBy('id')->chunkById(100, function($rows) use (&$updated) {
            foreach ($rows as $r) {
                $refId = $r->reference_id;
                if (!$refId) continue;
                $sale = DB::table('sales')->where('id', $refId)->first(['code']);
                if ($sale && !empty($sale->code)) {
                    $newNote = 'Sale ' . $sale->code;
                    // Avoid unnecessary updates
                    if ($r->note !== $newNote) {
                        DB::table('stock_movements')->where('id', $r->id)->update(['note' => $newNote]);
                        $updated++;
                    }
                }
            }
        });

        $this->info("Backfill complete. Updated {$updated} rows.");
        return 0;
    }
}
