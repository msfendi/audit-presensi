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
        <table>
        @if($getTotalDays == null)
            @php
                $getTotalDays = \Carbon\Carbon::now()->month(\Carbon\Carbon::parse($employees[0]->TANGGAL)->format('m'))->daysInMonth;
            @endphp
        @endif
        <tr>
            <th>Dept</th>
            <th>NPK</th>
            <th >Nama Karyawan</th>
            @for($date = 1; $date <= $getTotalDays; $date++)
                <th>{{ $date }}</th>
            @endfor
            <th>Keterangan</th>
        </tr>
        @foreach($employeeGroup as $group)
            <tr>
                <td>
                    {{ $group->BAG }}
                </td>
                <td>
                    {{ $group->NPK }}
                </td>
                <td>
                    {{ $group->NAMA_KARYAWAN }}
                </td>
                    @foreach($employees as $employee)
                        @if($group->NPK == $employee->NPK)
                            @for($loopDays;$loopDays < (int)\Carbon\Carbon::parse($employee->TANGGAL)->format('d');$loopDays++)
                            <td>-</td>
                            @endfor
                            @php
                                $loopDays = (int)\Carbon\Carbon::parse($employee->TANGGAL)->format('d') + 1 ;
                            @endphp
                            <td>{{$employee->JAM_PAGI}}</td>
                        @else
                        @php
                            $loopDays = 1;
                        @endphp
                        @endif
                    @endforeach
            </tr>
        @endforeach
        </table>
    </div>
</body>
</html>