<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$sql = <<<SQL
SELECT it.id as item_id, it.name as item_name,
  COALESCE(SUM(CASE WHEN sm.type = 'in' THEN sml.qty ELSE 0 END),0) AS total_in,
  COALESCE(SUM(CASE WHEN sm.type = 'sale' THEN ABS(sml.qty) ELSE 0 END),0) AS total_out,
  (COALESCE(SUM(CASE WHEN sm.type = 'in' THEN sml.qty ELSE 0 END),0) - COALESCE(SUM(CASE WHEN sm.type = 'sale' THEN ABS(sml.qty) ELSE 0 END),0)) as remaining
FROM stock_movement_lines sml
JOIN stock_movements sm ON sml.stock_movement_id = sm.id
JOIN items it ON sml.item_id = it.id
GROUP BY it.id, it.name
ORDER BY it.name;
SQL;

$rows = DB::select($sql);

echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
