<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class TemplateController extends Controller
{

    public function audit()
    {
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*', 'AUDIT.TANGGAL', 'AUDIT.SUBDIVISI', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK');
        $employeesLeaves = DB::connection('sqlsrv')->table('BIODATA_KELUAR')->select('BIODATA_KELUAR.*', 'AUDIT.TANGGAL', 'AUDIT.SUBDIVISI', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM',  'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA_KELUAR.NPK', '=', 'AUDIT.NPK');

        // $unionEmployees = $employees->union($employeesLeaves)->get()->groupBy(['BAG', 'NPK', 'TANGGAL']);

        $unionEmployees = $employees->get();

        // dd($unionEmployees);

        $groupedByBag = $unionEmployees->groupBy('BAG');
        $groupedByNPK = $unionEmployees->groupBy('NPK');
        $groupedByTanggal = $unionEmployees->groupBy('TANGGAL');

        // $groupedByBag = Cache::remember(
        //     'bag',
        //     24000,
        //     function () use ($unionEmployees) {
        //         return $unionEmployees->groupBy('BAG');
        //     }
        // );

        // $groupedByNPK = Cache::remember(
        //     'npk',
        //     24000,
        //     function () use ($unionEmployees) {
        //         return $unionEmployees->groupBy('NPK');
        //     }
        // );

        // $groupedByTanggal = Cache::remember(
        //     'tanggal',
        //     24000,
        //     function () use ($unionEmployees) {
        //         return $unionEmployees->groupBy('TANGGAL');
        //     }
        // );

        // dd($groupedByTanggal->first());

        return view('template.report', compact([
            'groupedByBag',
            'groupedByNPK',
            'groupedByTanggal'
        ]));
    }

    public function auditsewing()
    {
        // ini_set('max_execution_time', 36000);

        $employeeGroupChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->distinct('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->where('SUBDIVISI', 'LIKE', "%LINE%");
        $employeeGroup = $employeeGroupChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->get();

        $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')->where('SUBDIVISI', 'LIKE', "%LINE%")->limit(15010);
        $employees = $employeesChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        // dd($employees);  

        // $pdf = Pdf::loadView('/template/report2', compact('employees', 'employeeGroup'));
        // return $pdf->download('Data Absen.pdf');
        return view('template.report2', compact('employees', 'employeeGroup'));
    }

    public function auditnonsewing()
    {
        $employeeGroupChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->distinct('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employeeGroup = $employeeGroupChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->get();

        $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employees = $employeesChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        // dd($employeesChutex->get());
        return view('template.report2', compact('employees', 'employeeGroup'));





        

        // ini_set('memory_limit', '512M');
        // ini_set('max_execution_time', 600);

        // $chunkSize = 1000; // Process 1000 records at a time
        // $employees = collect();

        // $baseQuery = DB::connection('sqlsrv')->table('AUDIT')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");

        // // if ($request->department === 'sewing') {
        // //     $baseQuery->where('SUBDIVISI', 'LIKE', "%LINE%");
        // // } else {
        // //     $baseQuery->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        // // }

        // // Process data in chunks
        // $baseQuery->clone()
        //     ->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')
        //     ->orderBy('KODE_BAGIAN', 'ASC')
        //     ->orderBy('NPK', 'ASC')
        //     ->orderBy('TANGGAL', 'ASC')
        //     ->chunk($chunkSize, function ($employeesy) use (&$employees) {
        //         $employees = $employees->concat($employeesy);
        //     });

        // $employeeGroup = $baseQuery->clone()
        //     ->select('NPK', 'KODE_BAGIAN', 'SUBDIVISI')
        //     ->distinct()
        //     ->orderBy('KODE_BAGIAN', 'ASC')
        //     ->orderBy('NPK', 'ASC')
        //     ->get();

        // return view('template.report2', compact('employees', 'employeeGroup'));
    }
}
