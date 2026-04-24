@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Stock In</h3>
        <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.stock_movements.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Item</label>
                    <select id="item-select" name="item_id" class="form-select">
                        <option value="">-- choose item --</option>
                        @foreach($items as $it)
                            <option value="{{ $it->id }}" data-units='@json($it->itemUnits->map(fn($u)=>["id"=>$u->unit->id,"name"=>$u->unit->name,"factor"=>$u->factor]))'>{{ $it->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <select id="unit-select" name="unit_id" class="form-select">
                        <option value="">-- choose unit --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input name="qty" type="number" step="1" min="1" class="form-control" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Cost Price (optional)</label>
                    <input name="cost_price" type="number" step="0.01" class="form-control" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Note</label>
                    <input name="note" class="form-control" />
                </div>

                <button class="btn btn-primary">Add Stock</button>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    (function(){
        const itemSelect = document.getElementById('item-select');
        const unitSelect = document.getElementById('unit-select');

        function populateUnits() {
            const opt = itemSelect.selectedOptions[0];
            unitSelect.innerHTML = '<option value="">-- choose unit --</option>';
            if(!opt) return;
            const units = opt.dataset.units ? JSON.parse(opt.dataset.units) : [];
            units.forEach(u=>{
                const o = document.createElement('option'); o.value = u.id; o.textContent = u.name + (u.factor ? (' (factor '+u.factor+')') : '');
                unitSelect.appendChild(o);
            });
        }

        itemSelect.addEventListener('change', populateUnits);
    })();
</script>
@endsection

@endsection
