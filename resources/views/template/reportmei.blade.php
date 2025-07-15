<!DOCTYPE html>
<html lang="en">
@php
$keterangan = '-';
$jamMasuk = '-';
$jamPulang = '-';
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kehadiran Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 11px;
            min-width: 28px;
        }
        
        td {
            font-size: 10px;
            min-width: 28px;
        }
        
        .employee-name {
            text-align: left;
            padding-left: 5px;
            min-width: 100px;
            font-size: 11px;
        }
        
        .employee-nip {
            min-width: 80px;
            font-size: 10px;
        }
        
        .date-cell {
            line-height: 1.2;
        }
        
        .status {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .time {
            font-size: 8px;
            color: #666;
            margin: 1px 0;
        }
        
        @media print {
            body {
                margin: 10px;
                font-size: 10px;
            }
            
            .header h2 {
                font-size: 16px;
            }
            
            th {
                font-size: 9px;
                padding: 2px;
            }
            
            td {
                font-size: 8px;
                padding: 2px;
            }
            
            .employee-name {
                font-size: 9px;
            }
            
            .time {
                font-size: 7px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Data Kehadiran Karyawan - Juli 2025</h2>
    </div>

    <div class="table-container">
        @foreach($groupedByBag as $bagian => $bagianCollection)
            <table>
                <thead>
                    @php
                        $startOfMonth = \Carbon\Carbon::parse($groupedByTanggal->first()[0]->TANGGAL)->startOfMonth()->format('d');
                        $endOfMonth = \Carbon\Carbon::parse($groupedByTanggal->first()[0]->TANGGAL)->endOfMonth()->format('d');
                    @endphp
                    <tr>
                        <th></th>
                        <th>NPK</th>
                        <th >Nama Karyawan</th>
                        <!-- Loop tanggal 1-31 -->
                        @for($date = $startOfMonth; $date <= $endOfMonth; $date++)
                            <th>{{ $date }}</th>
                        @endfor
                        <th>Keterangan</th>
                    </tr>
                </thead>    
                <tbody>
                    @foreach($groupedByNPK as $npk => $npkCollection)
                        @if($bagian == $npkCollection[0]->BAG)
                            <tr>
                                <td style="width: 100px;">{{ $npkCollection[0]->BAG }}</td>
                                <td>{{ $npkCollection[0]->NPK }}</td>
                                <td style="width: 100px;">{{ $npkCollection[0]->NAMA_KARYAWAN }}</td>

                                @for($date = $startOfMonth; $date <= $endOfMonth; $date++)
                                    <td>
                                    @foreach($groupedByTanggal as $dateCollection => $dateArray) 
                                        @for($i = 0; $i < count($dateArray); $i++)
                                            @if($date == \Carbon\Carbon::parse($dateArray[$i]->TANGGAL)->format('d') && $dateArray[$i]->NPK == $npk)
                                                @php
                                                    $keterangan = $dateArray[$i]->KETERANGAN;
                                                    $jamMasuk = $dateArray[$i]->JAM_PAGI;
                                                    $jamPulang = $dateArray[$i]->JAM_SIANG;
                                                @endphp
                                            @endif
                                        @endfor
                                    @endforeach
                                        <div class="status">
                                            {{ $keterangan }}
                                        </div>
                                        <div class="status">
                                            {{ $jamMasuk }}
                                        </div>
                                        <div class="status">
                                            {{ $jamPulang }}
                                        </div>
                                        @php
                                            $keterangan = '-';
                                            $jamMasuk = '-';
                                            $jamPulang = '-';
                                        @endphp
                                    </td>
                                @endfor
                                <td>
                                    <div class="status">
                                        {{ 'Status' }}
                                    </div>
                                    <div class="status">
                                        {{ 'Jam Datang' }}
                                    </div>
                                    <div class="status">
                                        {{ 'Jam Pulang' }}
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <br>
            <br>
        @endforeach
    </div>
</body>
</html>22:00 7/8/202522:49 7/8/2025