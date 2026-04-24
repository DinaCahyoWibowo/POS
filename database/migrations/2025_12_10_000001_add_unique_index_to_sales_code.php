<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create unique index only if it doesn't already exist (idempotent)
        $exists = \Illuminate\Support\Facades\DB::selectOne("SELECT COUNT(1) as c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'sales' AND index_name = 'sales_code_unique'");
        if (!$exists || ($exists && $exists->c == 0)) {
            Schema::table('sales', function (Blueprint $table) {
                $table->unique('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $exists = \Illuminate\Support\Facades\DB::selectOne("SELECT COUNT(1) as c FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'sales' AND index_name = 'sales_code_unique'");
        if ($exists && $exists->c > 0) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropUnique('sales_code_unique');
            });
        }
    }
};
