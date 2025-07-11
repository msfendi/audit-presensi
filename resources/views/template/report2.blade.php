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

$lastDate = 0;

@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kehadiran Karyawan</title>
    <style>
        @page { 
            size: 13in 8.5in;
            margin: 10px;
            orientation: landscape;
            /* margin: 20px; margin-top: 40;  */
        }
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
        @for($head = 0; $head < count($employeeGroup); $head++)
            @if($deptBefore != $employeeGroup[$head]->KODE_BAGIAN)
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
                    $deptBefore = $employeeGroup[$head]->KODE_BAGIAN;
                @endphp
            @endif
            <tr>
                <td>
                    {{ $employeeGroup[$head]->SUBDIVISI }}
                </td>
                <td>
                    {{ $employeeGroup[$head]->NPK }}
                </td>
                <td>
                    {{ $employeeGroup[$head]->NAMA_KARYAWAN }}
                </td>
                    @for($i = 0; $i < count($employees); $i++)
                    <!-- NPK Sama -->
                        
                        @if($employeeGroup[$head]->NPK == $employees[$i]->NPK && $employeeGroup[$head]->KODE_BAGIAN == $employees[$i]->KODE_BAGIAN && $employeeGroup[$head]->SUBDIVISI == $employees[$i]->SUBDIVISI)
                            @for($loopDays;$loopDays < (int)\Carbon\Carbon::parse($employees[$i]->TANGGAL)->format('d');$loopDays++)
                                <!-- Tidak ada absen -->
                                <td>-<br> - </td>
                            @endfor
                            <!-- Tanggal null -->
                            @if($employees[$i]->TANGGAL == null || (int)\Carbon\Carbon::parse($employees[$i]->TANGGAL)->format('d') == $getTotalDays)
                                @php
                                    $loopDays = $getTotalDays;
                                @endphp
                                <td>{{'-'}} <br> {{'-'}}</td>
                            @else
                            <!-- Ada tanggal -->
                            <td><div class="mb-2">
                                {{$employees[$i]->JAM_PAGI != null ? $employees[$i]->JAM_PAGI : ($employees[$i]->JAM_SIANG != null ? $employees[$i]->JAM_SIANG : '-')}}
                            </div>
                                <div class="mb-2">
                                    {{$employees[$i]->JAM_SIANG != null ? $employees[$i]->JAM_SIANG : ($employees[$i]->JAM_MALAM != null ? $employees[$i]->JAM_MALAM : '-')}}
                                </div>
                                <div>
                                    {{$employees[$i]->KETERANGAN != null ? $employees[$i]->KETERANGAN : (($employees[$i]->JAM_PAGI != null || $employees[$i]->JAM_SIANG != null) ? 'MSK' : 'LBR')}}
                                </div>
                            </td>

                            @endif
                            @php
                                $loopDays++;
                                $lastDate = (int)\Carbon\Carbon::parse($employees[$i]->TANGGAL)->format('d');
                            @endphp
                            
                        @else
                        <!-- Beda NPK -->
                        @php
                            $loopDays = 1;
                        @endphp
                        @endif
                    @endfor
                    @for($sisa = $lastDate; $sisa < $getTotalDays; $sisa++)
                        <td>-</td>
                    @endfor
                    <td>Jam Masuk <br> Jam Pulang <br> Keterangan </td>
            </tr>
        @endfor
        </table>
    </div>
</body>
</html>