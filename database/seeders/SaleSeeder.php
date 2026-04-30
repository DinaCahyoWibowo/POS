<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates the sales table with initial data.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        try { DB::table('sales')->truncate(); } catch (\Exception $e) {}
        Schema::enableForeignKeyConstraints();

        $sales = [
            [
                'id' => null,
                'code' => 'S202512060001',
                'user_id' => 1,
                'subtotal' => 125000,
                'total' => 125000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-06 05:00:00',
                'updated_at' => '2025-12-06 05:00:00'
            ],
            [
                'id' => null,
                'code' => 'S202512060002',
                'user_id' => 1,
                'subtotal' => 10500,
                'total' => 10500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-06 05:05:13',
                'updated_at' => '2025-12-06 05:05:13'
            ],
            [
                'id' => null,
                'code' => 'S202512100031',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:19:16',
                'updated_at' => '2025-12-10 12:19:16'
            ],
            [
                'code' => 'S202512060004',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-06 07:38:24',
                'updated_at' => '2025-12-06 07:38:24'
            ],
            [
                'code' => 'S202512080005',
                'user_id' => 1,
                'subtotal' => 125000,
                'total' => 125000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-08 04:04:49',
                'updated_at' => '2025-12-08 04:04:49'
            ],
            [
                'code' => 'S202512080006',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-08 11:17:12',
                'updated_at' => '2025-12-08 11:17:12'
            ],
            [
                'code' => 'S202512100001',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 10:10:25',
                'updated_at' => '2025-12-10 10:10:25'
            ],
            [
                'code' => 'S202512100002',
                'user_id' => 1,
                'subtotal' => 125000,
                'total' => 125000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 10:14:32',
                'updated_at' => '2025-12-10 10:14:32'
            ],
            [
                'code' => 'S202512100003',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 10:17:00',
                'updated_at' => '2025-12-10 10:17:00'
            ],
            [
                'code' => 'S202512100004',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 10:18:54',
                'updated_at' => '2025-12-10 10:18:54'
            ],
            [
                'code' => 'S202512100005',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 10:29:10',
                'updated_at' => '2025-12-10 10:29:10'
            ],
            [
                'code' => 'S202512100006',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:18:28',
                'updated_at' => '2025-12-10 11:18:28'
            ],
            [
                'code' => 'S202512100007',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:19:09',
                'updated_at' => '2025-12-10 11:19:09'
            ],
            [
                'code' => 'S202512100008',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:19:31',
                'updated_at' => '2025-12-10 11:19:31'
            ],
            [
                'code' => 'S202512100009',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:21:00',
                'updated_at' => '2025-12-10 11:21:00'
            ],
            [
                'code' => 'S202512100010',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:22:16',
                'updated_at' => '2025-12-10 11:22:16'
            ],
            [
                'code' => 'S202512100011',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:22:33',
                'updated_at' => '2025-12-10 11:22:33'
            ],
            [
                'code' => 'S202512100012',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:24:37',
                'updated_at' => '2025-12-10 11:24:37'
            ],
            [
                'code' => 'S202512100013',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:30:15',
                'updated_at' => '2025-12-10 11:30:15'
            ],
            [
                'code' => 'S202512100014',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:35:20',
                'updated_at' => '2025-12-10 11:35:20'
            ],
            [
                'code' => 'S202512100015',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:38:19',
                'updated_at' => '2025-12-10 11:38:19'
            ],
            [
                'code' => 'S202512100016',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:42:22',
                'updated_at' => '2025-12-10 11:42:22'
            ],
            [
                'code' => 'S202512100017',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:44:06',
                'updated_at' => '2025-12-10 11:44:06'
            ],
            [
                'id' => 27,
                'code' => 'S202512100018',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:45:35',
                'updated_at' => '2025-12-10 11:45:35'
            ],
            [
                'id' => 28,
                'code' => 'S202512100019',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:49:01',
                'updated_at' => '2025-12-10 11:49:01'
            ],
            [
                'id' => 29,
                'code' => 'S202512100020',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:53:03',
                'updated_at' => '2025-12-10 11:53:03'
            ],
            [
                'id' => 30,
                'code' => 'S202512100021',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:53:36',
                'updated_at' => '2025-12-10 11:53:36'
            ],
            [
                'id' => 31,
                'code' => 'S202512100022',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:54:13',
                'updated_at' => '2025-12-10 11:54:13'
            ],
            [
                'id' => 32,
                'code' => 'S202512100023',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:54:38',
                'updated_at' => '2025-12-10 11:54:38'
            ],
            [
                'id' => 33,
                'code' => 'S202512100024',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 11:56:48',
                'updated_at' => '2025-12-10 11:56:48'
            ],
            [
                'id' => 34,
                'code' => 'S202512100025',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:02:24',
                'updated_at' => '2025-12-10 12:02:24'
            ],
            [
                'id' => 35,
                'code' => 'S202512100026',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:04:54',
                'updated_at' => '2025-12-10 12:04:54'
            ],
            [
                'id' => 36,
                'code' => 'S202512100027',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:08:08',
                'updated_at' => '2025-12-10 12:08:08'
            ],
            [
                'id' => 37,
                'code' => 'S202512100028',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:11:25',
                'updated_at' => '2025-12-10 12:11:25'
            ],
            [
                'id' => 38,
                'code' => 'S202512100029',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:13:03',
                'updated_at' => '2025-12-10 12:13:03'
            ],
            [
                'id' => 39,
                'code' => 'S202512100030',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:16:44',
                'updated_at' => '2025-12-10 12:16:44'
            ],
            [
                'id' => 40,
                'code' => 'S202512100031',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:19:16',
                'updated_at' => '2025-12-10 12:19:16'
            ],
            [
                'id' => 41,
                'code' => 'S202512100032',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:28:38',
                'updated_at' => '2025-12-10 12:28:38'
            ],
            [
                'id' => 42,
                'code' => 'S202512100033',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:30:56',
                'updated_at' => '2025-12-10 12:30:56'
            ],
            [
                'id' => 43,
                'code' => 'S202512100034',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:31:36',
                'updated_at' => '2025-12-10 12:31:36'
            ],
            [
                'id' => 44,
                'code' => 'S202512100035',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:33:57',
                'updated_at' => '2025-12-10 12:33:57'
            ],
            [
                'id' => 45,
                'code' => 'S202512100036',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:36:09',
                'updated_at' => '2025-12-10 12:36:09'
            ],
            [
                'id' => 46,
                'code' => 'S202512100037',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:42:53',
                'updated_at' => '2025-12-10 12:42:53'
            ],
            [
                'id' => 47,
                'code' => 'S202512100038',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:43:22',
                'updated_at' => '2025-12-10 12:43:22'
            ],
            [
                'id' => 48,
                'code' => 'S202512100039',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:46:51',
                'updated_at' => '2025-12-10 12:46:51'
            ],
            [
                'id' => 49,
                'code' => 'S202512100040',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 12:51:03',
                'updated_at' => '2025-12-10 12:51:03'
            ],
            [
                'id' => 50,
                'code' => 'S202512100041',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-10 13:17:35',
                'updated_at' => '2025-12-10 13:17:35'
            ],
            [
                'id' => 51,
                'code' => 'S202512120001',
                'user_id' => 3,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-12 14:51:51',
                'updated_at' => '2025-12-12 14:51:51'
            ],
            [
                'id' => 52,
                'code' => 'S202512120002',
                'user_id' => 3,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-12 14:51:52',
                'updated_at' => '2025-12-12 14:51:52'
            ],
            [
                'id' => 53,
                'code' => 'S202512120003',
                'user_id' => 3,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-12 14:53:04',
                'updated_at' => '2025-12-12 14:53:04'
            ],
            [
                'id' => 54,
                'code' => 'S202512150001',
                'user_id' => 3,
                'subtotal' => 250000,
                'total' => 250000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-15 14:09:53',
                'updated_at' => '2025-12-15 14:09:53'
            ],
            [
                'id' => 55,
                'code' => 'S202512150002',
                'user_id' => 1,
                'subtotal' => 138000,
                'total' => 138000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2025-12-15 14:21:31',
                'updated_at' => '2025-12-15 14:21:31'
            ],
            [
                'id' => 56,
                'code' => 'S202601030001',
                'user_id' => 1,
                'subtotal' => 3500,
                'total' => 3500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2026-01-03 13:45:44',
                'updated_at' => '2026-01-03 13:45:44'
            ],
            [
                'id' => 57,
                'code' => 'S202601260001',
                'user_id' => 1,
                'subtotal' => 148500,
                'total' => 148500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2026-01-26 14:37:40',
                'updated_at' => '2026-01-26 14:37:40'
            ],
            [
                'id' => 58,
                'code' => 'S202601270001',
                'user_id' => 1,
                'subtotal' => 108500,
                'total' => 108500,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2026-01-27 02:30:54',
                'updated_at' => '2026-01-27 02:30:54'
            ],
            [
                'id' => 59,
                'code' => 'S202601270002',
                'user_id' => 1,
                'subtotal' => 125000,
                'total' => 125000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2026-01-27 02:52:56',
                'updated_at' => '2026-01-27 02:52:56'
            ],
            [
                'id' => 60,
                'code' => 'S202601270003',
                'user_id' => 1,
                'subtotal' => 12000,
                'total' => 12000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2026-01-27 03:07:05',
                'updated_at' => '2026-01-27 03:07:05'
            ],
            [
                'id' => 61,
                'code' => 'S202601300001',
                'user_id' => 1,
                'subtotal' => 14000,
                'total' => 14000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2026-01-30 05:45:06',
                'updated_at' => '2026-01-30 05:45:06'
            ],
            [
                'id' => 62,
                'code' => 'S202601300002',
                'user_id' => 1,
                'subtotal' => 35000,
                'total' => 35000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2026-01-30 05:45:24',
                'updated_at' => '2026-01-30 05:45:24'
            ],
            [
                'id' => 63,
                'code' => 'S202602040001',
                'user_id' => 1,
                'subtotal' => 28000,
                'total' => 28000,
                'tax' => 0,
                'discount' => 0,
                'status' => 'completed',
                'created_at' => '2026-02-04 01:28:35',
                'updated_at' => '2026-02-04 01:28:35'
            ]
        ];

        // Clean any explicit 'id' keys from source rows (they were present in original dump)
        foreach ($sales as &$s) {
            if (isset($s['id'])) unset($s['id']);
        }
        unset($s);

        // Deduplicate sales by `code` (keep first) to avoid unique-key conflicts
        $seen = [];
        $deduped = [];
        foreach ($sales as $row) {
            if (!isset($row['code'])) {
                $deduped[] = $row;
                continue;
            }
            if (isset($seen[$row['code']])) continue;
            $seen[$row['code']] = true;
            $deduped[] = $row;
        }
        $sales = array_values($deduped);

        // Ensure every sale row has an explicit sequential id (preserve ordering)
        $i = 1;
        foreach ($sales as &$row) {
            $row['id'] = $i++;
        }
        unset($row);

        DB::table('sales')->insert($sales);
    }
}