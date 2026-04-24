@extends('layouts.app')

@section('content')
<div class="dashboard-area mt-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-sm btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle sidebar">
                Menu
            </button>
        </div>
        
    </div>
        

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 dashboard-icon" style="background:#00bcd4">
                            <i class="bi bi-bag" style="font-size:24px"></i>
                        </div>
                        <div>
                            <div class="small text-muted">TODAY'S TOTAL PURCHASE</div>
                            <div class="h5 number">{{ 'Rp '.number_format($todayPurchase ?? 0,0,',','.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 dashboard-icon" style="background:#28a745">
                            <i class="bi bi-cart-plus" style="font-size:24px"></i>
                        </div>
                        <div>
                            <div class="small text-muted">TODAY'S TOTAL SALES</div>
                            <div class="h5 number">{{ 'Rp '.number_format($todaySales ?? 0,0,',','.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 dashboard-icon" style="background:#ffc107">
                            <i class="bi bi-cash-stack" style="font-size:24px"></i>
                        </div>
                        <div>
                            <div class="small text-muted">TODAY'S PROFIT</div>
                            <div class="h5 number">{{ 'Rp '.number_format($todayProfitCurrentCost ?? 0,0,',','.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Welcome card removed per request -->
        </div>
        
        

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 dashboard-icon" style="background:#00bcd4">
                            <i class="bi bi-bag" style="font-size:24px"></i>
                        </div>
                        <div>
                            <div class="small text-muted">MONTH TOTAL PURCHASE</div>
                            <div class="h5 number">{{ 'Rp '.number_format($monthPurchase ?? 0,0,',','.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 dashboard-icon" style="background:#28a745">
                            <i class="bi bi-cart-plus" style="font-size:24px"></i>
                        </div>
                        <div>
                            <div class="small text-muted">MONTH TOTAL SALES</div>
                            <div class="h5 number">{{ 'Rp '.number_format($monthSales ?? 0,0,',','.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 dashboard-icon" style="background:#ffc107">
                            <i class="bi bi-cash-stack" style="font-size:24px"></i>
                        </div>
                        <div>
                            <div class="small text-muted">MONTH PROFIT</div>
                            <div class="h5 number">{{ 'Rp '.number_format($monthProfitCurrentCost ?? 0,0,',','.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">STOCK ALERT <small class="text-muted">(10 items lowest stock)</small></div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Item Name</th>
                                    <th>Category Name</th>
                                    <th style="width:110px">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockItems as $i => $it)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $it->item_name }}</td>
                                        <td>{{ $it->category_name }}</td>
                                        <td class="text-end">{{ number_format($it->stock ?? 0, 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No items found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">TOP 10 TRENDING ITEMS <small class="text-muted">(this month)</small></div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Item Name</th>
                                    <th style="width:130px">Qty Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trendingItems as $j => $t)
                                    <tr>
                                        <td>{{ $j+1 }}</td>
                                        <td>{{ $t->item_name }}</td>
                                        <td class="text-end">{{ number_format($t->qty_sold ?? 0, 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center">No sales yet this month</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3 mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">PURCHASE &amp; SALES BAR CHART</div>
                    <div class="card-body">
                        <canvas id="purchaseSalesChart" height="220"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function(){
            const labels = {!! json_encode($months ?? []) !!};
            const sales = {!! json_encode($chartSales ?? []) !!};
            const purchases = {!! json_encode($chartPurchases ?? []) !!};

            const ctx = document.getElementById('purchaseSalesChart');
            if (ctx){
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Purchase (cost at sale time)', data: purchases, backgroundColor: '#bfc6cc' },
                            { label: 'Sales (sale price at time)', data: sales, backgroundColor: '#16a34a' }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: function(v){ return v.toLocaleString(); } } }
                        },
                        interaction: { mode: 'index', intersect: false }
                    }
                });
            }
        })();
    </script>
@endsection
