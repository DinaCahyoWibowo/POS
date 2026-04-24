<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Backfill item_name from items table for existing stock movement lines
        DB::statement("UPDATE stock_movement_lines JOIN items ON items.id = stock_movement_lines.item_id SET stock_movement_lines.item_name = items.name WHERE stock_movement_lines.item_name IS NULL");
    }

    public function down()
    {
        // noop
    }
};
