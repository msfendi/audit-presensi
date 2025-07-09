<!DOCTYPE html>
<html lang="en">
@php
$keterangan = '-';
$jamMasuk = '-';
$jamPulang = '-';
$deptBefore = '';
$npkBefore = '';

$getTotalDays = null;

$loopDays = 1;
$totalDays = 0;

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
        <h2>Data Kehadiran Karyawan - {{ \Carbon\Carbon::parse($employees[0]->TANGGAL)->format('F') }} {{ \Carbon\Carbon::parse($employees[0]->TANGGAL)->format('Y') }}</h2>
    </div>

    <div class="table-container">
        <table>
        @if($getTotalDays == null)
            @php
                $getTotalDays = \Carbon\Carbon::now()->month(\Carbon\Carbon::parse($employees[0]->TANGGAL)->format('m'))->daysInMonth;
            @endphp
        @endif
                @foreach($employees as $employee)
                    @if($deptBefore != $employee->BAG)      
                        <tr>
                            <th>Dept</th>
                            <th>NPK</th>
                            <th >Nama Karyawan</th>
                            @for($date = 1; $date <= $getTotalDays; $date++)
                                <th>{{ $date }}</th>
                            @endfor
                            <th>Keterangan</th>
                        </tr>
                        @php
                            $deptBefore = $employee->BAG;
                        @endphp
                    @endif
                        @if($npkBefore != $employee->NPK)
                        <tr></tr>
                        <td>
                            {{ $employee->BAG }}
                        </td>
                        <td>
                            {{ $employee->NPK }}
                        </td>
                        <td>
                            {{ $employee->NAMA_KARYAWAN }}
                        </td>
                        @php
                            $loopDays = 1;
                            $npkBefore = $employee->NPK;
                            $totalDays = 0;
                        @endphp
                        @if($employee->TANGGAL == null || (int)\Carbon\Carbon::parse($employee->TANGGAL)->format('d') == 30)
                        @for($loopDays;$loopDays <= $getTotalDays;$loopDays++)
                                <td>-<br> {{$totalDays}}</td>
                            @endfor
                                <td>
                                    Keterangan
                                </td>
                        @endif
                        
                            <!-- Kalau NPK Sama -->
                        @else
                            @for($loopDays;$loopDays < (int)\Carbon\Carbon::parse($employee->TANGGAL)->format('d');$loopDays++)
                                <td>-</td>
                            @php
                                $totalDays++;
                            @endphp
                            @endfor
                            @php
                                $loopDays = (int)\Carbon\Carbon::parse($employee->TANGGAL)->format('d') + 1 ;
                                $totalDays++;
                            @endphp
                            <td>{{$employee->JAM_PAGI}} <br> {{$totalDays}}</td>
                            @if($loopDays == $getTotalDays+1)
                                <td>
                                    Keterangan
                                </td>
                            @endif
                        @endif
                    @endforeach
        </table>
    </div>
</body>
</html>