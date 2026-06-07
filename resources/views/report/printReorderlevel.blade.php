<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stock Received Items Report</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --ink: #0f1923;
    --ink-soft: #3a4a58;
    --ink-mute: #6b7f8e;
    --accent: #006b5b;
    --accent-light: #e0f2ee;
    --accent-mid: #009e86;
    --border: #d0dde6;
    --bg: #f5f8fa;
    --white: #ffffff;
    --danger: #b91c1c;
    --gold: #92680a;
    --gold-light: #fef3c7;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

 body {
  font-family: 'DM Sans', sans-serif;
  background: var(--bg);
  color: var(--ink);
  font-size: 17px;
  min-height: 100vh;
  padding: 40px 20px;
}

 .page {
  width: 100%;
  max-width: 100%;
  margin: 0;
}

  /* ── HEADER ── */
  .header {
    background: var(--ink);
    color: var(--white);
    padding: 32px 40px 28px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
  }
  .table-section {
  overflow-x: auto;
}
  .header-left {}

  .logo-line {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
  }

  .logo-badge {
    width: 42px; height: 42px;
    border: 2px solid var(--accent-mid);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'DM Serif Display', serif;
    font-size: 14px;
    color: var(--accent-mid);
    letter-spacing: 1px;
  }

  .org-name {
    font-family: 'DM Serif Display', serif;
    font-size: 26px;
    letter-spacing: 0.3px;
  }

  .org-sub {
    font-size: 13px;
    color: #8fa5b5;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-top: 2px;
  }

  .report-title-block {
    text-align: right;
  }

  .report-title {
    font-family: 'DM Serif Display', serif;
    font-size: 30px;
    letter-spacing: 0.2px;
    line-height: 1.1;
  }

  .report-sub {
    font-size: 13px;
    color: #8fa5b5;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-top: 6px;
  }

  /* ── META BAR ── */
  .meta-bar {
    background: var(--accent);
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    border-bottom: 3px solid var(--accent-mid);
  }

  .meta-cell {
    padding: 14px 20px;
    border-right: 1px solid rgba(255,255,255,0.12);
    color: var(--white);
  }
  .meta-cell:last-child { border-right: none; }

  .meta-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: rgba(255,255,255,0.65);
    margin-bottom: 4px;
  }

  .meta-value {
    font-size: 17px;
    font-weight: 600;
  }

  /* ── INFO SECTION ── */
  .info-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border-bottom: 1px solid var(--border);
  }

  .info-block {
    padding: 24px 40px;
    border-right: 1px solid var(--border);
  }
  .info-block:last-child { border-right: none; }

  .info-block-title {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 2.5px;
    color: var(--accent);
    font-weight: 600;
    margin-bottom: 14px;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--accent-light);
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 8px;
  }

  .info-key {
    color: var(--ink-mute);
    font-size: 12px;
    white-space: nowrap;
  }

  .info-val {
    font-weight: 500;
    font-size: 12px;
    text-align: right;
    color: var(--ink);
  }

  /* ── TABLE ── */
  .table-section {
    padding: 0 0 0 0;
  }

  .table-header-bar {
    background: var(--ink);
    color: var(--white);
    padding: 12px 40px;
    font-size: 15px;
    text-transform: uppercase;
    letter-spacing: 3px;
    font-weight: 600;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead tr {
    background: var(--accent-light);
  }

  thead th {
    padding: 11px 10px;
    text-align: left;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--accent);
    font-weight: 700;
    border-bottom: 2px solid var(--accent-mid);
    white-space: nowrap;
  }

 thead th:first-child { padding-left: 10px; }
thead th:last-child { padding-right: 10px; }



  tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
  }

  tbody tr:hover { background: #f0f7f5; }
  tbody tr:nth-child(even) { background: #fafcfc; }
  tbody tr:nth-child(even):hover { background: #f0f7f5; }

  tbody td {
    padding: 12px 10px;
    font-size: 16px;
    color: var(--ink-soft);
    vertical-align: middle;
  }

 tbody td:first-child { padding-left: 10px; }
tbody td:last-child { padding-right: 10px; }

  .item-code {
    font-family: 'DM Sans', monospace;
    font-size: 10px;
    background: var(--accent-light);
    color: var(--accent);
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.3px;
  }

  .badge-active { background: #dcfce7; color: #166534; }
  .badge-pending { background: var(--gold-light); color: var(--gold); }

  /* ── TOTALS ROW ── */
  tfoot tr {
    background: var(--ink);
    color: var(--white);
  }

  tfoot td {
    padding: 14px 10px;
    font-weight: 600;
    font-size: 13px;
  }

  tfoot td:first-child { padding-left: 40px; }
  tfoot td:last-child { padding-right: 40px; text-align: right; }

  .total-label {
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: rgba(255,255,255,0.6);
  }

  /* ── SUMMARY ── */
  .summary-section {
    display: flex;
    justify-content: flex-end;
    padding: 30px 40px;
    border-top: 1px solid var(--border);
  }

  .summary-box {
    width: 300px;
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 16px;
    border-bottom: 1px solid var(--border);
    font-size: 15px;
  }

  .summary-row:last-child {
    background: var(--accent);
    color: var(--white);
    border-radius: 0 0 4px 4px;
    padding: 14px 16px;
    border-bottom: none;
    font-size: 14px;
    font-weight: 700;
  }

  .summary-row:last-child .s-label { color: rgba(255,255,255,0.8); }

  .s-label { color: var(--ink-mute); }
  .s-val { font-weight: 600; color: var(--ink); }

  .summary-title {
    background: var(--ink);
    color: var(--white);
    padding: 10px 16px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 2px;
    border-radius: 4px 4px 0 0;
    font-weight: 600;
  }

  /* ── FOOTER ── */
  .footer {
    background: var(--bg);
    border-top: 1px solid var(--border);
    padding: 16px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    color: var(--ink-mute);
  }

  .footer-left { display: flex; gap: 20px; }
  .footer-right { letter-spacing: 0.3px; }

  /* ── PRINT BUTTON ── */
  .print-bar {
    max-width: 1100px;
    margin: 0 auto 16px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

  .btn-print {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--accent);
    color: var(--white);
    border: none;
    padding: 10px 22px;
    border-radius: 4px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    letter-spacing: 0.3px;
    transition: background 0.18s, transform 0.1s;
    box-shadow: 0 2px 8px rgba(0,107,91,0.25);
  }

  .btn-print:hover {
    background: var(--accent-mid);
    transform: translateY(-1px);
  }

  .btn-print:active { transform: translateY(0); }

  .btn-print svg {
    width: 16px; height: 16px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  @media print {
  .print-bar {
    display: none !important;
  }

   html, body {
    font-size: 16pt !important;
    transform: scale(1) !important;
    transform-origin: top left !important;
  }
   body {
    width: 100% !important;
    min-width: 1200px !important;
  }
  
}

   

    /* Force background colors to print */
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    .header { background: var(--ink) !important; }
    .meta-bar { background: var(--accent) !important; }
    .table-header-bar { background: var(--ink) !important; }
    thead tr { background: var(--accent-light) !important; }
    tfoot tr { background: var(--ink) !important; }
    .summary-title { background: var(--ink) !important; }
    .summary-row:last-child { background: var(--accent) !important; }
    .footer { background: var(--bg) !important; }

    @page {
      margin: 10mm 12mm;
      size: A4 landscape;
    }

    table {
    font-size: 14px;
    }

    thead th {
    font-size: 14px;
    }

    tbody td {
    font-size: 15px;
    }
    thead th, tbody td {
  white-space: nowrap;
}
tfoot td {
  white-space: nowrap;
}

@page {
  size: A4 landscape;
}
@page {
  size: A3 landscape;
}
  
</style>
</head>
<body>

<!-- PRINT BAR -->
<div class="print-bar">
  <button class="btn-print" onclick="window.print()">
    <svg viewBox="0 0 24 24">
      <polyline points="6 9 6 2 18 2 18 9"/>
      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
      <rect x="6" y="14" width="12" height="8"/>
    </svg>
    Print Report
  </button>
</div>

<div class="page">

  <!-- HEADER -->
  <div class="header">
    <div class="header-left">
      <div class="logo-line">
        <div class="logo-badge">
             <img src="{{asset('backend/assets/img/logo (2).png')}}" alt="" class="height-100 mb-3" width="70">
        </div>
        <div>
          <div class="org-name">Korle Bu Teaching Hospital</div>
          <div class="org-sub">Stock Shield </div>
        </div>
      </div>
    </div>
    <div class="report-title-block">
      <div class="report-title">Re-order Level Report</div>
      <div class="report-sub">Central   Stores Department</div>
    </div>
  </div>

  <!-- META BAR -->
  <div class="meta-bar">
    <div class="meta-cell">
      <div class="meta-label">Generate By</div>
      <div class="meta-value">{{ auth()->user()->name ?? '' }}</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Department / Store</div>
      <div class="meta-value">{{ $store->name }}</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Generated On</div>
      <div class="meta-value">{{  date('d M, Y h:i A')  }}</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Total Records</div>
      <div class="meta-value">{{ $liststock->count() }} Items</div>
    </div>
  </div>

  <!-- INFO SECTION -->
  {{-- <div class="info-section">
    <div class="info-block">
      <div class="info-block-title">Supplier Information</div>
      <div class="info-row">
        <span class="info-key">Company Name:</span>
        <span class="info-val">MedSupply Ghana Ltd.</span>
      </div>
      <div class="info-row">
        <span class="info-key">Phone Number:</span>
        <span class="info-val">+233 30 123 4567</span>
      </div>
      <div class="info-row">
        <span class="info-key">Email:</span>
        <span class="info-val">supply@medsupply.gh</span>
      </div>
      <div class="info-row">
        <span class="info-key">Purchase Order:</span>
        <span class="info-val">PO-2025-00187</span>
      </div>
    </div>
    <div class="info-block">
      <div class="info-block-title">Delivery Details</div>
      <div class="info-row">
        <span class="info-key">Waybill No.:</span>
        <span class="info-val">WB-98234</span>
      </div>
      <div class="info-row">
        <span class="info-key">Award Letter:</span>
        <span class="info-val">AL-2025-019</span>
      </div>
      <div class="info-row">
        <span class="info-key">Received By:</span>
        <span class="info-val">John Mensah</span>
      </div>
      <div class="info-row">
        <span class="info-key">Status Filter:</span>
        <span class="info-val">Active &amp; Pending</span>
      </div>
    </div>
  </div> --}}

  <!-- TABLE -->
  <div class="table-section">
    <div class="table-header-bar">Items Re-Order Level Breakdown</div>
    <table  class="table table-bordered "  >
            <thead>
                    <tr>
                    <th>ID</th>
                    <th>Item Code</th>
                    <th >Item Name</th>
                    <th>Category</th>
                    <th>UoM</th>
                    <th >Store</th>
                    <th>ReOrder Level</th>
                    <th>Stock Level</th>
                    
                        
                </tr>
            </thead>
            <tbody>
                    
                    @if($liststock)
                    @foreach($liststock as $lists)
                    <tr>
                        <td>{{ $loop->iteration}}</td>
                        <td> {{ $lists->item_code }}</td>
                        <td>{{ $lists->name}}</td>
                        <td>{{$lists->categoryname->name}}</td>
                        <td>{{$lists->unitname->name}}</td>
                        <td>{{$lists->storename->name}}</td>
                        <td>  <b> {{ $lists->reorder_level }}</b>
                        <td><b>{{ $lists->total_qty ?? 0 }}</b></td>
                
                        </td>
                            
                    </tr>
                        
                    
                    @endforeach
                    @endif
                    
            </tbody>
        </table>
  </div>

  <!-- SUMMARY -->
  {{-- <div class="summary-section">
    <div class="summary-box">
      <div class="summary-title">Summary</div>
      <div class="summary-row">
        <span class="s-label">Subtotal</span>
        <span class="s-val">GH₵ 38,500.00</span>
      </div>
      <div class="summary-row">
        <span class="s-label">Tax (0%)</span>
        <span class="s-val">GH₵ 0.00</span>
      </div>
      <div class="summary-row">
        <span class="s-label">Retainage (0%)</span>
        <span class="s-val">GH₵ 0.00</span>
      </div>
      <div class="summary-row">
        <span class="s-label">Total Amount Due</span>
        <span class="s-val">GH₵ 38,500.00</span>
      </div>
    </div>
  </div> --}}

  <!-- FOOTER -->
  <div class="footer">
    <div class="footer-left">
      <span>Generated by Stock Shield v1.0</span>
      <span>·</span>
      <span>Report Date: {{ date("Y-m-d") }}</span>
      <span>·</span>
      <span>Confidential — Internal Use Only</span>
    </div>
    <div class="footer-right">Page 1 of 1</div>
  </div>

</div>
</body>
</html>
