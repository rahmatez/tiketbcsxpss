<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #1a73e8;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #777;
            font-size: 0.9em;
        }
        .button {
            display: inline-block;
            background-color: #1a73e8;
            color: #fff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
        .info-block {
            background-color: #f8f9fa;
            border-left: 4px solid #1a73e8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .notification-icon {
            text-align: center;
            margin-bottom: 20px;
            font-size: 48px;
        }
    </style>
</head>
<body>    <div class="container">
        <div class="header">
            <h2>{{ $title }}</h2>
        </div>
        
        <div class="notification-icon">
            @php 
                $type = isset($data) && is_object($data) && isset($data->type) ? $data->type : 'default';
                if(is_object($data) && get_class($data) == 'App\Models\Game') {
                    $type = 'match_reminder';
                } elseif(is_object($data) && get_class($data) == 'App\Models\Order') {
                    $type = 'payment_reminder';
                }
            @endphp
            
            @switch($type)
                @case('match_reminder')
                    ⚽
                    @break
                @case('payment_reminder')
                    💳
                    @break
                @default
                    📢
            @endswitch
        </div>
        
        <div>
            <p>{{ $message }}</p>
            
            @if($type == 'match_reminder' && isset($data) && is_object($data) && get_class($data) == 'App\Models\Game')
                <div class="info-block">
                    <p><strong>Detail Pertandingan:</strong></p>
                    <p>Waktu: {{ \Carbon\Carbon::parse($data->match_time)->format('d F Y, H:i') }} WIB</p>
                    <p>Stadion: {{ $data->stadium_name }}</p>
                    <p>Pertandingan: {{ $data->home_team }} vs {{ $data->away_team }}</p>
                </div>
            @endif
            
            @if($type == 'payment_reminder' && isset($data) && is_object($data) && get_class($data) == 'App\Models\Order')
                <div class="info-block">
                    <p><strong>Detail Pesanan:</strong></p>
                    <p>ID Pesanan: {{ $data->id }}</p>
                    <p>Status: Menunggu Pembayaran</p>
                </div>
                
                <div style="text-align: center;">
                    <a href="{{ route('payment.detail', ['order' => $data->id]) }}" class="button">Selesaikan Pembayaran</a>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>Email ini dikirim dari sistem BCSXPSS.</p>
            <p>© {{ date('Y') }} BCSXPSS - Sistem Pemesanan Tiket Pertandingan</p>
        </div>
    </div>
</body>
</html>
