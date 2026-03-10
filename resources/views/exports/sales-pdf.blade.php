<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; }
        .header { background: #059669; color: white; padding: 15px 20px; margin-bottom: 15px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 10px; opacity: 0.85; margin-top: 4px; }
        .kpi-row { display: flex; gap: 12px; padding: 10px 20px; background: #f0fdf4; margin-bottom: 15px; }
        .kpi { text-align: center; flex: 1; }
        .kpi strong { display: block; font-size: 16px; color: #065f46; }
        .kpi span { font-size: 9px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #475569; font-size: 8px; text-transform: uppercase; padding: 6px 8px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; }
        .value { color: #059669; font-weight: bold; }
        .footer { margin-top: 15px; font-size: 8px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>💰 Laporan Penjualan — WebCare CRM</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($to)->format('d M Y') }} &nbsp;|&nbsp; Digenerate: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="kpi-row">
        <div class="kpi"><strong>{{ $totalDeals }}</strong><span>Deal Menang</span></div>
        <div class="kpi"><strong>Rp{{ number_format($totalValue / 1000000, 1) }}M</strong><span>Total Revenue</span></div>
        <div class="kpi"><strong>Rp{{ $totalDeals ? number_format(($totalValue / $totalDeals) / 1000000, 1) : 0 }}M</strong><span>Rata-rata Deal</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Judul Deal</th>
                <th>Lead</th>
                <th>Salesperson</th>
                <th>Nilai</th>
                <th>Tanggal Menang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deals as $i => $deal)
            <tr>
                <td style="color:#94a3b8">{{ $i + 1 }}</td>
                <td><strong>{{ $deal->title }}</strong></td>
                <td>{{ $deal->lead?->name ?: '—' }}</td>
                <td>{{ $deal->assignedUser?->name ?: '—' }}</td>
                <td class="value">Rp{{ number_format($deal->value, 0, ',', '.') }}</td>
                <td>{{ $deal->closed_date ? \Carbon\Carbon::parse($deal->closed_date)->format('d M Y') : '—' }}</td>
            </tr>
            @endforeach
            <tr style="background:#f0fdf4; font-weight:bold;">
                <td colspan="4" style="text-align:right; padding-right:8px; color:#065f46;">TOTAL</td>
                <td class="value">Rp{{ number_format($totalValue, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Document ini digenerate secara otomatis oleh WebCare CRM &copy; {{ date('Y') }}
    </div>
</body>
</html>
