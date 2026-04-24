@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h3>POS (Point of Sale)</h3>
            <div class="card mb-3">
                <div class="card-body">
                    <div id="pos-app">
                        <div class="row">
                            <div class="col-lg-8">
                                <h5>Cart</h5>
                                <div id="cart" style="min-height:320px"></div>

                                <div class="mt-3 p-3 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-center">
                                            <div class="small text-muted">Total Amount</div>
                                            <div id="cart-total" class="fs-3">Rp 0</div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="small text-muted">Payment Amount</div>
                                            <input id="payment-amount" class="form-control mt-1 text-center" placeholder="Enter payment" />
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="small text-muted">Actions</div>
                                            <div class="d-grid gap-2">
                                                <button id="hold-btn" class="btn btn-danger">Hold</button>
                                                <button id="cash-btn" class="btn btn-success">Cash</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="mb-2 d-flex">
                                    <input id="search" class="form-control me-2" placeholder="Item name/Barcode/Itemcode [Ctrl+Shift+S]" />
                                    <button id="refresh-products" class="btn btn-outline-secondary">↻</button>
                                </div>

                                <div class="mb-2">
                                    <select id="category-filter" class="form-select mb-2">
                                        <option value="">All Categories</option>
                                    </select>
                                    <select id="brand-filter" class="form-select">
                                        <option value="">All Brands</option>
                                    </select>
                                </div>

                                <div id="products-grid" style="max-height:520px; overflow:auto;">
                                    <div class="row g-2" id="products-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>

@section('scripts')
<style>
    /* Quantity control styling */
    .qty-control .btn {
        width:36px; height:36px; padding:0; display:flex; align-items:center; justify-content:center;
        border-radius:0.25rem;
    }
    .qty-control { display:inline-flex; align-items:center; }
    .qty-control .form-control {
        max-width:80px; text-align:center; border-left:1px solid #dee2e6; border-right:1px solid #dee2e6; height:36px; padding:0.375rem .5rem;
    }
    .qty-control .btn:first-child{ border-top-left-radius:.25rem; border-bottom-left-radius:.25rem }
    .qty-control .btn:last-child{ border-top-right-radius:.25rem; border-bottom-right-radius:.25rem }
    /* Product card out-of-stock styling */
    .pos-product-card.out-of-stock { opacity:0.55; filter:grayscale(60%); }
    .pos-product-card.out-of-stock .card-body { pointer-events: none; }
    .out-of-stock-badge { color:#dc3545; font-weight:600; }
    /* Inline print helper: when `body.printing-receipt` is present, only the receipt (#receipt) will be visible in print */
    @media print {
        body.printing-receipt * { visibility: hidden !important; }
        body.printing-receipt #receipt, body.printing-receipt #receipt * { visibility: visible !important; }
        /* Ensure the receipt prints at thermal width (58mm) and sits at the top-left of the page */
        body.printing-receipt #receipt { position: absolute !important; left: 0; top: 0; width: 58mm !important; margin: 0 !important; }
        /* Hide modal chrome and controls during print to avoid extra elements */
        body.printing-receipt .modal-header,
        body.printing-receipt .modal-footer,
        body.printing-receipt .btn,
        body.printing-receipt .btn-close { display: none !important; visibility: hidden !important; }
        /* Remove margins and page decorations for compact receipt print */
        @page { margin: 8mm; }
    }
</style>

<script>
    // Enhanced POS client: editable units and qty, subtotal, and structured checkout
    (function(){
        const items = @json($items);
        const productsList = document.getElementById('products-list');
        const cartEl = document.getElementById('cart');
        const cartTotalEl = document.getElementById('cart-total');
        const paymentAmountEl = document.getElementById('payment-amount');
        const cashBtn = document.getElementById('cash-btn');
        const holdBtn = document.getElementById('hold-btn');
        const refreshBtn = document.getElementById('refresh-products');
        const categoryFilter = document.getElementById('category-filter');
        const brandFilter = document.getElementById('brand-filter');
        const cart = [];
        const productStock = {}; // item_id -> available qty (UI-only)

        function getUnitsFor(it){
            return it.itemUnits || it.item_units || it.item_units || [];
        }

        function formatRp(n){
            return 'Rp ' + Number(n || 0).toLocaleString('id-ID', {maximumFractionDigits:0});
        }

        function renderFilters(){
            const cats = new Map();
            const brands = new Map();
            items.data.forEach(it=>{
                if(it.category) cats.set(it.category.id, it.category.name);
                if(it.brand) brands.set(it.brand.id, it.brand.name);
            });
            // populate selects
            categoryFilter.innerHTML = '<option value="">All Categories</option>' + Array.from(cats).map(([id,name])=>`<option value="${id}">${name}</option>`).join('');
            brandFilter.innerHTML = '<option value="">All Brands</option>' + Array.from(brands).map(([id,name])=>`<option value="${id}">${name}</option>`).join('');
        }

        function initProductStock(){
            items.data.forEach(it=>{
                // detect common stock fields or parse numbers from formatted strings (e.g. "74 (pcs)")
                let s = it.stock ?? it.stock_qty ?? it.quantity ?? it.qty ?? it.qty_available ?? it.available_qty ?? it.stock_display ?? it.stock_label ?? null;
                if(s === null || s === undefined || s === ''){
                    // try other likely properties or fallback to scanning the item object for a number
                    const candidate = it.stock_text ?? it.stock_formatted ?? it.stock_label ?? null;
                    if(candidate !== null && candidate !== undefined && String(candidate).trim() !== '') s = candidate;
                    else {
                        // last resort: search the whole item object for the first integer
                        const m = JSON.stringify(it).match(/-?\\d+/);
                        s = m ? Number(m[0]) : 0;
                    }
                }
                // if s is a string with extra text like "74 (pcs)", extract the number
                const m = String(s).match(/-?\d+/);
                productStock[it.id] = m ? Number(m[0]) : (Number(s) || 0);
            });
        }

        function updateProductStockBadge(itemId){
            try{
                const el = document.getElementById('product-stock-' + itemId);
                const card = document.getElementById('product-card-' + itemId);
                const stock = (productStock[itemId] ?? 0);
                if(el) el.textContent = 'Stock: ' + stock;
                if(card){
                    if(stock <= 0){
                        card.classList.add('out-of-stock');
                        // badge style
                        if(el){ el.classList.remove('text-muted'); el.classList.add('out-of-stock-badge'); el.textContent = 'Out of stock'; }
                    } else {
                        card.classList.remove('out-of-stock');
                        if(el){ el.classList.remove('out-of-stock-badge'); el.classList.add('text-muted'); el.textContent = 'Stock: ' + stock; }
                    }
                }
            }catch(e){}
        }

        function renderProducts(){
            const q = (document.getElementById('search').value || '').toLowerCase();
            const cat = categoryFilter.value;
            const br = brandFilter.value;
            productsList.innerHTML = '';
            items.data.forEach(it=>{
                if(q && !(it.name.toLowerCase().includes(q) || (it.code||'').toLowerCase().includes(q))) return;
                if(cat && !(it.category && String(it.category.id) === String(cat))) return;
                if(br && !(it.brand && String(it.brand.id) === String(br))) return;

                const col = document.createElement('div'); col.className = 'col-6';
                const card = document.createElement('div'); card.className = 'card p-2 h-100';
                const img = document.createElement('div'); img.style.height='90px'; img.style.display='flex'; img.style.alignItems='center'; img.style.justifyContent='center'; img.style.overflow='hidden';
                if(it.image){ const im = document.createElement('img'); im.src = '/storage/'+it.image; im.style.maxWidth='100%'; im.style.maxHeight='100%'; im.style.objectFit='cover'; im.alt = it.name; img.appendChild(im); }
                else { img.innerHTML = '<div class="text-muted">No image</div>'; }
                const body = document.createElement('div'); body.className = 'card-body p-2';
                body.innerHTML = `<h6 class="card-title mb-1" style="font-size:14px">${it.name}</h6><div class="small text-muted mb-1">${it.code || ''}</div><div class="fw-bold">${formatRp(it.sell_price)}</div>`;
                // stock badge
                const stockBadge = document.createElement('div'); stockBadge.className = 'small text-muted mt-1'; stockBadge.id = `product-stock-${it.id}`;
                stockBadge.textContent = 'Stock: ' + (productStock[it.id] ?? 0);
                body.appendChild(stockBadge);
                card.appendChild(img); card.appendChild(body);
                card.id = `product-card-${it.id}`;
                // if out of stock, don't attach click handler and show not-allowed cursor
                if((productStock[it.id] ?? 0) <= 0){
                    card.classList.add('pos-product-card','out-of-stock');
                    stockBadge.classList.remove('text-muted');
                    stockBadge.classList.add('out-of-stock-badge');
                    stockBadge.textContent = 'Out of stock';
                    card.style.cursor = 'not-allowed';
                } else {
                    card.classList.add('pos-product-card');
                    card.style.cursor = 'pointer';
                    card.addEventListener('click', ()=> addToCart(it));
                }
                col.appendChild(card);
                productsList.appendChild(col);
            });
        }

        function addToCart(it){
            // check UI stock
            const avail = productStock[it.id] ?? 0;
            if(avail <= 0) return alert('Out of stock');

            const units = getUnitsFor(it);
            const baseUnitId = it.base_unit_id || (it.baseUnit? it.baseUnit.id : null);
            // find base unit price if available
            let unitPrice = it.sell_price || 0;
            if(units && units.length){
                const base = units.find(u=> (u.unit_id && String(u.unit_id) === String(baseUnitId)) || (u.unit && String(u.unit.id) === String(baseUnitId)) );
                if(base && (base.price !== undefined && base.price !== null)) unitPrice = base.price;
            }

            // determine base factor
            let baseFactor = 1;
            if(units && units.length){
                const base = units.find(u=> (u.unit_id && String(u.unit_id) === String(baseUnitId)) || (u.unit && String(u.unit.id) === String(baseUnitId)) );
                if(base){ baseFactor = (base.factor !== undefined && base.factor !== null) ? Number(base.factor) : (base.unit && base.unit.factor ? Number(base.unit.factor) : 1); }
            }

            // try to find an existing cart row that matches the base unit
            const existingSameUnit = cart.find(c => c.item_id == it.id && String(c.unit_id) === String(baseUnitId));
            if(existingSameUnit){
                const factor = existingSameUnit.factor || 1;
                if((productStock[it.id]||0) < factor) return alert('Not enough stock');
                existingSameUnit.qty = parseInt(existingSameUnit.qty) + 1;
                productStock[it.id] = (productStock[it.id]||0) - factor;
                updateProductStockBadge(it.id);
                renderCart();
                return;
            }

            // no matching row for base unit; create a new cart row for base unit
            cart.push({ item_id: it.id, name: it.name, units: units, unit_id: baseUnitId, qty: 1, unit_price: Math.round(unitPrice), factor: baseFactor });
            // decrement UI stock by base factor
            productStock[it.id] = (productStock[it.id]||0) - baseFactor;
            updateProductStockBadge(it.id);
            renderCart();
        }

        function renderCart(){
            cartEl.innerHTML = '';
            if(cart.length === 0){ cartEl.innerHTML = '<div class="text-muted">Cart is empty</div>'; updateTotals(); return; }

            const table = document.createElement('table');
            table.className = 'table table-sm';
            table.innerHTML = `<thead><tr><th>Item</th><th>Unit</th><th class="text-end">Stock</th><th class="text-start">Quantity</th><th class="text-end">Unit Price</th><th class="text-end">Line Total</th><th></th></tr></thead><tbody></tbody>`;
            const tbody = table.querySelector('tbody');

            cart.forEach((c,i)=>{
                const tr = document.createElement('tr');

                // unit select
                const unitSelect = document.createElement('select');
                unitSelect.className = 'form-select form-select-sm';
                const units = c.units || [];
                units.forEach(u=>{
                    const uid = u.unit_id ?? (u.unit ? u.unit.id : u.id);
                    const uname = (u.unit ? u.unit.name : (u.name || u.unit_name || 'Unit')) || 'Unit';
                    const price = (u.price !== undefined && u.price !== null) ? u.price : (u.unit ? u.unit.price : null);
                    const opt = document.createElement('option');
                    opt.value = uid; opt.textContent = price ? (uname + ' - ' + formatRp(price)) : uname;
                    if(price !== null) opt.dataset.price = price;
                        const factor = (u.factor !== undefined && u.factor !== null) ? u.factor : (u.unit && u.unit.factor !== undefined ? u.unit.factor : 1);
                        opt.dataset.factor = factor;
                        unitSelect.appendChild(opt);
                });
                if(!units.length){ const opt = document.createElement('option'); opt.value = c.unit_id || ''; opt.textContent = 'Base'; unitSelect.appendChild(opt); }
                unitSelect.value = c.unit_id;

                // qty control with +/- buttons
                const qtyGroup = document.createElement('div'); qtyGroup.className = 'input-group input-group-sm qty-control';
                const btnMinus = document.createElement('button'); btnMinus.type = 'button'; btnMinus.className = 'btn btn-sm btn-danger'; btnMinus.textContent = '-';
                const qtyInput = document.createElement('input'); qtyInput.type='number'; qtyInput.inputMode = 'numeric'; qtyInput.min = '1'; qtyInput.value = c.qty; qtyInput.className='form-control form-control-sm text-center'; qtyInput.style.maxWidth = '80px';
                const btnPlus = document.createElement('button'); btnPlus.type = 'button'; btnPlus.className = 'btn btn-sm btn-success'; btnPlus.textContent = '+';
                // disable + button if not enough UI stock for the current unit factor
                (function(){
                    const unitFactorForBtn = c.factor || 1;
                    if((productStock[c.item_id]||0) < unitFactorForBtn){
                        btnPlus.disabled = true;
                        btnPlus.classList.remove('btn-success');
                        btnPlus.classList.add('btn-secondary');
                    } else {
                        btnPlus.disabled = false;
                        btnPlus.classList.remove('btn-secondary');
                        btnPlus.classList.add('btn-success');
                    }
                })();
                qtyGroup.appendChild(btnMinus); qtyGroup.appendChild(qtyInput); qtyGroup.appendChild(btnPlus);

                const lineTotal = () => (Number(c.unit_price)||0) * (Number(c.qty)||0);

                tr.innerHTML = `<td>${c.name}</td><td></td><td class="text-end"></td><td class="text-start ps-0"></td><td class="text-end"></td><td class="text-end">${formatRp(lineTotal())}</td><td class="text-end"><button class="btn btn-sm btn-outline-danger">Remove</button></td>`;
                tr.children[1].appendChild(unitSelect);
                // stock display cell (remaining stock in UI)
                const stockDisplay = document.createElement('div'); stockDisplay.className = 'text-end'; stockDisplay.textContent = (productStock[c.item_id] ?? 0);
                tr.children[2].appendChild(stockDisplay);
                tr.children[3].appendChild(qtyGroup);
                // price (read-only display, rounded to whole rupiah)
                const priceDisplay = document.createElement('div'); priceDisplay.className = 'form-control form-control-sm text-end'; priceDisplay.style.background = 'transparent'; priceDisplay.style.border = '0'; priceDisplay.textContent = formatRp(Math.round(c.unit_price || 0));
                                
                                // unit change: handle price, factor and stock delta (factor-aware handler kept)
                                unitSelect.addEventListener('change', function(){
                                    const sel = unitSelect.selectedOptions[0];
                                    const p = sel && sel.dataset.price ? Math.round(Number(sel.dataset.price)) : null;
                                    const newFactor = sel && sel.dataset.factor ? Number(sel.dataset.factor) : 1;
                                    const prevBase = (Number(c.qty)||0) * (c.factor || 1);
                                    const newBase = (Number(c.qty)||0) * newFactor;
                                    const delta = newBase - prevBase; // positive means need more stock
                                    if(delta > 0){ if((productStock[c.item_id]||0) < delta){ alert('Not enough stock for selected unit'); // revert select
                                                // reset select to previous value
                                                const prevOpt = Array.from(unitSelect.options).find(o=>o.value == c.unit_id);
                                                if(prevOpt) prevOpt.selected = true; return; } productStock[c.item_id] = (productStock[c.item_id]||0) - delta; }
                                    else if(delta < 0){ productStock[c.item_id] = (productStock[c.item_id]||0) - delta; }
                                    if(p !== null){ c.unit_price = p; }
                                    c.unit_id = unitSelect.value;
                                    c.factor = newFactor;
                                    priceDisplay.textContent = formatRp(Math.round(c.unit_price || 0));
                                    updateProductStockBadge(c.item_id);
                                    updateTotals(); renderCart();
                                });
                // qty input events and +/- buttons (factor-aware handler)
                                function setQtyFromInput(){ 
                                    let v = parseInt(qtyInput.value || 0); 
                                    if(!Number.isInteger(v) || v <= 0) v = 1; 
                                    const prev = Number(c.qty)||0; 
                                    const unitFactor = c.factor || 1; 
                                    const deltaUnits = v - prev; 
                                    const deltaBase = deltaUnits * unitFactor; 
                                    if(deltaBase > 0){ 
                                        if((productStock[c.item_id]||0) < deltaBase){ 
                                            alert('Not enough stock'); 
                                            qtyInput.value = prev; 
                                            return; 
                                        } 
                                        productStock[c.item_id] = (productStock[c.item_id]||0) - deltaBase; 
                                    } else if(deltaBase < 0){ 
                                        productStock[c.item_id] = (productStock[c.item_id]||0) - deltaBase; 
                                    } 
                                    qtyInput.value = v; 
                                    c.qty = v; 
                                    updateProductStockBadge(c.item_id); 
                                    updateTotals(); 
                                    renderCart(); 
                                }
                qtyInput.addEventListener('change', setQtyFromInput);
                qtyInput.addEventListener('blur', setQtyFromInput);
                                btnPlus.addEventListener('click', function(){ 
                                    const unitFactor = c.factor || 1; 
                                    if((productStock[c.item_id]||0) < unitFactor) return alert('Not enough stock'); 
                                    let v = parseInt(qtyInput.value || 0) || 0; 
                                    v = v + 1; 
                                    qtyInput.value = v; 
                                    c.qty = v; 
                                    productStock[c.item_id] = (productStock[c.item_id]||0) - unitFactor; 
                                    updateProductStockBadge(c.item_id); 
                                    updateTotals(); 
                                    renderCart(); 
                                });
                                btnMinus.addEventListener('click', function(){ 
                                    const unitFactor = c.factor || 1; 
                                    let v = parseInt(qtyInput.value || 0) || 0; 
                                    if(v <= 1) return; 
                                    v = v - 1; 
                                    qtyInput.value = v; 
                                    c.qty = v; 
                                    productStock[c.item_id] = (productStock[c.item_id]||0) + unitFactor; 
                                    updateProductStockBadge(c.item_id); 
                                    updateTotals(); 
                                    renderCart(); 
                                });
                tr.children[4].appendChild(priceDisplay);
                // price is read-only; no manual edits allowed
                // attach remove handler only to the Remove button and return stock
                const removeBtn = tr.querySelector('td:last-child button.btn-outline-danger');
                if(removeBtn){ 
                                    removeBtn.addEventListener('click', function(){ 
                                        const unitFactor = c.factor || 1; 
                                        productStock[c.item_id] = (productStock[c.item_id]||0) + (Number(c.qty || 0) * unitFactor); 
                                        updateProductStockBadge(c.item_id); 
                                        cart.splice(i,1); 
                                        renderCart(); 
                                    }); 
                                }
                tbody.appendChild(tr);
            });

            cartEl.appendChild(table);
            updateTotals();
        }

        function updateTotals(){ let total = 0; cart.forEach(c=>{ total += (Number(c.unit_price)||0) * (Number(c.qty)||0); }); cartTotalEl.textContent = formatRp(total); return total; }

        // wire up product search / filters
        document.getElementById('search').addEventListener('input', function(e){ renderProducts(); });
        categoryFilter.addEventListener('change', renderProducts);
        brandFilter.addEventListener('change', renderProducts);
        refreshBtn.addEventListener('click', function(){ document.getElementById('search').value=''; categoryFilter.value=''; brandFilter.value=''; renderProducts(); });

        // cash button prompts for payment and submits
        cashBtn.addEventListener('click', function(){
            if(cart.length === 0) return alert('Cart empty');
            const total = updateTotals();
            // read payment amount from input; if empty, fallback to total
            let raw = (paymentAmountEl && paymentAmountEl.value) ? String(paymentAmountEl.value) : '';
            // remove non-numeric characters
            raw = raw.replace(/[^0-9\.-]/g,'');
            const payment = raw === '' ? total : (parseFloat(raw) || 0);
            if(payment < total) return alert('Insufficient payment');
            const payloadItems = cart.map(c => ({ item_id: c.item_id, unit_id: c.unit_id, qty: parseInt(c.qty), unit_price: c.unit_price }));
            const payload = { items: payloadItems, payment: { amount: Number(payment) } };
            fetch(`{{ route('admin.pos.sale') }}`, { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' }, body: JSON.stringify(payload) })
                .then(r=>r.json()).then(j=>{
                    if(j.success) {
                        // Clear cart and reset payment input so POS stays ready for next sale
                        try{ cart.length = 0; renderCart(); if(paymentAmountEl) paymentAmountEl.value = ''; } catch(e){}
                        // show a small success toast, then display receipt modal shortly after
                        try{ showSuccessToast('Sale completed'); } catch(e){}
                        setTimeout(function(){
                            fetch(`{{ url('admin/sales') }}/${j.sale_id}/receipt`).then(r=>r.text()).then(html=>{
                                showReceiptModal(html);
                            }).catch(e=>{ alert('Sale created but unable to load receipt'); });
                        }, 700);
                    }
                    else if(j.error) alert('Error: '+j.error);
                }).catch(e=>alert('Server error'));
        });

        holdBtn.addEventListener('click', function(){ alert('Hold not implemented yet'); });

        initProductStock();
        renderFilters();
        renderProducts();
    })();
</script>
<!-- Receipt modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sale Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="receiptModalBody">
                <!-- Receipt HTML will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                <button type="button" class="btn btn-primary" id="receiptPrintBtn">Print</button>
            </div>
        </div>
    </div>
</div>

<script>
        function showReceiptModal(html){
                const modalBody = document.getElementById('receiptModalBody');
                modalBody.innerHTML = html;
                const modalEl = document.getElementById('receiptModal');
                const modal = new bootstrap.Modal(modalEl);
                // attach print handler (inline printing — no new tab)
                document.getElementById('receiptPrintBtn').onclick = function(){
                    try{
                        const receiptEl = modalBody.querySelector('#receipt');
                        if(!receiptEl){ alert('Receipt not found'); return; }

                        // Add class to body so @media print rules show only the receipt
                        document.body.classList.add('printing-receipt');

                        // Allow the browser a short moment to apply styles, then print
                        setTimeout(function(){
                            try{
                                // Prefer using afterprint event to cleanup when available
                                const cleanup = function(){
                                    try{ document.body.classList.remove('printing-receipt'); }catch(_){}
                                    try{ window.removeEventListener('afterprint', cleanup); }catch(_){}
                                    // hide the modal when printing is finished
                                    try{ if(modal && typeof modal.hide === 'function') modal.hide(); }catch(_){}
                                };
                                if('onafterprint' in window){
                                    window.addEventListener('afterprint', cleanup);
                                } else {
                                    // Fallback: remove class and hide modal shortly after printing is triggered
                                    setTimeout(cleanup, 800);
                                }

                                window.print();
                            }catch(e){
                                // ensure we still remove the class on error
                                try{ document.body.classList.remove('printing-receipt'); }catch(_){}
                                throw e;
                            }
                        }, 60);
                    }catch(e){
                        alert('Unable to print: ' + (e && e.message ? e.message : e));
                    }
                };
                modal.show();
        }
</script>

<!-- Small success toast (positioned top-right) -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:10850;">
    <div id="posSuccessToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div id="posSuccessToastBody" class="toast-body">Sale completed</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
        function showSuccessToast(msg){
                const el = document.getElementById('posSuccessToast');
                const body = document.getElementById('posSuccessToastBody');
                if(body) body.textContent = msg || 'Success';
                const toast = bootstrap.Toast.getOrCreateInstance(el, { delay: 1400 });
                toast.show();
        }
</script>
@endsection

@endsection
