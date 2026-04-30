<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'UD Kasemi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
@php $cur = $currentUser ?? auth()->user(); @endphp
<!-- Temporary debug UI removed -->
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
                            {{-- Stock Report moved to Sales menu --}}
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
<!-- Shared table styles and client-side sorting (applies to any table with <th class="sortable">) -->
<style>
    /* Keep top navbar fixed and avoid content overlap */
    body { padding-top: 56px; }
    .navbar.fixed-top { z-index: 1050; }

    /* Prevent admin two-column layout from vertically stretching columns
       which can create a large empty area when one column is taller. */
    .container-fluid.mt-3 > .row { align-items: flex-start; }

     /* Avoid extra top margin inside `main` when views also use `.container.mt-4`.
         Some views nest a container inside `main` which can combine with other
         layout rules and push content downward for some roles. Reset it here. */
     .container-fluid.mt-3 > .row > main > .container.mt-4 { margin-top: 0 !important; padding-top: 0 !important; }

    .table.table-bordered { border: 1px solid rgba(0,0,0,.125); }
    .table thead th.sortable { cursor: pointer; }
    .table thead th.sortable a { text-decoration: none; color: inherit; }
    .table thead th.sortable a .bi { font-size: .9rem; opacity: .75; }
</style>
<script>
    (function(){
        // Generic client-side sorting for tables. Uses th.sortable; anchor href is fallback.
        function getCellValue(row, idx){
            const cells = row.children;
            if (!cells[idx]) return '';
            return cells[idx].innerText.trim();
        }

        function comparer(idx, type, asc){
            return function(a,b){
                let v1 = getCellValue(asc ? a : b, idx);
                let v2 = getCellValue(asc ? b : a, idx);
                if (type === 'number'){
                    v1 = parseInt(v1) || 0; v2 = parseInt(v2) || 0;
                    return v1 - v2;
                }
                return v1.localeCompare(v2, undefined, {numeric: true, sensitivity: 'base'});
            };
        }

        document.addEventListener('click', function(e){
            // handle clicks on th.sortable (anywhere in the cell)
            const th = e.target.closest('th.sortable');
            if (!th) return;
            // allow fallback if modifier keys used
            if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            const table = th.closest('table');
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            e.preventDefault();

            const colIndex = Array.from(th.parentNode.children).indexOf(th);
            const anchor = th.querySelector('a.js-sort');
            if (!anchor) return;
            const icon = anchor.querySelector('i.bi');
            const type = th.dataset.type || 'string';

            const currentlyAsc = icon.classList.contains('bi-arrow-up');
            const asc = !currentlyAsc;

            // clear icons
            table.querySelectorAll('th.sortable a.js-sort i.bi').forEach(ic => { ic.className = 'bi'; });
            if (asc) icon.classList.add('bi-arrow-up'); else icon.classList.add('bi-arrow-down');

            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort(comparer(colIndex, type, asc));
            rows.forEach(r => tbody.appendChild(r));
        });
    })();
</script>
<style>
    /* Tighter sidebar list spacing and slightly narrower sticky padding */
    .sidebar .list-group .list-group-item { padding-left: .9rem !important; padding-right: .9rem !important; }
    .sidebar .position-sticky { padding-top: .5rem !important; }
    /* Reduce main horizontal padding so content sits nearer sidebar */
    main.px-md-3 { padding-left: .75rem !important; padding-right: .75rem !important; }
</style>
<style>
    /* Make the left sidebar sticky on medium+ screens so it stays visible while scrolling */
    @media (min-width: 768px) {
        #sidebar { position: sticky; top: 56px; height: calc(100vh - 56px); overflow-y: auto; }
        /* ensure inner sticky padding remains as expected */
        #sidebar .position-sticky { top: 0; }
    }
</style>
<style>
    /* Ensure main column always starts at top next to sidebar */
    .container-fluid.mt-3 > .row > main { position: relative; top: 0 !important; }
</style>

<style>
    /* Dashboard table grid and header styling to improve column borders */
    .dashboard-area table.table { border-collapse: collapse; width: 100%; }
    .dashboard-area table.table th,
    .dashboard-area table.table td { border: 1px solid #cfdfe9; }
    .dashboard-area table.table thead th { background: #0b74a8; color: #fff; font-weight: 600; }
    .dashboard-area table.table thead th:first-child { text-align: center; }
    .dashboard-area table.table tbody td { background: #fff; }
    .dashboard-area .card-header { background: #0b74a8; color: #fff; font-weight:600; }
    .dashboard-area table.table td, .dashboard-area table.table th { padding: .55rem .75rem; }
</style>
<style>
    /* Dashboard specific fixed sizing so large numbers don't grow cards */
    .dashboard-area .card .card-body { min-height: 88px; }
    .dashboard-area .number { white-space: normal; overflow: visible; text-overflow: clip; max-width: none; display: block; font-size:1.1rem; line-height:1.1; }
    .dashboard-area .dashboard-icon { width:56px; height:56px; display:flex; align-items:center; justify-content:center; border-radius:6px; color:#fff }
</style>
<!-- Auto-injected Export toolbar for all tables -->
<style>
    .export-toolbar { display:flex; gap:.5rem; justify-content:flex-end; margin:.5rem 0; }
    .export-toolbar .btn-group > .btn { padding:.25rem .5rem; }
    @media print { .export-toolbar { display:none !important; } }
    .export-print { font-size:11px; }
    .export-print table { width:100%; border-collapse:collapse; }
    .export-print th, .export-print td { border:1px solid #000; padding:4px; font-size:11px; }
    .export-print thead th { background:#eee; }
    .export-print h4 { margin:4px 0 8px; }
    .export-print .meta { font-size:10px; margin-bottom:6px; color:#333; }
  </style>
<script>
    (function(){
        function fitDashboardNumbers(){
            document.querySelectorAll('.dashboard-area .number').forEach(function(el){
                // reset any previous sizing
                el.style.fontSize = '';
                var cardBody = el.closest('.card-body');
                if (!cardBody) return;
                var icon = cardBody.querySelector('.dashboard-icon');
                var iconWidth = icon ? icon.offsetWidth + 16 : 0; // gap
                var avail = cardBody.clientWidth - iconWidth - 24; // padding
                // start from computed font-size
                var cs = window.getComputedStyle(el);
                var fs = parseFloat(cs.fontSize) || 16;
                var minFs = 10; // minimum
                // If it already fits, no change
                if (el.scrollWidth <= avail) return;
                // reduce font size until it fits or reaches min
                while (el.scrollWidth > avail && fs > minFs){
                    fs -= 1;
                    el.style.fontSize = fs + 'px';
                }
            });
        }
        document.addEventListener('DOMContentLoaded', fitDashboardNumbers);
        window.addEventListener('resize', function(){ clearTimeout(window._fitDashTimer); window._fitDashTimer = setTimeout(fitDashboardNumbers, 120); });
    })();
</script>
<script>
    (function(){
        function downloadBlob(content, mime, filename){
            const blob = new Blob([content], {type: mime});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = filename; a.style.display='none';
            document.body.appendChild(a); a.click();
            setTimeout(()=>{ URL.revokeObjectURL(url); a.remove(); }, 0);
        }

        function inferName(table){
            const explicit = table.getAttribute('data-export-name');
            if (explicit) return explicit;
            const h = document.querySelector('main h1, main h2, main h3');
            if (h && h.textContent.trim()) return h.textContent.trim().replace(/\s+/g,'_').toLowerCase();
            return 'table_export';
        }

        function buildIncludedIndexes(table){
            const headerRow = table.querySelector('thead tr') || table.querySelector('tr');
            const headers = headerRow ? Array.from(headerRow.children) : [];
            const include = headers.map(h => {
                const txt = (h.innerText || '').toString().trim().toLowerCase();
                if (h.classList.contains('no-export')) return false;
                if (txt.includes('action')) return false;
                return true;
            });
            // if headers not found, default include all
            if (headers.length === 0) return null;
            return include.map((v,i)=> v ? i : -1).filter(i=> i >= 0);
        }

        function tableToCSV(table){
            const rows = Array.from(table.querySelectorAll('tr'));
            const included = buildIncludedIndexes(table);
            const escape = (v)=> '"'+ String(v).replace(/"/g,'""') +'"';
            const getText = (cell)=> cell.innerText.replace(/\n/g,' ').trim();
            if (!included) {
                return rows.map(r => Array.from(r.children).map(getText).map(escape).join(',')).join('\r\n');
            }
            return rows.map(r => included.map(i => getText(r.children[i] || '')).map(escape).join(',')).join('\r\n');
        }

        function exportCSV(table){
            const name = inferName(table) + '_' + new Date().toISOString().slice(0,19).replace(/[:T]/g,'-') + '.csv';
            const csv = tableToCSV(table);
            downloadBlob('\uFEFF' + csv, 'text/csv;charset=utf-8', name);
        }

        function exportExcel(table){
            const name = inferName(table) + '_' + new Date().toISOString().slice(0,19).replace(/[:T]/g,'-') + '.xls';
            const included = buildIncludedIndexes(table);
            let clone = table.cloneNode(true);
            if (included) {
                Array.from(clone.querySelectorAll('tr')).forEach(r => {
                    Array.from(r.children).forEach((cell, idx) => {
                        if (!included.includes(idx)) cell.remove();
                    });
                });
            }
            const html = `<!DOCTYPE html><html><head><meta charset="utf-8" /></head><body>${clone.outerHTML}</body></html>`;
            downloadBlob(html, 'application/vnd.ms-excel', name);
        }

        function exportPDF(table){
            const title = (document.querySelector('main h1, main h2, main h3')?.textContent || 'Export') +
                          ' - ' + new Date().toLocaleString();
            const css = `body{font-family:Arial,Helvetica,sans-serif;}
                table{width:100%;border-collapse:collapse;}
                th,td{border:1px solid #000;padding:4px;font-size:11px}
                thead th{background:#eee}
                h4{margin:4px 0 8px}
                .meta{font-size:10px;margin-bottom:6px;color:#333}`;
            const included = buildIncludedIndexes(table);
            let clone = table.cloneNode(true);
            if (included) {
                Array.from(clone.querySelectorAll('tr')).forEach(r => {
                    Array.from(r.children).forEach((cell, idx) => {
                        if (!included.includes(idx)) cell.remove();
                    });
                });
            }
            const w = window.open('', '_blank');
            if (!w) return alert('Pop-up blocked. Please allow pop-ups.');
            w.document.write(`<!doctype html><html><head><meta charset="utf-8"><title>${title}</title>
                <style>${css}</style>
            </head><body>
                <h4>${title}</h4>
                ${clone.outerHTML}
                <script>window.addEventListener('load', function(){ setTimeout(function(){ window.print(); }, 300); });<\/script>
            </body></html>`);
            w.document.close();
        }

        function addToolbar(table){
            // don't add export toolbar for dashboard widgets
            if (table.closest && table.closest('.dashboard-area')) return;
            if (table.dataset.exportToolbarInjected) return;
            const toolbar = document.createElement('div');
            toolbar.className = 'export-toolbar';
            toolbar.innerHTML = `
                <div class="btn-group" role="group" aria-label="Export">
                    <button type="button" class="btn btn-sm btn-outline-secondary js-exp-csv"><i class="bi bi-filetype-csv"></i> CSV</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary js-exp-xls"><i class="bi bi-file-earmark-excel"></i> Excel</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary js-exp-pdf"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                </div>`;
            table.parentNode.insertBefore(toolbar, table);
            // If table requests server-side export, navigate to export URL (full filtered dataset)
                if (table.dataset.serverExport === '1') {
                // build export URL including filter form values (e.g. from/to)
                function buildExportUrl(fmt){
                    const u = new URL(window.location.href);
                    const f = document.querySelector('main form[method="get"], main form');
                    if (f) {
                        Array.from(f.querySelectorAll('input[name], select[name], textarea[name]')).forEach(el => {
                            if (!el.name) return;
                            const v = (el.type === 'checkbox' || el.type === 'radio') ? (el.checked ? el.value : '') : el.value;
                            if (v !== undefined && v !== null && String(v).trim() !== '') u.searchParams.set(el.name, v);
                            else u.searchParams.delete(el.name);
                        });
                    }
                    u.searchParams.set('export', fmt); u.searchParams.delete('page');
                    return u.toString();
                }

                toolbar.querySelector('.js-exp-csv').addEventListener('click', ()=>{ window.location = buildExportUrl('csv'); });
                toolbar.querySelector('.js-exp-xls').addEventListener('click', ()=>{ window.location = buildExportUrl('xls'); });
                // request server printable export for PDF (HTML that auto-triggers print)
                toolbar.querySelector('.js-exp-pdf').addEventListener('click', ()=>{ window.location = buildExportUrl('pdf'); });
            } else {
                toolbar.querySelector('.js-exp-csv').addEventListener('click', ()=> exportCSV(table));
                toolbar.querySelector('.js-exp-xls').addEventListener('click', ()=> exportExcel(table));
                toolbar.querySelector('.js-exp-pdf').addEventListener('click', ()=> exportPDF(table));
            }
            table.dataset.exportToolbarInjected = '1';
        }

        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('main table.table').forEach(addToolbar);

            // Ensure only one sidebar submenu is open at a time
            var inventory = document.getElementById('inventoryMenu');
            var sales = document.getElementById('salesMenu');
            var menus = [inventory, sales].filter(Boolean);

            // If multiple menus are opened on load (race or markup), collapse extras
            (function normalizeInitialMenus(){
                var shown = menus.filter(m => m.classList.contains('show'));
                if (shown.length > 1) {
                    // prefer to keep the first shown or collapse all; collapse all for predictable UX
                    shown.forEach(m => {
                        var inst = bootstrap.Collapse.getInstance(m) || bootstrap.Collapse.getOrCreateInstance(m, {toggle:false});
                        inst.hide();
                    });
                }
            })();

            // Helper to hide other menus then show/hide target
            function toggleExclusive(target){
                var targetEl = target;
                var targetInst = bootstrap.Collapse.getInstance(targetEl) || bootstrap.Collapse.getOrCreateInstance(targetEl, {toggle:false});
                if (targetEl.classList.contains('show')){
                    targetInst.hide();
                    return;
                }
                menus.forEach(function(other){
                    if (other === targetEl) return;
                    var inst = bootstrap.Collapse.getInstance(other) || bootstrap.Collapse.getOrCreateInstance(other, {toggle:false});
                    inst.hide();
                });
                targetInst.show();
            }

            // Attach click handlers to toggles so clicks explicitly control exclusivity
            var invToggle = document.querySelector('[href="#inventoryMenu"]');
            var salesToggle = document.querySelector('[href="#salesMenu"]');
            var lastManualToggle = null;
            if (invToggle && inventory){ invToggle.addEventListener('click', function(e){ e.preventDefault(); lastManualToggle = inventory.id; toggleExclusive(inventory); setTimeout(()=> lastManualToggle = null, 300); }); }
            if (salesToggle && sales){ salesToggle.addEventListener('click', function(e){ e.preventDefault(); lastManualToggle = sales.id; toggleExclusive(sales); setTimeout(()=> lastManualToggle = null, 300); }); }

            // Add/remove 'active' class to the toggle anchor when its menu opens/closes
            menus.forEach(function(el){
                var selector = '[href="#' + el.id + '"]';
                var toggleAnchor = document.querySelector(selector);
                if (!toggleAnchor) return;
                el.addEventListener('show.bs.collapse', function(){ toggleAnchor.classList.add('active'); });
                el.addEventListener('hide.bs.collapse', function(e){
                    // if submenu contains an active child link, prefer to keep it visible to indicate active route
                    var hasActiveChild = !!el.querySelector('.list-group .active');
                    if (hasActiveChild){
                        // if hide was triggered by a manual toggle click, allow hide but keep header highlighted
                        if (lastManualToggle === el.id){
                            // keep the header 'active' class even when collapsed
                            return;
                        }
                        // otherwise prevent collapse (e.g. clicking outside) so active sections stay open
                        e.preventDefault();
                        return;
                    }
                    toggleAnchor.classList.remove('active');
                });
            });

            // Fallback: keep behavior on show event as extra safety
            menus.forEach(function(el){
                el.addEventListener('show.bs.collapse', function(){
                    menus.forEach(function(other){
                        if (other === el) return;
                        var inst = bootstrap.Collapse.getInstance(other) || bootstrap.Collapse.getOrCreateInstance(other, {toggle:false});
                        inst.hide();
                    });
                });
            });
        });
    })();
</script>
@yield('scripts')

</body>
</html>
