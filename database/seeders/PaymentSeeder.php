<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the payments table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('payments')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        $payments = [
            ['sale_id' => 1,'method' => 'cash','amount' => 130000,'change' => 0,'created_at' => '2025-12-06 05:00:00','updated_at' => '2025-12-06 05:00:00'],
            ['sale_id' => 2,'method' => 'cash','amount' => 11000,'change' => 0,'created_at' => '2025-12-06 05:05:13','updated_at' => '2025-12-06 05:05:13'],
            ['sale_id' => 3,'method' => 'cash','amount' => 15000,'change' => 1000,'created_at' => '2025-12-06 05:42:05','updated_at' => '2025-12-06 05:42:05'],
            ['sale_id' => 4,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-06 07:38:24','updated_at' => '2025-12-06 07:38:24'],
            ['sale_id' => 5,'method' => 'cash','amount' => 130000,'change' => 5000,'created_at' => '2025-12-08 04:04:49','updated_at' => '2025-12-08 04:04:49'],
            ['sale_id' => 6,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-08 11:17:12','updated_at' => '2025-12-08 11:17:12'],
            ['sale_id' => 7,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 10:10:25','updated_at' => '2025-12-10 10:10:25'],
            ['sale_id' => 11,'method' => 'cash','amount' => 150000,'change' => 25000,'created_at' => '2025-12-10 10:14:32','updated_at' => '2025-12-10 10:14:32'],
            ['sale_id' => 12,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 10:17:00','updated_at' => '2025-12-10 10:17:00'],
            ['sale_id' => 13,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 10:18:54','updated_at' => '2025-12-10 10:18:54'],
            ['sale_id' => 14,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 10:29:10','updated_at' => '2025-12-10 10:29:10'],
            ['sale_id' => 15,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:18:28','updated_at' => '2025-12-10 11:18:28'],
            ['sale_id' => 16,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:19:09','updated_at' => '2025-12-10 11:19:09'],
            ['sale_id' => 17,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:19:31','updated_at' => '2025-12-10 11:19:31'],
            ['sale_id' => 18,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:21:00','updated_at' => '2025-12-10 11:21:00'],
            ['sale_id' => 19,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:22:16','updated_at' => '2025-12-10 11:22:16'],
            ['sale_id' => 20,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:22:33','updated_at' => '2025-12-10 11:22:33'],
            ['sale_id' => 21,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:24:37','updated_at' => '2025-12-10 11:24:37'],
            ['sale_id' => 22,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:30:15','updated_at' => '2025-12-10 11:30:15'],
            ['sale_id' => 23,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:35:20','updated_at' => '2025-12-10 11:35:20'],
            ['sale_id' => 24,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:38:19','updated_at' => '2025-12-10 11:38:19'],
            ['sale_id' => 25,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:42:22','updated_at' => '2025-12-10 11:42:22'],
            ['sale_id' => 26,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:44:06','updated_at' => '2025-12-10 11:44:06'],
            ['sale_id' => 27,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:45:35','updated_at' => '2025-12-10 11:45:35'],
            ['sale_id' => 28,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:49:01','updated_at' => '2025-12-10 11:49:01'],
            ['sale_id' => 29,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:53:03','updated_at' => '2025-12-10 11:53:03'],
            ['sale_id' => 30,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:53:36','updated_at' => '2025-12-10 11:53:36'],
            ['sale_id' => 31,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:54:13','updated_at' => '2025-12-10 11:54:13'],
            ['sale_id' => 32,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:54:38','updated_at' => '2025-12-10 11:54:38'],
            ['sale_id' => 33,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 11:56:48','updated_at' => '2025-12-10 11:56:48'],
            ['sale_id' => 34,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:02:24','updated_at' => '2025-12-10 12:02:24'],
            ['sale_id' => 35,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:04:54','updated_at' => '2025-12-10 12:04:54'],
            ['sale_id' => 36,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:08:08','updated_at' => '2025-12-10 12:08:08'],
            ['sale_id' => 37,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:11:25','updated_at' => '2025-12-10 12:11:25'],
            ['sale_id' => 38,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:13:03','updated_at' => '2025-12-10 12:13:03'],
            ['sale_id' => 39,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:16:44','updated_at' => '2025-12-10 12:16:44'],
            ['sale_id' => 40,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:19:16','updated_at' => '2025-12-10 12:19:16'],
            ['sale_id' => 41,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:28:38','updated_at' => '2025-12-10 12:28:38'],
            ['sale_id' => 42,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:30:56','updated_at' => '2025-12-10 12:30:56'],
            ['sale_id' => 43,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:31:36','updated_at' => '2025-12-10 12:31:36'],
            ['sale_id' => 44,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:33:57','updated_at' => '2025-12-10 12:33:57'],
            ['sale_id' => 45,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:36:09','updated_at' => '2025-12-10 12:36:09'],
            ['sale_id' => 46,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:42:53','updated_at' => '2025-12-10 12:42:53'],
            ['sale_id' => 47,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:43:22','updated_at' => '2025-12-10 12:43:22'],
            ['sale_id' => 48,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:46:51','updated_at' => '2025-12-10 12:46:51'],
            ['sale_id' => 49,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 12:51:03','updated_at' => '2025-12-10 12:51:03'],
            ['sale_id' => 50,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-10 13:17:35','updated_at' => '2025-12-10 13:17:35'],
            ['sale_id' => 51,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-12 14:51:51','updated_at' => '2025-12-12 14:51:51'],
            ['sale_id' => 52,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-12 14:51:52','updated_at' => '2025-12-12 14:51:52'],
            ['sale_id' => 53,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2025-12-12 14:53:04','updated_at' => '2025-12-12 14:53:04'],
            ['sale_id' => 54,'method' => 'cash','amount' => 250000,'change' => 0,'created_at' => '2025-12-15 14:09:53','updated_at' => '2025-12-15 14:09:53'],
            ['sale_id' => 55,'method' => 'cash','amount' => 150000,'change' => 12000,'created_at' => '2025-12-15 14:21:31','updated_at' => '2025-12-15 14:21:31'],
            ['sale_id' => 56,'method' => 'cash','amount' => 5000,'change' => 1500,'created_at' => '2026-01-03 13:45:44','updated_at' => '2026-01-03 13:45:44'],
            ['sale_id' => 57,'method' => 'cash','amount' => 150000,'change' => 1500,'created_at' => '2026-01-26 14:37:40','updated_at' => '2026-01-26 14:37:40'],
            ['sale_id' => 58,'method' => 'cash','amount' => 110000,'change' => 1500,'created_at' => '2026-01-27 02:30:54','updated_at' => '2026-01-27 02:30:54'],
            ['sale_id' => 59,'method' => 'cash','amount' => 130000,'change' => 5000,'created_at' => '2026-01-27 02:52:56','updated_at' => '2026-01-27 02:52:56'],
            ['sale_id' => 60,'method' => 'cash','amount' => 12000,'change' => 0,'created_at' => '2026-01-27 03:07:05','updated_at' => '2026-01-27 03:07:05'],
            ['sale_id' => 61,'method' => 'cash','amount' => 15000,'change' => 1000,'created_at' => '2026-01-30 05:45:06','updated_at' => '2026-01-30 05:45:06'],
            ['sale_id' => 62,'method' => 'cash','amount' => 40000,'change' => 5000,'created_at' => '2026-01-30 05:45:24','updated_at' => '2026-01-30 05:45:24'],
            ['sale_id' => 63,'method' => 'cash','amount' => 30000,'change' => 2000,'created_at' => '2026-02-04 01:28:35','updated_at' => '2026-02-04 01:28:35']
        ];

        // Build full payments array from existing hardcoded rows
        // We'll read the original array from the file content above; to avoid duplication here,
        // ensure the remaining rows are cleaned of 'id' keys programmatically if present.

        // If there are extra rows defined below (manually), append them to $payments
        // then normalize by removing any 'id' keys before insert.

        // For safety, scan and remove 'id' keys on all payment rows
        foreach ($payments as &$p) {
            if (isset($p['id'])) unset($p['id']);
        }

        // Remap sale_id to actual inserted sale id by matching created_at (handles deduped sales)
        foreach ($payments as &$p) {
            if (isset($p['created_at'])) {
                $sale = DB::table('sales')->where('created_at', $p['created_at'])->first();
                if ($sale) {
                    $p['sale_id'] = $sale->id;
                }
            }
        }

        DB::table('payments')->insert($payments);
    }
}