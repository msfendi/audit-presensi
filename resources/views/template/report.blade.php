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
                    {{-- @php
                        dd($attendanceCollection->first());
                    @endphp --}}
                    @foreach($groupedByNPK as $npk => $npkCollection)
                        @if($bagian == $npkCollection[0]->BAG)
                            <tr>
                                <td style="width: 100px;">{{ $npkCollection[0]->BAG }}</td>
                                <td>{{ $npkCollection[0]->NPK }}</td>
                                <td style="width: 100px;">{{ $npkCollection[0]->NAMA_KARYAWAN }}</td>

                                {{-- @if($dateCollection[0]->TANGGAL == \Carbon\Carbon::parse($date)->format('Y-m-d') && $dateCollection[0]->NPK == $npkCollection[0]->NPK) --}}
                                @for($date = $startOfMonth; $date <= $endOfMonth; $date++)
                                    <td class="date-cell">
                                        @foreach($groupedByTanggal as $dateCollection => $dateArray) 
                                            @for($i = 0; $i < count($dateArray); $i++)
                                            
                                                @if($date == \Carbon\Carbon::parse($dateArray[0]->TANGGAL)->format('d'))
                                                    @php
                                                        $keterangan = '-';
                                                        $jamMasuk = '-';
                                                        $jamPulang = '-';
                                                    @endphp
                                                    @if($dateArray[$i]->NPK == $npk)
                                                        @php
                                                            $keterangan = $dateArray[$i]->KETERANGAN;
                                                            $jamMasuk = $dateArray[$i]->JAM_PAGI;
                                                            $jamPulang = $dateArray[$i]->JAM_SIANG;
                                                        @endphp
                                                    @endif

                                                {{-- @else 
                                                    <div class="status">
                                                        -
                                                    </div>
                                                    <div class="status">
                                                        -
                                                    </div>
                                                    <div class="status">
                                                        -
                                                    </div> --}}
                                                @endif
                                            @endfor
                                        @endforeach
                                        
                                        <div class="status">
                                            {{ $keterangan}}
                                        </div>
                                        <div class="status">
                                            {{ $jamMasuk }}
                                        </div>
                                        <div class="status">
                                            {{ $jamPulang}}
                                        </div>
                                    </td>



                                {{-- @php
                                    dd($groupedByNPK);
                                @endphp --}}
                                    {{-- @foreach($groupedByTanggal as $dateCollection => $dateArray)  --}}
                                    {{-- @php
                                        dd(count($dateArray));
                                    @endphp --}}
                                            {{-- @for($i = 0; $i < count($dateArray); $i++) --}}
                                                {{-- @if(\Carbon\Carbon::parse($dateArray[0]->TANGGAL)->format('d') == $date && $dateArray[0]->NPK == $npk)     --}}
                                                    {{-- <td class="date-cell"> --}}
                                                        {{-- <div class="status">
                                                            {{ $dateArray[0]->KETERANGAN }}
                                                        </div>
                                                        <div class="status">
                                                            {{ $dateArray[0]->NPK }}
                                                        </div>
                                                        <div class="status">
                                                            {{ $dateArray[0]->NAMA_KARYAWAN }}
                                                        </div> --}}
                                                    {{-- </td> --}}
                                                {{-- @else 
                                                    <td class="date-cell">
                                                        <div class="status">
                                                            -
                                                        </div>
                                                        <div class="status">
                                                            -
                                                        </div>
                                                        <div class="status">
                                                            -
                                                        </div>
                                                    </td>
                                                @endif --}}
                                            {{-- @endfor --}}
                                    {{-- @endforeach --}}
                                @endfor
                                <td>{{'keterangan'}}</td>
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
</html>