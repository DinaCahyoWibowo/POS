
<div id="receipt" class="receipt-paper p-3">
	<div class="receipt-head text-center">
		<div class="store-name">UD KASEMI</div>
		<div class="store-sub">Alamat toko / Telp</div>
		<div class="divider"></div>
	</div>

	<div class="receipt-meta small">
		<div>Sale: <strong>{{ $sale->code }}</strong></div>
		<div>Date: <strong>{{ $sale->created_at->format('d-m-Y H:i') }}</strong></div>
		<div>Cashier: <strong>{{ $sale->user_id ? optional($sale->user)->name : '-' }}</strong></div>
	</div>

	<div class="divider"></div>

	<div class="items">
		@foreach($sale->items as $it)
		<div class="item-row">
			<div class="item-name">{{ Str::limit($it->item?->name ?? 'Item', 30) }}</div>
			<div class="item-right">
				<span class="qty">{{ (int)($it->qty ?? 0) }}@if($it->unit) {{ ' ' . strtoupper($it->unit->code) }}@endif</span>
				x
				<span class="price">{{ number_format($it->unit_price,0,',','.') }}</span>
			</div>
		</div>
		<div class="item-line-total">
			<div class="item-name">&nbsp;</div>
			<div class="item-right">
				<span class="line">{{ number_format($it->line_total,0,',','.') }}</span>
			</div>
		</div>
		@endforeach
	</div>

	<div class="divider dashed"></div>

	<div class="totals">
		<div class="tot-row"><span>Subtotal</span><span>{{ 'Rp '.number_format($sale->subtotal ?? 0,0,',','.') }}</span></div>
		<div class="tot-row"><span>Tax</span><span>{{ 'Rp '.number_format($sale->tax ?? 0,0,',','.') }}</span></div>
		<div class="tot-row"><span>Discount</span><span>{{ 'Rp '.number_format($sale->discount ?? 0,0,',','.') }}</span></div>
		<div class="tot-row total"><span>Total</span><span>{{ 'Rp '.number_format($sale->total ?? 0,0,',','.') }}</span></div>
	</div>

	<div class="divider dashed"></div>

	<div class="payments small">
		<div><strong>Payments</strong></div>
		@foreach($sale->payments as $p)
			<div class="pay-row"><span>{{ ucfirst($p->method) }}</span><span>{{ 'Rp '.number_format($p->amount,0,',','.') }}</span></div>
		@endforeach
		<div class="pay-row"><strong>Change</strong><strong>{{ 'Rp '.number_format(max(0, $sale->payments->sum('amount') - $sale->total),0,',','.') }}</strong></div>
	</div>


	<div class="divider"></div>

	<div class="thanks text-center small">Terima kasih atas kunjungan Anda!</div>
</div>

<style>
	/* Minimal receipt styles for inline modal/print */
	.receipt-paper { width: 58mm; font-family: 'Courier New', Courier, monospace; font-size: 12px; background: #fff; color:#000; text-align: left !important; }
	/* Keep only the receipt head centered, other sections should be left-aligned */
	.receipt-paper .receipt-head { text-align: center !important; }
	.receipt-paper .receipt-meta,
	.receipt-paper .items,
	.receipt-paper .totals,
	.receipt-paper .payments { text-align: left !important; }
	.receipt-head .store-name{ font-weight:700; font-size:14px; }
	.receipt-head .store-sub{ font-size:10px; margin-bottom:6px; }
	.divider{ border-top:1px solid #000; margin:6px 0; }
	.divider.dashed{ border-top:1px dashed #000; }
	.receipt-meta div{ margin-bottom:3px; }
	.item-row{ display:flex; justify-content:space-between; align-items:flex-start; margin:4px 0; }
	.item-name{ max-width:160px; }
	.item-right{ text-align:right; min-width:120px; }
	.item-right .qty{ margin-right:6px; }
	.item-right .price{ margin-right:8px; }
	.item-line-total{ display:flex; justify-content:space-between; align-items:flex-start; margin:0 0 6px 0; }
	.item-line-total .item-name{ opacity:0.0; }
	.item-line-total .line{ font-weight:700; }
	.totals{ margin-top:8px; }
	.tot-row{ display:flex; justify-content:space-between; margin:3px 0; }
	.tot-row.total{ font-weight:700; }
	.payments{ margin-top:6px; }
	.pay-row{ display:flex; justify-content:space-between; }
	.thanks{ margin-top:10px; }
</style>
