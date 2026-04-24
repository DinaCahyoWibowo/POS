<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonToStockOpnameLines extends Migration
{
    public function up()
    {
        Schema::table('stock_opname_lines', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('physical_qty');
        });
    }

    public function down()
    {
        Schema::table('stock_opname_lines', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
}
