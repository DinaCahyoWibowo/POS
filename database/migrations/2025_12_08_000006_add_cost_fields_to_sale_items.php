<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('unit_factor', 12, 4)->default(1)->after('unit_id');
            $table->decimal('cost_price_per_unit', 14, 2)->nullable()->after('cost_price');
            $table->decimal('cost_total', 14, 2)->nullable()->after('cost_price_per_unit');
        });
    }

    public function down()
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['unit_factor','cost_price_per_unit','cost_total']);
        });
    }
};
