<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'UD Kasemi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/layout-styles.css" rel="stylesheet">
</head>
<body>
@php
    use Illuminate\Support\Facades\Route;
    // reference the facade to satisfy static analyzers
    $__route_facade = Route::class;
    $cur = $currentUser ?? auth()->user();
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
            UD Kasemi
            @auth
                @php
                    $appMode = request()->query('app_mode') ?: request()->cookie('app_mode') ?: session('app_mode', 'live');
                @endphp
                <small class="text-muted ms-2">({{ $cur->name ?? auth()->user()->name }})</small>
                <span class="badge ms-2 {{ $appMode === 'demo' ? 'bg-warning text-dark' : 'bg-success' }}">{{ $appMode === 'demo' ? 'Demo' : 'Live' }}</span>
            @endauth
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('profile') }}">Profile</a></li>
                    @if($cur && $cur->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.roles.index') }}">Roles</a></li>
                    @endif
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-link nav-link">Logout</button></form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@auth
@php $isPos = request()->routeIs('admin.pos.*'); @endphp
<div class="container-fluid mt-3">
    <div class="row">
        @unless($isPos)
        <nav id="sidebar" class="col-md-2 col-lg-2 d-md-block bg-light sidebar collapse show">
            <div class="position-sticky pt-3">
                @if($cur && in_array($cur->role, ['admin','inventory']))
                <h6 class="px-3">Inventory</h6>
                <div class="list-group list-group-flush">
                    @php
                        $inventoryActive = request()->routeIs('admin.items.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.units.*') || request()->routeIs('admin.stock_movements.*');
                    @endphp
                    <a class="list-group-item list-group-item-action {{ $inventoryActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#inventoryMenu" role="button" aria-expanded="{{ $inventoryActive ? 'true' : 'false' }}" aria-controls="inventoryMenu">
                        Items
                    </a>
                    <div class="collapse {{ $inventoryActive ? 'show' : '' }}" id="inventoryMenu">
                        <div class="list-group">
                            @if(($cur?->role ?? null) === 'admin')
                                <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.items.create') ? 'active' : '' }}" href="{{ route('admin.items.create') }}">New Item</a>
                            @endif
                            <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.items.index') ? 'active' : '' }}" href="{{ route('admin.items.index') }}">Item List</a>
                            @if(($cur?->role ?? null) === 'admin')
                                <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.brands.create') ? 'active' : '' }}" href="{{ route('admin.brands.create') }}">New Brand</a>
                            @endif
                            <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.brands.index') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">Brand List</a>
                            @if(($cur?->role ?? null) === 'admin')
                                <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}" href="{{ route('admin.categories.create') }}">New Category</a>
                            @endif
                            <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Category List</a>
                            @if(auth()->user()?->role === 'admin')
                                <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.units.create') ? 'active' : '' }}" href="{{ route('admin.units.create') }}">New Unit</a>
                            @endif
                            <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.units.index') ? 'active' : '' }}" href="{{ route('admin.units.index') }}">Unit List</a>
                            <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.stock_movements.create') ? 'active' : '' }}" href="{{ route('admin.stock_movements.create') }}">Stock In</a>
                            <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.stock_movements.index') ? 'active' : '' }}" href="{{ route('admin.stock_movements.index') }}">Stock Movements</a>
                            <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.stock_opnames.*') ? 'active' : '' }}" href="{{ route('admin.stock_opnames.index') }}">Stock Opname</a>
                        </div>
                    </div>
                @endif
                
                @if($cur && in_array($cur->role, ['admin','sales']))
                <h6 class="px-3 mt-3">Sales</h6>
                <div class="list-group list-group-flush">
                    @php
                        $salesActive = request()->routeIs('admin.pos.*') || request()->routeIs('admin.sales.*') || request()->routeIs('admin.sales.report') || request()->routeIs('admin.sales.stock');
                    @endphp
                    <a class="list-group-item list-group-item-action {{ $salesActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#salesMenu" role="button" aria-expanded="{{ $salesActive ? 'true' : 'false' }}" aria-controls="salesMenu">
                        Sales
                    </a>
                    <div class="collapse {{ $salesActive ? 'show' : '' }}" id="salesMenu">
                        <div class="list-group">
                            @if(Route::has('admin.pos.index'))
                                <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.pos.index') ? 'active' : '' }}" href="{{ route('admin.pos.index') }}">POS (Point of Sale)</a>
                            @endif
                            @if(Route::has('admin.sales.index'))
                                <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.sales.index') ? 'active' : '' }}" href="{{ route('admin.sales.index') }}">Sales List</a>
                            @endif
                            @if(Route::has('admin.sales.report') && auth()->user()?->role === 'admin')
                                <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.sales.report') ? 'active' : '' }}" href="{{ route('admin.sales.report') }}">Sales Report</a>
                            @endif
                            @if(Route::has('admin.sales.stock') && auth()->user()?->role === 'admin')
                                <a class="list-group-item list-group-item-action ps-4 {{ request()->routeIs('admin.sales.stock') ? 'active' : '' }}" href="{{ route('admin.sales.stock') }}">Stock Report</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </nav>
        @endunless

        <main class="{{ $isPos ? 'col-12' : 'col-md-10 col-lg-10' }} px-md-3" style="display:block !important; visibility:visible !important; opacity:1 !important;">
            @yield('content')
        </main>
    </div>
</div>
@else
<main style="display:block !important; visibility:visible !important; opacity:1 !important;">
    @yield('content')
</main>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/layout-scripts.js"></script>

@yield('scripts')

</body>
</html>