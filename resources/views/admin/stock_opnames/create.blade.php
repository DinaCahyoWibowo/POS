@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Buat Stock Opname</h3>
    <form method="POST" action="{{ route('admin.stock_opnames.store') }}">
        @csrf
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="mb-3">
            <label>Tanggal Opname</label>
            <input type="date" name="opname_date" class="form-control" value="{{ old('opname_date', now()->format('Y-m-d')) }}" required>
        </div>
        <div class="mb-3">
            <label>Alasan</label>
            <textarea name="reason" class="form-control">{{ old('reason') }}</textarea>
        </div>

        <h5>Items</h5>
        <p class="text-muted">Isi stok fisik untuk item yang diperiksa. Stok sistem ditampilkan (readonly). Anda dapat memberikan alasan per item jika ada perbedaan.</p>
        <table class="table table-bordered">
            <thead><tr><th>Item</th><th>Stok Sistem</th><th>Stok Fisik</th><th>Selisih</th><th>Alasan (opsional)</th></tr></thead>
            <tbody id="lines">
                @foreach($items as $item)
                <tr>
                    <td>
                        <input type="hidden" name="lines[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                        {{ $item->name }}
                    </td>
                    <td>
                        <input type="text" class="form-control" value="{{ $item->currentStock() }}" readonly>
                        <input type="hidden" name="lines[{{ $loop->index }}][system_qty]" value="{{ $item->currentStock() }}">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="lines[{{ $loop->index }}][physical_qty]" class="form-control physical-input" value="{{ old('lines.'.$loop->index.'.physical_qty', '') }}">
                    </td>
                    <td class="diff-cell text-end">0</td>
                    <td>
                        <input type="text" name="lines[{{ $loop->index }}][reason]" class="form-control" value="{{ old('lines.'.$loop->index.'.reason', '') }}" placeholder="Alasan jika berbeda">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            <button class="btn btn-primary" id="save-opname">Simpan Opname</button>
            <a href="{{ route('admin.stock_opnames.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    function updateDiffRow(row){
        var systemHidden = row.querySelector('input[name$="[system_qty]"]');
        var physInput = row.querySelector('.physical-input');
        var diffCell = row.querySelector('.diff-cell');
        var system = parseFloat(systemHidden ? systemHidden.value : 0) || 0;
        var phys = parseFloat(physInput && physInput.value !== '' ? physInput.value : 0) || 0;
        var diff = phys - system;
        if(diffCell) {
            diffCell.textContent = Number(diff).toFixed(2);
            diffCell.style.color = diff < 0 ? '#c00' : (diff > 0 ? '#090' : '');
        }
    }

    document.querySelectorAll('#lines tr').forEach(function(row){
        // initialize
        updateDiffRow(row);
        var phys = row.querySelector('.physical-input');
        if(phys){
            phys.addEventListener('input', function(){ updateDiffRow(row); });
        }
    });

    document.getElementById('save-opname').addEventListener('click', function(e){
        // show confirmation/warning
        var ok = confirm('Anda akan menyimpan stock opname dan menyesuaikan stok sistem sesuai input fisik. Pastikan data sudah benar. Lanjutkan?');
        if(!ok) e.preventDefault();
    });
});
</script>
@endsection
