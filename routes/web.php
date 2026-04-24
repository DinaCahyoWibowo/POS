<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    // instantiate the controller and call the instance method instead of calling statically
    return app()->make(App\Http\Controllers\AuthController::class)->showLogin();
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    // compute today's and this month's totals for dashboard
    $tz = config('app.timezone');
    $todayStart = \Carbon\Carbon::now($tz)->startOfDay();
    $todayEnd = \Carbon\Carbon::now($tz)->endOfDay();

    $monthStart = \Carbon\Carbon::now($tz)->startOfMonth();
    $monthEnd = \Carbon\Carbon::now($tz)->endOfMonth();

    // today's totals
    $todaySales = \App\Models\Sale::whereBetween('created_at', [$todayStart, $todayEnd])->sum('total');
    // purchase should account for unit factor (convert qty to base units)
    $todayPurchase = \DB::table('sale_items')
        ->join('sales','sale_items.sale_id','=','sales.id')
        ->leftJoin('item_units as iu', function($join){
            $join->on('iu.item_id','=','sale_items.item_id')->on('iu.unit_id','=','sale_items.unit_id');
        })
        ->whereBetween('sales.created_at', [$todayStart, $todayEnd])
        ->selectRaw('COALESCE(SUM(COALESCE(sale_items.cost_total, sale_items.cost_price * sale_items.qty * COALESCE(iu.factor,1))),0) as total')
        ->value('total');
    $todayProfit = ($todaySales - $todayPurchase);

    // compute today's purchase using CURRENT item cost (to see profit/loss against current cost)
    $todayCurrentPurchase = \DB::table('sale_items')
        ->join('sales','sale_items.sale_id','=','sales.id')
        ->leftJoin('item_units as iu', function($join){
            $join->on('iu.item_id','=','sale_items.item_id')->on('iu.unit_id','=','sale_items.unit_id');
        })
        ->join('items as it','sale_items.item_id','=','it.id')
        ->whereBetween('sales.created_at', [$todayStart, $todayEnd])
        ->selectRaw('COALESCE(SUM(it.cost_price * sale_items.qty * COALESCE(sale_items.unit_factor, iu.factor,1)),0) as total')
        ->value('total');
    $todayProfitCurrentCost = ($todaySales - $todayCurrentPurchase);

    // this month's totals
    $monthSales = \App\Models\Sale::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total');
    $monthPurchase = \DB::table('sale_items')
        ->join('sales','sale_items.sale_id','=','sales.id')
        ->leftJoin('item_units as iu', function($join){
            $join->on('iu.item_id','=','sale_items.item_id')->on('iu.unit_id','=','sale_items.unit_id');
        })
        ->whereBetween('sales.created_at', [$monthStart, $monthEnd])
        ->selectRaw('COALESCE(SUM(COALESCE(sale_items.cost_total, sale_items.cost_price * sale_items.qty * COALESCE(iu.factor,1))),0) as total')
        ->value('total');
    $monthProfit = ($monthSales - $monthPurchase);

    // compute month's purchase using CURRENT item cost
    $monthCurrentPurchase = \DB::table('sale_items')
        ->join('sales','sale_items.sale_id','=','sales.id')
        ->leftJoin('item_units as iu', function($join){
            $join->on('iu.item_id','=','sale_items.item_id')->on('iu.unit_id','=','sale_items.unit_id');
        })
        ->join('items as it','sale_items.item_id','=','it.id')
        ->whereBetween('sales.created_at', [$monthStart, $monthEnd])
        ->selectRaw('COALESCE(SUM(it.cost_price * sale_items.qty * COALESCE(sale_items.unit_factor, iu.factor,1)),0) as total')
        ->value('total');
    $monthProfitCurrentCost = ($monthSales - $monthCurrentPurchase);

    // Build monthly series for the current year for the Purchase & Sales bar chart
    $year = \Carbon\Carbon::now($tz)->year;
    // initialize arrays for 12 months
    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $salesByMonth = array_fill(1,12,0);
    $purchasesByMonth = array_fill(1,12,0);

    // sales totals by month (uses sales.total which reflects sale price at time of sale)
    $salesRows = \DB::table('sales')
        ->selectRaw('MONTH(created_at) as m, COALESCE(SUM(total),0) as total')
        ->whereYear('created_at', $year)
        ->groupByRaw('MONTH(created_at)')
        ->get();
    foreach($salesRows as $r) { $salesByMonth[(int)$r->m] = (float) $r->total; }

    // purchases (cost) by month: sum per-sale cost_total when available, else compute from cost_price * qty * factor
    $purchaseRows = \DB::table('sale_items as si')
        ->join('sales as s', 'si.sale_id', '=', 's.id')
        ->leftJoin('item_units as iu', function($join){
            $join->on('iu.item_id', '=', 'si.item_id')->on('iu.unit_id', '=', 'si.unit_id');
        })
        ->whereYear('s.created_at', $year)
        ->selectRaw('MONTH(s.created_at) as m, COALESCE(SUM(COALESCE(si.cost_total, si.cost_price * si.qty * COALESCE(si.unit_factor, iu.factor,1))),0) as total')
        ->groupByRaw('MONTH(s.created_at)')
        ->get();
    foreach($purchaseRows as $r) { $purchasesByMonth[(int)$r->m] = (float) $r->total; }

    // Prepare JSON-serializable arrays in order Jan..Dec
    $chartSales = [];
    $chartPurchases = [];
    for ($i=1;$i<=12;$i++){
        $chartSales[] = $salesByMonth[$i] ?? 0;
        $chartPurchases[] = $purchasesByMonth[$i] ?? 0;
    }

    // Low-stock items: compute current stock by summing stock_movement_lines.qty (base units)
    $lowStockItems = \DB::table('items as it')
        ->leftJoin('stock_movement_lines as sml', 'it.id', '=', 'sml.item_id')
        ->leftJoin('categories as c', 'it.category_id', '=', 'c.id')
        ->selectRaw('it.id, it.name as item_name, c.name as category_name, COALESCE(SUM(sml.qty),0) as stock')
        ->groupBy('it.id', 'it.name', 'c.name')
        ->orderByRaw('stock asc')
        ->limit(10)
        ->get();

    // Trending items for this month: most sold (in base units) so far this month
    $trendingItems = \DB::table('sale_items as si')
        ->join('sales as s', 'si.sale_id', '=', 's.id')
        ->join('items as it', 'si.item_id', '=', 'it.id')
        ->leftJoin('item_units as iu', function($join){
            $join->on('iu.item_id', '=', 'si.item_id')->on('iu.unit_id', '=', 'si.unit_id');
        })
        ->whereBetween('s.created_at', [$monthStart, $monthEnd])
        ->selectRaw('it.id, it.name as item_name, COALESCE(SUM(si.qty * COALESCE(si.unit_factor, iu.factor, 1)),0) as qty_sold')
        ->groupBy('it.id', 'it.name')
        ->orderByDesc('qty_sold')
        ->limit(10)
        ->get();

    return view('dashboard', compact(
        'todaySales','todayPurchase','todayProfit','monthSales','monthPurchase','monthProfit',
        'todayCurrentPurchase','todayProfitCurrentCost','monthCurrentPurchase','monthProfitCurrentCost',
        'lowStockItems','trendingItems', 'months','chartSales','chartPurchases'
    ));
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        // (debug route removed)
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/bulk-delete', [\App\Http\Controllers\Admin\UserController::class, 'bulkDelete'])->name('users.bulkDelete');
    Route::post('users/{user}/password', [\App\Http\Controllers\Admin\UserController::class, 'updatePassword'])->name('users.updatePassword');

    // Roles management
    Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');

    // Inventory: categories, brands, units, items
    Route::get('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('brands', [\App\Http\Controllers\Admin\BrandController::class, 'index'])->name('brands.index');
    Route::get('brands/create', [\App\Http\Controllers\Admin\BrandController::class, 'create'])->name('brands.create');
    Route::post('brands', [\App\Http\Controllers\Admin\BrandController::class, 'store'])->name('brands.store');
    Route::get('brands/{brand}/edit', [\App\Http\Controllers\Admin\BrandController::class, 'edit'])->name('brands.edit');
    Route::put('brands/{brand}', [\App\Http\Controllers\Admin\BrandController::class, 'update'])->name('brands.update');
    Route::delete('brands/{brand}', [\App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('brands.destroy');

    Route::get('units', [\App\Http\Controllers\Admin\UnitController::class, 'index'])->name('units.index');
    Route::get('units/create', [\App\Http\Controllers\Admin\UnitController::class, 'create'])->name('units.create');
    Route::post('units', [\App\Http\Controllers\Admin\UnitController::class, 'store'])->name('units.store');
    Route::get('units/{unit}/edit', [\App\Http\Controllers\Admin\UnitController::class, 'edit'])->name('units.edit');
    Route::put('units/{unit}', [\App\Http\Controllers\Admin\UnitController::class, 'update'])->name('units.update');
    Route::delete('units/{unit}', [\App\Http\Controllers\Admin\UnitController::class, 'destroy'])->name('units.destroy');

    Route::get('items', [\App\Http\Controllers\Admin\ItemController::class, 'index'])->name('items.index');
    Route::get('items/create', [\App\Http\Controllers\Admin\ItemController::class, 'create'])->name('items.create');
    Route::post('items', [\App\Http\Controllers\Admin\ItemController::class, 'store'])->name('items.store');
    Route::get('items/{item}/edit', [\App\Http\Controllers\Admin\ItemController::class, 'edit'])->name('items.edit');
    Route::put('items/{item}', [\App\Http\Controllers\Admin\ItemController::class, 'update'])->name('items.update');
    Route::delete('items/{item}', [\App\Http\Controllers\Admin\ItemController::class, 'destroy'])->name('items.destroy');
    
    // POS routes
    Route::get('pos', [\App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos.index');
    Route::post('pos/sale', [\App\Http\Controllers\Admin\PosController::class, 'storeSale'])->name('pos.sale');
    
    // Sales management
    Route::get('sales', [\App\Http\Controllers\Admin\SalesController::class, 'index'])->name('sales.index');
    // Plain debug sales table (no layout/JS) to isolate client issues
    Route::get('sales/plain', function(\Illuminate\Http\Request $request){
        $query = \App\Models\Sale::with(['items.item','payments'])->orderBy('created_at','desc');
        $sales = $query->paginate(25);
        return view('admin.sales.plain_table', compact('sales'));
    })->name('sales.plain');
    // Stock Report (static route) - register before dynamic sales/{sale} to avoid route collision
    Route::get('sales/stock-report', [\App\Http\Controllers\Admin\ReportController::class, 'stockReport'])->name('sales.stock')->middleware('role:admin');
    Route::get('sales/{sale}/receipt', [\App\Http\Controllers\Admin\SalesController::class, 'receipt'])->name('sales.receipt');
    Route::get('sales/{sale}', [\App\Http\Controllers\Admin\SalesController::class, 'show'])->name('sales.show');
    Route::get('sales-report', [\App\Http\Controllers\Admin\ReportController::class, 'salesReport'])->name('sales.report')->middleware('role:admin');
    
    // Stock movements (Stock IN)
    Route::get('stock-movements/create', [\App\Http\Controllers\Admin\StockMovementController::class, 'create'])->name('stock_movements.create');
    Route::post('stock-movements', [\App\Http\Controllers\Admin\StockMovementController::class, 'store'])->name('stock_movements.store');
    Route::get('stock-movements', [\App\Http\Controllers\Admin\StockMovementController::class, 'index'])->name('stock_movements.index');
    Route::get('stock-movements/{stock_movement}', [\App\Http\Controllers\Admin\StockMovementController::class, 'show'])->name('stock_movements.show');
    // Stock Opname
    Route::get('stock-opnames', [\App\Http\Controllers\Admin\StockOpnameController::class, 'index'])->name('stock_opnames.index');
    Route::get('stock-opnames/create', [\App\Http\Controllers\Admin\StockOpnameController::class, 'create'])->name('stock_opnames.create');
    Route::post('stock-opnames', [\App\Http\Controllers\Admin\StockOpnameController::class, 'store'])->name('stock_opnames.store');
    Route::get('stock-opnames/{stock_opname}', [\App\Http\Controllers\Admin\StockOpnameController::class, 'show'])->name('stock_opnames.show');
});
