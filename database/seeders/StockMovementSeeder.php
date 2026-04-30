<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the stock_movements table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('stock_movements')->truncate(); } catch (Exception $e) {}
        Schema::enableForeignKeyConstraints();

        DB::table('stock_movements')->insert([
            [
                'id' => 1,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2025-12-06 04:40:36',
                'updated_at' => '2025-12-06 04:40:36'
            ],
            [
                'id' => 2,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 1,
                'note' => 'Sale S202512060001',
                'created_by' => 1,
                'created_at' => '2025-12-06 05:00:00',
                'updated_at' => '2025-12-06 05:00:00'
            ],
            [
                'id' => 3,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 2,
                'note' => 'Sale S202512060002',
                'created_by' => 1,
                'created_at' => '2025-12-06 05:05:13',
                'updated_at' => '2025-12-06 05:05:13'
            ],
            [
                'id' => 4,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2025-12-06 05:27:36',
                'updated_at' => '2025-12-06 05:27:36'
            ],
            [
                'id' => 5,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2025-12-06 05:33:11',
                'updated_at' => '2025-12-06 05:33:11'
            ],
            [
                'id' => 6,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 3,
                'note' => 'Sale S202512060003',
                'created_by' => 1,
                'created_at' => '2025-12-06 05:42:05',
                'updated_at' => '2025-12-06 05:42:05'
            ],
            [
                'id' => 7,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 4,
                'note' => 'Sale S202512060004',
                'created_by' => 1,
                'created_at' => '2025-12-06 07:38:24',
                'updated_at' => '2025-12-06 07:38:24'
            ],
            [
                'id' => 8,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 5,
                'note' => 'Sale S202512080005',
                'created_by' => 1,
                'created_at' => '2025-12-08 04:04:49',
                'updated_at' => '2025-12-08 04:04:49'
            ],
            [
                'id' => 9,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 6,
                'note' => 'Sale S202512080006',
                'created_by' => 1,
                'created_at' => '2025-12-08 11:17:12',
                'updated_at' => '2025-12-08 11:17:12'
            ],
            [
                'id' => 10,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 7,
                'note' => 'Sale S202512100001',
                'created_by' => 1,
                'created_at' => '2025-12-10 10:10:25',
                'updated_at' => '2025-12-10 10:10:25'
            ],
            [
                'id' => 11,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 11,
                'note' => 'Sale S202512100002',
                'created_by' => 1,
                'created_at' => '2025-12-10 10:14:32',
                'updated_at' => '2025-12-10 10:14:32'
            ],
            [
                'id' => 12,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 12,
                'note' => 'Sale S202512100003',
                'created_by' => 1,
                'created_at' => '2025-12-10 10:17:00',
                'updated_at' => '2025-12-10 10:17:00'
            ],
            [
                'id' => 13,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 13,
                'note' => 'Sale S202512100004',
                'created_by' => 1,
                'created_at' => '2025-12-10 10:18:54',
                'updated_at' => '2025-12-10 10:18:54'
            ],
            [
                'id' => 14,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 14,
                'note' => 'Sale S202512100005',
                'created_by' => 1,
                'created_at' => '2025-12-10 10:29:10',
                'updated_at' => '2025-12-10 10:29:10'
            ],
            [
                'id' => 15,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 15,
                'note' => 'Sale S202512100006',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:18:28',
                'updated_at' => '2025-12-10 11:18:28'
            ],
            [
                'id' => 16,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 16,
                'note' => 'Sale S202512100007',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:19:09',
                'updated_at' => '2025-12-10 11:19:09'
            ],
            [
                'id' => 17,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 17,
                'note' => 'Sale S202512100008',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:19:31',
                'updated_at' => '2025-12-10 11:19:31'
            ],
            [
                'id' => 18,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 18,
                'note' => 'Sale S202512100009',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:21:00',
                'updated_at' => '2025-12-10 11:21:00'
            ],
            [
                'id' => 19,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 19,
                'note' => 'Sale S202512100010',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:22:16',
                'updated_at' => '2025-12-10 11:22:16'
            ],
            [
                'id' => 20,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 20,
                'note' => 'Sale S202512100011',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:22:33',
                'updated_at' => '2025-12-10 11:22:33'
            ],
            [
                'id' => 21,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 21,
                'note' => 'Sale S202512100012',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:24:37',
                'updated_at' => '2025-12-10 11:24:37'
            ],
            [
                'id' => 22,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 22,
                'note' => 'Sale S202512100013',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:30:15',
                'updated_at' => '2025-12-10 11:30:15'
            ],
            [
                'id' => 23,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 23,
                'note' => 'Sale S202512100014',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:35:20',
                'updated_at' => '2025-12-10 11:35:20'
            ],
            [
                'id' => 24,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 24,
                'note' => 'Sale S202512100015',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:38:19',
                'updated_at' => '2025-12-10 11:38:19'
            ],
            [
                'id' => 25,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 25,
                'note' => 'Sale S202512100016',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:42:22',
                'updated_at' => '2025-12-10 11:42:22'
            ],
            [
                'id' => 26,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 26,
                'note' => 'Sale S202512100017',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:44:06',
                'updated_at' => '2025-12-10 11:44:06'
            ],
            [
                'id' => 27,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 27,
                'note' => 'Sale S202512100018',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:45:35',
                'updated_at' => '2025-12-10 11:45:35'
            ],
            [
                'id' => 28,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 28,
                'note' => 'Sale S202512100019',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:49:01',
                'updated_at' => '2025-12-10 11:49:01'
            ],
            [
                'id' => 29,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 29,
                'note' => 'Sale S202512100020',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:53:03',
                'updated_at' => '2025-12-10 11:53:03'
            ],
            [
                'id' => 30,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 30,
                'note' => 'Sale S202512100021',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:53:36',
                'updated_at' => '2025-12-10 11:53:36'
            ],
            [
                'id' => 31,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 31,
                'note' => 'Sale S202512100022',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:54:13',
                'updated_at' => '2025-12-10 11:54:13'
            ],
            [
                'id' => 32,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 32,
                'note' => 'Sale S202512100023',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:54:38',
                'updated_at' => '2025-12-10 11:54:38'
            ],
            [
                'id' => 33,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 33,
                'note' => 'Sale S202512100024',
                'created_by' => 1,
                'created_at' => '2025-12-10 11:56:48',
                'updated_at' => '2025-12-10 11:56:48'
            ],
            [
                'id' => 34,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 34,
                'note' => 'Sale S202512100025',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:02:24',
                'updated_at' => '2025-12-10 12:02:24'
            ],
            [
                'id' => 35,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 35,
                'note' => 'Sale S202512100026',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:04:54',
                'updated_at' => '2025-12-10 12:04:54'
            ],
            [
                'id' => 36,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 36,
                'note' => 'Sale S202512100027',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:08:08',
                'updated_at' => '2025-12-10 12:08:08'
            ],
            [
                'id' => 37,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 37,
                'note' => 'Sale S202512100028',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:11:25',
                'updated_at' => '2025-12-10 12:11:25'
            ],
            [
                'id' => 38,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 38,
                'note' => 'Sale S202512100029',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:13:03',
                'updated_at' => '2025-12-10 12:13:03'
            ],
            [
                'id' => 39,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 39,
                'note' => 'Sale S202512100030',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:16:44',
                'updated_at' => '2025-12-10 12:16:44'
            ],
            [
                'id' => 40,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 40,
                'note' => 'Sale S202512100031',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:19:16',
                'updated_at' => '2025-12-10 12:19:16'
            ],
            [
                'id' => 41,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 41,
                'note' => 'Sale S202512100032',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:28:38',
                'updated_at' => '2025-12-10 12:28:38'
            ],
            [
                'id' => 42,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 42,
                'note' => 'Sale S202512100033',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:30:56',
                'updated_at' => '2025-12-10 12:30:56'
            ],
            [
                'id' => 43,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 43,
                'note' => 'Sale S202512100034',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:31:36',
                'updated_at' => '2025-12-10 12:31:36'
            ],
            [
                'id' => 44,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 44,
                'note' => 'Sale S202512100035',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:33:57',
                'updated_at' => '2025-12-10 12:33:57'
            ],
            [
                'id' => 45,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 45,
                'note' => 'Sale S202512100036',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:36:09',
                'updated_at' => '2025-12-10 12:36:09'
            ],
            [
                'id' => 46,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 46,
                'note' => 'Sale S202512100037',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:42:53',
                'updated_at' => '2025-12-10 12:42:53'
            ],
            [
                'id' => 47,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 47,
                'note' => 'Sale S202512100038',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:43:22',
                'updated_at' => '2025-12-10 12:43:22'
            ],
            [
                'id' => 48,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 48,
                'note' => 'Sale S202512100039',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:46:51',
                'updated_at' => '2025-12-10 12:46:51'
            ],
            [
                'id' => 49,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 49,
                'note' => 'Sale S202512100040',
                'created_by' => 1,
                'created_at' => '2025-12-10 12:51:03',
                'updated_at' => '2025-12-10 12:51:03'
            ],
            [
                'id' => 50,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 50,
                'note' => 'Sale S202512100041',
                'created_by' => 1,
                'created_at' => '2025-12-10 13:17:35',
                'updated_at' => '2025-12-10 13:17:35'
            ],
            [
                'id' => 51,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2025-12-11 12:52:15',
                'updated_at' => '2025-12-11 12:52:15'
            ],
            [
                'id' => 52,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 51,
                'note' => 'Sale S202512120001',
                'created_by' => 3,
                'created_at' => '2025-12-12 14:51:51',
                'updated_at' => '2025-12-12 14:51:51'
            ],
            [
                'id' => 53,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 52,
                'note' => 'Sale S202512120002',
                'created_by' => 3,
                'created_at' => '2025-12-12 14:51:52',
                'updated_at' => '2025-12-12 14:51:52'
            ],
            [
                'id' => 54,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 53,
                'note' => 'Sale S202512120003',
                'created_by' => 3,
                'created_at' => '2025-12-12 14:53:04',
                'updated_at' => '2025-12-12 14:53:04'
            ],
            [
                'id' => 55,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 2,
                'created_at' => '2025-12-15 14:09:18',
                'updated_at' => '2025-12-15 14:09:18'
            ],
            [
                'id' => 56,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 54,
                'note' => 'Sale S202512150001',
                'created_by' => 3,
                'created_at' => '2025-12-15 14:09:53',
                'updated_at' => '2025-12-15 14:09:53'
            ],
            [
                'id' => 57,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2025-12-15 14:20:02',
                'updated_at' => '2025-12-15 14:20:02'
            ],
            [
                'id' => 58,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 55,
                'note' => 'Sale S202512150002',
                'created_by' => 1,
                'created_at' => '2025-12-15 14:21:31',
                'updated_at' => '2025-12-15 14:21:31'
            ],
            [
                'id' => 59,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2025-12-17 12:36:19',
                'updated_at' => '2025-12-17 12:36:19'
            ],
            [
                'id' => 60,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2025-12-17 12:36:43',
                'updated_at' => '2025-12-17 12:36:43'
            ],
            [
                'id' => 61,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 56,
                'note' => 'Sale S202601030001',
                'created_by' => 1,
                'created_at' => '2026-01-03 13:45:44',
                'updated_at' => '2026-01-03 13:45:44'
            ],
            [
                'id' => 62,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 57,
                'note' => 'Sale S202601260001',
                'created_by' => 1,
                'created_at' => '2026-01-26 14:37:40',
                'updated_at' => '2026-01-26 14:37:40'
            ],
            [
                'id' => 63,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2026-01-27 02:29:14',
                'updated_at' => '2026-01-27 02:29:14'
            ],
            [
                'id' => 64,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 58,
                'note' => 'Sale S202601270001',
                'created_by' => 1,
                'created_at' => '2026-01-27 02:30:54',
                'updated_at' => '2026-01-27 02:30:54'
            ],
            [
                'id' => 65,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2026-01-27 02:49:41',
                'updated_at' => '2026-01-27 02:49:41'
            ],
            [
                'id' => 66,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 59,
                'note' => 'Sale S202601270002',
                'created_by' => 1,
                'created_at' => '2026-01-27 02:52:56',
                'updated_at' => '2026-01-27 02:52:56'
            ],
            [
                'id' => 67,
                'type' => 'in',
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock IN',
                'created_by' => 1,
                'created_at' => '2026-01-27 03:04:37',
                'updated_at' => '2026-01-27 03:04:37'
            ],
            [
                'id' => 68,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 60,
                'note' => 'Sale S202601270003',
                'created_by' => 1,
                'created_at' => '2026-01-27 03:07:05',
                'updated_at' => '2026-01-27 03:07:05'
            ],
            [
                'id' => 69,
                'type' => 'opname',
                'reference_type' => 'App\\Models\\StockOpname',
                'reference_id' => 1,
                'note' => 'Opname OPN20260129N5MA',
                'created_by' => 1,
                'created_at' => '2026-01-29 14:23:29',
                'updated_at' => '2026-01-29 14:23:29'
            ],
            [
                'id' => 70,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 61,
                'note' => 'Sale S202601300001',
                'created_by' => 1,
                'created_at' => '2026-01-30 05:45:06',
                'updated_at' => '2026-01-30 05:45:06'
            ],
            [
                'id' => 71,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 62,
                'note' => 'Sale S202601300002',
                'created_by' => 1,
                'created_at' => '2026-01-30 05:45:24',
                'updated_at' => '2026-01-30 05:45:24'
            ],
            [
                'id' => 72,
                'type' => 'sale',
                'reference_type' => 'App\\Models\\Sale',
                'reference_id' => 63,
                'note' => 'Sale S202602040001',
                'created_by' => 1,
                'created_at' => '2026-02-04 01:28:35',
                'updated_at' => '2026-02-04 01:28:35'
            ]
        ]);
    }
}