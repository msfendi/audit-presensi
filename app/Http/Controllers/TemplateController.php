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
        $employeeGroupChutex = DB::connection('sqlsrv')->table('AUDIT')->select('AUDIT.NPK', 'AUDIT.NAMA_KARYAWAN', 'AUDIT.SUBDIVISI')->distinct('AUDIT.NPK')->where('AUDIT.SUBDIVISI', 'LIKE', "%LINE%");
        // $employeeGroupOut = DB::connection('sqlsrv')->table('AUDIT')->select('AUDIT.NPK', 'AUDIT.NAMA_KARYAWAN', 'AUDIT.SUBDIVISI')->where('AUDIT.SUBDIVISI', 'LIKE', "%LINE%");
        // $employeeGroupJp = DB::connection('sqlsrv')->table('BIODATA_JP')->select('BIODATA_JP.ID_DEPT', 'BIODATA_JP.NPK', 'BIODATA_JP.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'BIODATA_JP.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'LIKE', "%LINE%");
        $employeeGroup = $employeeGroupChutex->orderBy('SUBDIVISI', 'ASC')->orderBy('NPK', 'ASC')->get();
        // $employeeGroup = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%")->orderBy('BIODATA.ID_DEPT', 'ASC')->orderBy('BIODATA.NPK', 'ASC')->get();

        $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('BIODATA.ID_DEPT', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('BIODATA', 'AUDIT.NPK', '=', 'BIODATA.NPK')->where('AUDIT.SUBDIVISI', 'LIKE', "%LINE%");
        $employeesOut = DB::connection('sqlsrv')->table('AUDIT')->select('BIODATA_KELUAR.ID_DEPT', 'BIODATA_KELUAR.NPK', 'BIODATA_KELUAR.NAMA_KARYAWAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('BIODATA_KELUAR', 'AUDIT.NPK', '=', 'BIODATA_KELUAR.NPK')->where('AUDIT.SUBDIVISI', 'LIKE', "%LINE%");
        // $employeesJP = DB::connection('sqlsrv')->table('BIODATA_JP')->select('BIODATA_JP.ID_DEPT', 'BIODATA_JP.NPK', 'BIODATA_JP.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA_JP.NPK', '=', 'AUDIT.NPK')->leftJoin('DEPT', 'BIODATA_JP.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'LIKE', "%LINE%");
        // $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%")->orderBy('BIODATA.ID_DEPT', 'ASC')->orderBy('BIODATA.NPK', 'ASC')->orderBy('AUDIT.TANGGAL', 'ASC')->get();
        $employees = $employeesChutex->union($employeesOut)->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        // dd($employees);  

        // $pdf = Pdf::loadView('/template/report2', compact('employees', 'employeeGroup'));
        // return $pdf->download('Data Absen.pdf');
        return view('template.report2', compact('employees', 'employeeGroup'));
    }

    public function auditnonsewing()
    {
        // $employeeGroupChutex = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.ID_DEPT', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%");
        $employeeGroupChutex = DB::connection('sqlsrv')->table('AUDIT')->select('AUDIT.NPK', 'AUDIT.NAMA_KARYAWAN', 'AUDIT.SUBDIVISI')->distinct('AUDIT.NPK')->where('AUDIT.SUBDIVISI', 'NOT LIKE', "%LINE%");
        // $employeeGroupOut = DB::connection('sqlsrv')->table('AUDIT')->select('AUDIT.NPK', 'AUDIT.NAMA_KARYAWAN', 'AUDIT.SUBDIVIS')->where('AUDIT.SUBDIVISI', 'NOT LIKE', "%LINE%");
        // $employeeGroupJp = DB::connection('sqlsrv')->table('BIODATA_JP')->select('BIODATA_JP.ID_DEPT', 'BIODATA_JP.NPK', 'BIODATA_JP.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'BIODATA_JP.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%");
        $employeeGroup = $employeeGroupChutex->orderBy('SUBDIVISI', 'ASC')->orderBy('NPK', 'ASC')->get();
        // $employeeGroup = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%")->orderBy('BIODATA.ID_DEPT', 'ASC')->orderBy('BIODATA.NPK', 'ASC')->get();

        $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('BIODATA.ID_DEPT', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'AUDIT.TANGGAL', 'AUDIT.KODE_BAGIAN', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('BIODATA', 'AUDIT.NPK', '=', 'BIODATA.NPK')->where('AUDIT.SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employeesOut = DB::connection('sqlsrv')->table('AUDIT')->select('BIODATA_KELUAR.ID_DEPT', 'BIODATA_KELUAR.NPK', 'BIODATA_KELUAR.NAMA_KARYAWAN', 'AUDIT.TANGGAL', 'AUDIT.KODE_BAGIAN', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('BIODATA_KELUAR', 'AUDIT.NPK', '=', 'BIODATA_KELUAR.NPK')->where('AUDIT.SUBDIVISI', 'NOT LIKE', "%LINE%");
        // $employeesJP = DB::connection('sqlsrv')->table('BIODATA_JP')->select('BIODATA_JP.ID_DEPT', 'BIODATA_JP.NPK', 'BIODATA_JP.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA_JP.NPK', '=', 'AUDIT.NPK')->leftJoin('DEPT', 'BIODATA_JP.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%");
        // $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'DEPT.DEPARTEMENT', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->leftJoin('DEPT', 'BIODATA.ID_DEPT', '=', 'DEPT.ID_DEPT')->where('DEPT.DEPARTEMENT', 'NOT LIKE', "%LINE%")->orderBy('BIODATA.ID_DEPT', 'ASC')->orderBy('BIODATA.NPK', 'ASC')->orderBy('AUDIT.TANGGAL', 'ASC')->get();
        $employees = $employeesChutex->union($employeesOut)->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        // dd($employeesChutex->get());
        return view('template.report2', compact('employees', 'employeeGroup'));
    }

