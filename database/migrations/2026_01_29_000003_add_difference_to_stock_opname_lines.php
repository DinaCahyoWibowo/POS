<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDifferenceToStockOpnameLines extends Migration
{
    public function up()
    {
        Schema::table('stock_opname_lines', function (Blueprint $table) {
            $table->decimal('difference', 14, 4)->default(0)->after('physical_qty');
        });
    }

    public function down()
    {
        Schema::table('stock_opname_lines', function (Blueprint $table) {
            $table->dropColumn('difference');
        });
    }
}
