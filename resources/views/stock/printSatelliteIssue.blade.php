<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Satellite Issue Slip — {{ $issueNo }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --ink: #0f1923;
    --ink-soft: #3a4a58;
    --ink-mute: #6b7f8e;
    --accent: #b45309;
    --accent-light: #fff7ed;
    --accent-mid: #d97706;
    --border: #d0dde6;
    --bg: #f5f8fa;
    --white: #ffffff;
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

  .header {
    background: var(--ink);
    color: var(--white);
    padding: 32px 40px 28px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
  }

  .logo-line {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
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

  .report-title {
    font-family: 'DM Serif Display', serif;
    font-size: 30px;
    letter-spacing: 0.2px;
    line-height: 1.1;
    text-align: right;
  }

  .report-sub {
    font-size: 13px;
    color: #8fa5b5;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-top: 6px;
    text-align: right;
  }

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

  .info-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    border-bottom: 1px solid var(--border);
    background: var(--white);
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

  .table-section {
    overflow-x: auto;
    padding: 0;
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
    background: var(--white);
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
  }

  tbody tr:nth-child(even) { background: #fafcfc; }

  tbody td {
    padding: 12px 10px;
    font-size: 16px;
    color: var(--ink-soft);
    vertical-align: middle;
  }

  tbody td:first-child { padding-left: 10px; }
  tbody td:last-child { padding-right: 10px; }

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

  .print-bar {
    max-width: 1100px;
    margin: 0 auto 16px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

  .btn-print, .btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    padding: 10px 22px;
    border-radius: 4px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    letter-spacing: 0.3px;
    text-decoration: none;
  }

  .btn-print {
    background: var(--accent);
    color: var(--white);
    box-shadow: 0 2px 8px rgba(180,83,9,0.25);
  }

  .btn-print:hover { background: var(--accent-mid); }

  .btn-back {
    background: var(--white);
    color: var(--ink);
    border: 1px solid var(--border);
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
      padding: 0 !important;
      background: white !important;
    }

    * {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      color-adjust: exact !important;
    }

    .header { background: #0f1923 !important; color: #ffffff !important; }
    .meta-bar { background: #b45309 !important; color: #ffffff !important; border-bottom-color: #d97706 !important; }
    .meta-cell { color: #ffffff !important; }
    .meta-label { color: rgba(255,255,255,0.65) !important; }
    .meta-value { color: #ffffff !important; }
    .table-header-bar { background: #0f1923 !important; color: #ffffff !important; }
    thead tr { background: #fff7ed !important; }
    thead th { color: #b45309 !important; border-bottom-color: #d97706 !important; }
    tfoot tr { background: #0f1923 !important; color: #ffffff !important; }
    tfoot td { color: #ffffff !important; }
    .info-block-title { color: #b45309 !important; border-bottom-color: #fff7ed !important; }
    .footer { background: #f5f8fa !important; }

    @page {
      margin: 10mm 12mm;
      size: A4 landscape;
    }

    table { font-size: 14px; }
    thead th { font-size: 14px; }
    tbody td { font-size: 15px; }
    thead th, tbody td, tfoot td { white-space: nowrap; }
  }
</style>
</head>
<body>

<div class="print-bar">
  <a href="{{ route('IssueItemSatellite') }}" class="btn-back">← Back to Issue Item</a>
  <button class="btn-print" onclick="window.print()">Print Issue Slip</button>
</div>

<div class="page">
  <div class="header">
    <div class="header-left">
      <div class="logo-line">
        <img src="{{ asset('backend/assets/img/logo (2).png') }}" alt="" width="70">
        <div>
          <div class="org-name">Supply & Logistics Department — KBTH</div>
          <div class="org-sub">Stock Shield · Satellite Store</div>
        </div>
      </div>
    </div>
    <div>
      <div class="report-title">Issue Slip</div>
      <div class="report-sub">#{{ $issueNo }}</div>
    </div>
  </div>

  <div class="meta-bar">
    <div class="meta-cell">
      <div class="meta-label">Satellite Store</div>
      <div class="meta-value">{{ $store->name ?? '—' }}</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Issued To ({{ $destinationLabel ?? 'Ward' }})</div>
      <div class="meta-value">{{ $wardLabel }}</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Issued On</div>
      <div class="meta-value">
        {{ $issuedAt ? \Carbon\Carbon::parse($issuedAt)->format('d M, Y h:i A') : date('d M, Y h:i A') }}
      </div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Total Lines</div>
      <div class="meta-value">{{ $lines->count() }} Items</div>
    </div>
  </div>

  <div class="info-section">
    <div class="info-block">
      <div class="info-block-title">Issue Details</div>
      <div class="info-row">
        <span class="info-key">Store:</span>
        <span class="info-val">{{ $store->name ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-key">Issue Number:</span>
        <span class="info-val">#{{ $issueNo }}</span>
      </div>
      <div class="info-row">
        <span class="info-key">Issued By:</span>
        <span class="info-val">{{ $issuedBy->name ?? auth()->user()->name ?? '—' }}</span>
      </div>
    </div>
    <div class="info-block">
      <div class="info-block-title">Recipient</div>
      <div class="info-row">
        <span class="info-key">{{ $destinationLabel ?? 'Ward' }}:</span>
        <span class="info-val">{{ $wardLabel }}</span>
      </div>
      <div class="info-row">
        <span class="info-key">Printed By:</span>
        <span class="info-val">{{ auth()->user()->name ?? '—' }}</span>
      </div>
      <div class="info-row">
        <span class="info-key">Printed On:</span>
        <span class="info-val">{{ date('d M, Y h:i A') }}</span>
      </div>
    </div>
  </div>

  <div class="table-section">
    <div class="table-header-bar">Issued Items Breakdown</div>
    <table>
      <thead>
        <tr>
          <th>No.</th>
          <th>Item Code</th>
          <th>Description</th>
          <th>UoM</th>
          <th>Batch</th>
          <th>Qty Issued</th>
        </tr>
      </thead>
      <tbody>
        @php $totalQty = 0; @endphp
        @foreach ($lines as $line)
          @php
            $totalQty += (int) $line->qty_issued;
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ optional($line->itemcode)->item_code ?? '—' }}</td>
            <td>{{ optional($line->itemname)->name ?? '—' }}</td>
            <td>{{ optional(optional($line->itemname)->unitname)->name ?? '—' }}</td>
            <td>{{ $line->batch_number ?? '—' }}</td>
            <td>{{ $line->qty_issued }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5"><span class="total-label" style="float: right;">Total Quantity</span></td>
          <td><strong>{{ $totalQty }}</strong></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="footer">
    <div>Generated by Stock Shield · {{ date('Y-m-d') }} · Confidential — Internal Use Only</div>
    <div>Page 1 of 1</div>
  </div>
</div>

</body>
</html>
