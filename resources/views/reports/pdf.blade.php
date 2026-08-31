<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 30px; }
        * { font-family: DejaVu Sans, sans-serif; }
        html {
            border: 1px dotted #9ca3af;
        }
        body { color: #1f2937; font-size: 12px; margin: 0; padding: 18px; }
        .header {
            border-bottom: 3px solid #006838;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 { color: #006838; font-size: 22px; margin: 0 0 4px; }
        .header .accent { height: 3px; background: #FDB913; width: 80px; margin-top: 6px; }
        .meta { color: #6b7280; font-size: 10px; margin-top: 6px; }
        .section { margin-bottom: 22px; }
        .section h2 {
            font-size: 14px;
            color: #00542d;
            margin: 0 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #006838;
            color: #fff;
            text-align: left;
            padding: 6px 10px;
            font-size: 11px;
        }
        td {
            padding: 6px 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        tr:nth-child(even) td { background: #f9fafb; }
        .value-col { text-align: right; font-weight: bold; }
        .empty { color: #9ca3af; font-style: italic; padding: 8px 10px; }
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            color: #9ca3af;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Collin Saunders Hospital</h1>
        <div style="font-size: 15px; font-weight: bold;">{{ $title }}</div>
        <div class="accent"></div>
        <div class="meta">
            Generated: {{ $generatedAt->format('d M Y, H:i') }} &nbsp;|&nbsp; By: {{ $generatedBy }}
        </div>
    </div>

    @forelse ($sections as $section)
        <div class="section">
            <h2>{{ $section['heading'] }}</h2>
            @if (empty($section['rows']))
                <div class="empty">No data available.</div>
            @elseif (isset($section['columns']))
                <table>
                    <thead>
                        <tr>
                            @foreach ($section['columns'] as $col)
                                <th>{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section['rows'] as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <table>
                    <tbody>
                        @foreach ($section['rows'] as $row)
                            <tr>
                                <td>{{ $row[0] }}</td>
                                <td class="value-col">{{ $row[1] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @empty
        <div class="empty">No report data available.</div>
    @endforelse

    <div class="footer">
        Confidential — Hospital Management System &copy; {{ $generatedAt->year }}
    </div>
</body>
</html>
