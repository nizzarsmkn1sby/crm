<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Lead</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; }
        .header { background: #4f46e5; color: white; padding: 15px 20px; margin-bottom: 15px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 10px; opacity: 0.8; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #475569; font-size: 8px; text-transform: uppercase; padding: 6px 8px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        tr:hover td { background: #fafafa; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: 600; }
        .badge-new { background: #dbeafe; color: #1e40af; }
        .badge-won { background: #d1fae5; color: #065f46; }
        .badge-lost { background: #fee2e2; color: #991b1b; }
        .badge-contacted { background: #ede9fe; color: #5b21b6; }
        .badge-qualified { background: #fef3c7; color: #92400e; }
        .badge-proposal { background: #e0f2fe; color: #075985; }
        .badge-negotiation { background: #fdf4ff; color: #7e22ce; }
        .footer { margin-top: 15px; font-size: 8px; color: #94a3b8; text-align: center; }
        .summary { padding: 10px 20px; background: #f8fafc; display: flex; gap: 20px; margin-bottom: 15px; font-size: 10px; }
        .summary strong { font-size: 14px; display: block; color: #4f46e5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Export Data Lead — WebCare CRM</h1>
        <p>Digenerate: {{ now()->format('d F Y, H:i') }} WIB &nbsp;|&nbsp; Total: {{ $leads->count() }} lead</p>
    </div>

    <div class="summary">
        <div><strong>{{ $leads->count() }}</strong> Total Lead</div>
        <div><strong>{{ $leads->where('status', 'won')->count() }}</strong> Won</div>
        <div><strong>{{ $leads->where('status', 'new')->count() }}</strong> Baru</div>
        <div><strong>Rp{{ number_format($leads->sum('estimated_value') / 1000000, 1) }}M</strong> Total Nilai</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Perusahaan</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Status</th>
                <th>Prioritas</th>
                <th>Sumber</th>
                <th>Nilai Estimasi</th>
                <th>Salesperson</th>
                <th>Stage</th>
                <th>Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leads as $i => $lead)
            <tr>
                <td style="color:#94a3b8">{{ $i + 1 }}</td>
                <td><strong>{{ $lead->name }}</strong></td>
                <td>{{ $lead->company ?: '—' }}</td>
                <td>{{ $lead->email ?: '—' }}</td>
                <td>{{ $lead->phone ?: '—' }}</td>
                <td><span class="badge badge-{{ $lead->status }}">{{ ucfirst($lead->status) }}</span></td>
                <td>{{ ucfirst($lead->priority) }}</td>
                <td>{{ ucfirst($lead->source) }}</td>
                <td>{{ $lead->estimated_value ? 'Rp' . number_format($lead->estimated_value, 0, ',', '.') : '—' }}</td>
                <td>{{ $lead->assignedUser?->name ?: '—' }}</td>
                <td>{{ $lead->pipelineStage?->name ?: '—' }}</td>
                <td>{{ $lead->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document ini digenerate secara otomatis oleh WebCare CRM &copy; {{ date('Y') }}
    </div>
</body>
</html>
