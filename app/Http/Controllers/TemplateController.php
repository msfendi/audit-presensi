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
        $employeeGroup = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'LIKE', "%LINE%")->orderBy('BIODATA.ID_DEPT', 'ASC')->orderBy('BIODATA.NPK', 'ASC')->get();
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'LIKE', "%LINE%")->orderBy('BIODATA.ID_DEPT', 'ASC')->orderBy('BIODATA.NPK', 'ASC')->orderBy('AUDIT.TANGGAL', 'ASC')->get();
        // dd($employees);
        return view('template.report2', compact('employees', 'employeeGroup'));
    }

    public function auditnonsewing()
    {
        $employeeGroup = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%")->orderBy('BIODATA.ID_DEPT', 'ASC')->orderBy('BIODATA.NPK', 'ASC')->get();
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%")->orderBy('BIODATA.ID_DEPT', 'ASC')->orderBy('BIODATA.NPK', 'ASC')->orderBy('AUDIT.TANGGAL', 'ASC')->get();
        // dd($employees);
        return view('template.report2', compact('employees', 'employeeGroup'));
    }
}
