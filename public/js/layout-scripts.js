// Layout shared scripts moved out of Blade to avoid IDE linting issues.
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

(function(){
    function fitDashboardNumbers(){
        document.querySelectorAll('.dashboard-area .number').forEach(function(el){
            el.style.fontSize = '';
            var cardBody = el.closest('.card-body');
            if (!cardBody) return;
            var icon = cardBody.querySelector('.dashboard-icon');
            var iconWidth = icon ? icon.offsetWidth + 16 : 0;
            var avail = cardBody.clientWidth - iconWidth - 24;
            var cs = window.getComputedStyle(el);
            var fs = parseFloat(cs.fontSize) || 16;
            var minFs = 10;
            if (el.scrollWidth <= avail) return;
            while (el.scrollWidth > avail && fs > minFs){
                fs -= 1;
                el.style.fontSize = fs + 'px';
            }
        });
    }
    document.addEventListener('DOMContentLoaded', fitDashboardNumbers);
    window.addEventListener('resize', function(){ clearTimeout(window._fitDashTimer); window._fitDashTimer = setTimeout(fitDashboardNumbers, 120); });
})();

// Export / toolbar helpers
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
        if (table.dataset.serverExport === '1') {
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

        var inventory = document.getElementById('inventoryMenu');
        var sales = document.getElementById('salesMenu');
        var menus = [inventory, sales].filter(Boolean);

        (function normalizeInitialMenus(){
            var shown = menus.filter(m => m.classList.contains('show'));
            if (shown.length > 1) {
                shown.forEach(m => {
                    var inst = bootstrap.Collapse.getInstance(m) || bootstrap.Collapse.getOrCreateInstance(m, {toggle:false});
                    inst.hide();
                });
            }
        })();

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

        var invToggle = document.querySelector('[href="#inventoryMenu"]');
        var salesToggle = document.querySelector('[href="#salesMenu"]');
        var lastManualToggle = null;
        if (invToggle && inventory){ invToggle.addEventListener('click', function(e){ e.preventDefault(); lastManualToggle = inventory.id; toggleExclusive(inventory); setTimeout(()=> lastManualToggle = null, 300); }); }
        if (salesToggle && sales){ salesToggle.addEventListener('click', function(e){ e.preventDefault(); lastManualToggle = sales.id; toggleExclusive(sales); setTimeout(()=> lastManualToggle = null, 300); }); }

        menus.forEach(function(el){
            var selector = '[href="#' + el.id + '"]';
            var toggleAnchor = document.querySelector(selector);
            if (!toggleAnchor) return;
            el.addEventListener('show.bs.collapse', function(){ toggleAnchor.classList.add('active'); });
            el.addEventListener('hide.bs.collapse', function(e){
                var hasActiveChild = !!el.querySelector('.list-group .active');
                if (hasActiveChild){
                    if (lastManualToggle === el.id){
                        return;
                    }
                    e.preventDefault();
                    return;
                }
                toggleAnchor.classList.remove('active');
            });
        });

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

// Allow other scripts to append via Blade `@yield('scripts')` as before.
