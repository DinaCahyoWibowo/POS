@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Create Item</h3>
        <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">-- choose brand --</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : ''}}>{{ $b->name }} ({{ $b->code }})</option>
                            @endforeach
                        </select>
                        @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- choose category --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : ''}}>{{ $c->name }} ({{ $c->code }})</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Base Unit</label>
                        <select name="base_unit_id" class="form-select @error('base_unit_id') is-invalid @enderror">
                            <option value="">-- choose base unit --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ old('base_unit_id') == $u->id ? 'selected' : ''}}>{{ $u->name }} {{ $u->code ? '('.$u->code.')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('base_unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cost Price</label>
                        <input name="cost_price" value="{{ old('cost_price') }}" class="form-control @error('cost_price') is-invalid @enderror">
                        @error('cost_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sell Price</label>
                        <input name="sell_price" value="{{ old('sell_price') }}" class="form-control @error('sell_price') is-invalid @enderror">
                        @error('sell_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" id="image-input" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="mt-2"><img id="image-preview" style="display:none; max-height:150px;" alt="Item image"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Item code will be generated automatically from CategoryCode+BrandCode+number and cannot be changed.</small>
                </div>

                <hr>
                <h5>Additional Units (sub-units)</h5>
                <p class="text-muted">Add alternative units for this item and set how many base units each contains.</p>

                <div id="item-units-wrapper">
                    @if(old('item_units'))
                        @foreach(old('item_units') as $i => $oldIU)
                            <div class="row g-2 align-items-center mb-2 unit-row">
                                <div class="col-md-6">
                                    <select name="item_units[{{ $i }}][unit_id]" class="form-select">
                                        <option value="">-- choose unit --</option>
                                        @foreach($units as $u)
                                            <option value="{{ $u->id }}" {{ isset($oldIU['unit_id']) && $oldIU['unit_id']==$u->id ? 'selected' : '' }}>{{ $u->name }} {{ $u->code ? '('.$u->code.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input name="item_units[{{ $i }}][factor]" placeholder="Factor (how many base units)" class="form-control" value="{{ $oldIU['factor'] ?? '' }}" />
                                </div>
                                <div class="col-md-3">
                                    <input name="item_units[{{ $i }}][price]" placeholder="Price (per this unit)" class="form-control" value="{{ $oldIU['price'] ?? '' }}" />
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-unit">Delete</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="mb-3">
                    <button type="button" id="add-unit-btn" class="btn btn-sm btn-outline-primary">Add Unit</button>
                </div>

                <button class="btn btn-primary">Create Item</button>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    (function(){
        const units = @json($units->map(fn($u)=>['id'=>$u->id,'name'=>$u->name,'code'=>$u->code]));
        const wrapper = document.getElementById('item-units-wrapper');
        const addBtn = document.getElementById('add-unit-btn');
        let idx = wrapper.querySelectorAll('.unit-row').length || 0;

        function makeRow(data){
            const row = document.createElement('div');
            row.className = 'row g-2 align-items-center mb-2 unit-row';
            row.innerHTML = `
                <div class="col-md-6">
                    <select name="item_units[${idx}][unit_id]" class="form-select">
                        <option value="">-- choose unit --</option>
                        ${units.map(u=>`<option value="${u.id}">${u.name} ${u.code?('('+u.code+')'):''}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <input name="item_units[${idx}][factor]" placeholder="Factor (how many base units)" class="form-control" />
                </div>
                <div class="col-md-3">
                    <input name="item_units[${idx}][price]" placeholder="Price (per this unit)" class="form-control" />
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-unit">Delete</button>
                </div>
            `;

            if(data){
                const sel = row.querySelector('select');
                const inp = row.querySelector('input[name^="item_units"][name$="[factor]"]');
                const price = row.querySelector('input[name^="item_units"][name$="[price]"]');
                sel.value = data.unit_id;
                inp.value = data.factor;
                if(data.price !== undefined && data.price !== null) price.value = data.price;
            }

            row.querySelector('.remove-unit').addEventListener('click', function(){
                row.remove();
            });

            wrapper.appendChild(row);
            idx++;
        }

        addBtn.addEventListener('click', function(){
            makeRow();
        });

        document.addEventListener('click', function(e){
            if(e.target && e.target.matches('.remove-unit')){
                e.target.closest('.unit-row').remove();
            }
        });

        // image preview for create form
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        if(imageInput){
            imageInput.addEventListener('change', function(e){
                const f = e.target.files && e.target.files[0];
                if(!f){ imagePreview.style.display = 'none'; imagePreview.src = ''; return; }
                const reader = new FileReader();
                reader.onload = function(ev){ imagePreview.src = ev.target.result; imagePreview.style.display = 'block'; };
                reader.readAsDataURL(f);
            });
        }
    })();
</script>
@endsection

@endsection
