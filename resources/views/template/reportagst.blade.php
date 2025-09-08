<!DOCTYPE html>
<html lang="en">
@php
$keterangan = '-';
$jamMasuk = '-';
$jamPulang = '-';
$deptBefore = '';
$npkBefore = '';

$getTotalDays = null;
$getTanggal = false;
$sameNPK = false;

$loopDays = 1;

$lastDate = 0;

$mayDate = [1,2,5,6,7,8,9];


@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kehadiran Karyawan</title>
    <style>
        @page { 
            size: 13in 8.5in;
            margin: 20px;
            /* margin: 20px; margin-top: 40;  */
        }
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            width: 13in;
            height: 8.5in;
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
            border: 1px solid black;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: black;
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
                background-color: black;
                color: white;
                font-weight: bold;
                print-color-adjust: exact;
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
        @for($i = 0; $i < 31; $i++)
            @if($employees[$i]->TANGGAL != null && $getTanggal == false)
                <h2>Data Kehadiran Karyawan - {{\Carbon\Carbon::parse($employees[$i]->TANGGAL)->format('F Y')}}</h2>
                @php
                    $getTotalDays = \Carbon\Carbon::parse($employees[$i]->TANGGAL)->daysInMonth;
                    $getTanggal = true;
                @endphp
            @endif
        @endfor
    </div>

    <div class="table-container">
        <table>
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
                    @for($i = 0; $i < count($employees); $i++)
                    <!-- NPK Sama -->
                        @if($employeeGroup[$head]->NPK == $employees[$i]->NPK)
                            @if($sameNPK == false)
                                <td>
                                    {{ $employees[$i]->SUBDIVISI }}
                                </td>
                                <td>    
                                    {{ $employees[$i]->NPK }}
                                </td>
                                <td>
                                    {{ $employees[$i]->NAMA_KARYAWAN }}
                                </td>
                                @php
                                    $sameNPK = true;
                                @endphp
                            @endif
                            @for($loopDays;$loopDays < (int)\Carbon\Carbon::parse($employees[$i]->TANGGAL)->format('d');$loopDays++)
                                <!-- Tidak ada absen -->
                                @if($loopDays == 2 ||  $loopDays == 3 || $loopDays == 9 ||  $loopDays == 10 || $loopDays == 16 || $loopDays == 17 || $loopDays == 23 || $loopDays == 24 || $loopDays == 30 || $loopDays == 31)
                                    <td>-<br> - <br> LBR</td>
                                @else
                                    {{-- @if($loopDays == 17 || $loopDays == 18 || $loopDays == 24 || $loopDays == 25 || $loopDays == 31)
                                        <td>-<br> - <br> LBR</td>
                                    @else --}}
                                        <td>-<br> - <br> MA </td>
                                    {{-- @endif --}}
                                @endif
                            @endfor

                            <!-- Tanggal null -->
                            @if($employees[$i]->TANGGAL == null)
                                @php
                                    $loopDays = $getTotalDays;
                                @endphp
                                <td>{{'-'}} <br> {{'-'}} <br> MA</td> {{-- Not execute --}}
                            @else
                            
                            <!-- Ada tanggal -->
                            <td><div class="mb-2">
                                {{$employees[$i]->JAM_PAGI != null ? $employees[$i]->JAM_PAGI : ($employees[$i]->JAM_SIANG != null ? $employees[$i]->JAM_SIANG : '-')}}
                            </div>
                                <div class="mb-2">
                                    {{$employees[$i]->JAM_MALAM != null ? $employees[$i]->JAM_MALAM : ($employees[$i]->JAM_SIANG != null ? $employees[$i]->JAM_SIANG : '-')}}
                                </div>

                                @if(Carbon\Carbon::parse($employees[$i]->TANGGAL)->isWeekend() && ($employees[$i]->JAM_PAGI != null || $employees[$i]->JAM_SIANG != null || $employees[$i]->JAM_MALAM != null))
                                    <div class="mb-2">
                                        MSK
                                    </div>
                                @elseif((Carbon\Carbon::parse($employees[$i]->TANGGAL)->isWeekend()))
                                    <div class="mb-2">
                                        LBR
                                    </div>
                                @elseif(Carbon\Carbon::parse($employees[$i]->TANGGAL)->format('d') == '10')
                                    <div class="mb-2">
                                        {{$employees[$i]->JAM_PAGI != null || $employees[$i]->JAM_SIANG != null || $employees[$i]->JAM_MALAM != null ? 'MSK' : 'LBR'}}
                                    </div>
                                @else
                                    <div>
                                        {{$employees[$i]->KETERANGAN != null ? $employees[$i]->KETERANGAN : (($employees[$i]->JAM_PAGI != null || $employees[$i]->JAM_SIANG != null || $employees[$i]->JAM_MALAM != null) ? 'MSK' : 'MA')}}
                                    </div>
                                @endif
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
                    @if($sisa == 22 || $sisa == 23 || $sisa == 29 || $sisa == 30)
                        <td> - <br> LBR</td>
                    @else
                        <td>-<br> - <br> MA</td>
                    @endif
                        {{-- <td>{{'-'}} <br> {{'-'}} <br> MA <br>{{$sisa}}</td> --}}
                    @endfor
                    <td>Jam Masuk <br> Jam Pulang <br> Keterangan </td>
                    @php
                        $sameNPK = false;
                    @endphp
            </tr>
        @endfor
        </table>
    </div>
</body>
</html>