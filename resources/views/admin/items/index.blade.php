@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Items</h3>
        @if(auth()->user()?->role === 'admin')
            <a href="{{ route('admin.items.create') }}" class="btn btn-primary">Create Item</a>
        @endif
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    @php
        $fmt = function($v){
            if ($v === null || $v === '') return '';
            if (!is_numeric($v)) return $v;
            $f = (float) $v;
            if (floor($f) == $f) return (string) intval($f);
            $s = number_format($f, 8, '.', '');
            $s = rtrim($s, '0');
            $s = rtrim($s, '.');
            return $s;
        };
    @endphp

    <table class="table table-striped table-bordered align-middle" data-server-export="1" data-export-name="items_list">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Image</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Base Unit</th>
                <th>Stock</th>
                <th>Cost</th>
                <th>Price</th>
                <th>Units</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $it)
            <tr>
                <td>{{ $it->id }}</td>
                <td>{{ $it->code }}</td>
                <td>
                    @if($it->image)
                        <img src="{{ asset('storage/'.$it->image) }}" alt="{{ $it->name }}" class="img-thumbnail" style="width:64px;height:64px;object-fit:cover;" />
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $it->name }}</td>
                <td>{{ $it->brand?->name }}</td>
                <td>{{ $it->category?->name }}</td>
                <td>{{ $it->baseUnit?->name }}</td>
                <td>{{ ($fmt)($it->currentStock()) }} {{ $it->baseUnit?->code ? '('.$it->baseUnit->code.')' : $it->baseUnit?->name }}</td>
                <td>{{ $it->cost_price ? 'Rp '.number_format($it->cost_price,0,',','.') : '-' }}</td>
                <td>{{ $it->sell_price ? 'Rp '.number_format($it->sell_price,0,',','.') : '-' }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-popover" data-content-target="#units-{{ $it->id }}">Units</button>

                    <div id="units-{{ $it->id }}" class="d-none">
                        <div><strong>{{ $it->baseUnit?->name ?? '-' }}:</strong> {{ $it->sell_price ? 'Rp '.number_format($it->sell_price,0,',','.') : '-' }}</div>
                        @foreach($it->itemUnits->where('is_base',0) as $iu)
                            @php
                                $factorFloat = (float) $iu->factor;
                                $formattedFactor = (int)$factorFloat == $factorFloat ? (int)$factorFloat : rtrim(rtrim(sprintf('%.8f', $factorFloat),'0'),'.');
                            @endphp
                            <div class="small">
                                <strong>{{ $iu->unit?->name ?? 'Unit' }}</strong>
                                @if($iu->factor)
                                    &middot; {{ $formattedFactor }} {{ $it->baseUnit?->name ?? '' }}
                                @endif
                                &middot; <span class="text-success">{{ $iu->price ? 'Rp '.number_format($iu->price,0,',','.') : '-' }}</span>
                            </div>
                        @endforeach
                    </div>
                </td>
                <td>
                    @if(auth()->user()?->role === 'admin')
                        <a href="{{ route('admin.items.edit', $it) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.items.destroy', $it) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this item? This will remove associated item-units and cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $items->links('pagination::bootstrap-5') }}
</div>
@endsection

@section('scripts')
<script>
    (function(){
        // initialize bootstrap popovers for unit details
        document.querySelectorAll('.btn-popover').forEach(function(btn){
            const target = btn.dataset.contentTarget;
            const contentEl = document.querySelector(target);
            const pop = new bootstrap.Popover(btn, {
                html: true,
                content: function(){ return contentEl ? contentEl.innerHTML : '' },
                trigger: 'focus'
            });
        });
    })();
</script>
@endsection
