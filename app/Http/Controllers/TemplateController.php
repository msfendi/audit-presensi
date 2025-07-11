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
        $employeeGroupChutex = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'LIKE', "%LINE%");
        $employeeGroupOut = DB::connection('sqlsrv')->table('BIODATA_KELUAR')->select('BIODATA_KELUAR.NPK', 'BIODATA_KELUAR.NAMA_KARYAWAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA_KELUAR.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'LIKE', "%LINE%");
        $employeeGroupJp = DB::connection('sqlsrv')->table('BIODATA_JP')->select('BIODATA_JP.NPK', 'BIODATA_JP.NAMA_KARYAWAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA_JP.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'LIKE', "%LINE%");
        $employeeGroup = $employeeGroupChutex->union($employeeGroupOut)->union($employeeGroupJp)->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->get();
        // $employeeGroup = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'SUBDIVISI')->where('SUBDIVISI', 'LIKE', "%LINE%")->orderBy('ASC')->orderBy('BIODATA.NPK', 'ASC')->get();

        $employeesChutex = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'LIKE', "%LINE%");
        $employeesOut = DB::connection('sqlsrv')->table('BIODATA_KELUAR')->select('BIODATA_KELUAR.NPK', 'BIODATA_KELUAR.NAMA_KARYAWAN', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA_KELUAR.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'LIKE', "%LINE%");
        $employeesJP = DB::connection('sqlsrv')->table('BIODATA_JP')->select('BIODATA_JP.NPK', 'BIODATA_JP.NAMA_KARYAWAN', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA_JP.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'LIKE', "%LINE%");
        // $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'SUBDIVISI', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'LIKE', "%LINE%")->orderBy('ASC')->orderBy('BIODATA.NPK', 'ASC')->orderBy('AUDIT.TANGGAL', 'ASC')->get();
        $employees = $employeesChutex->union($employeesOut)->union($employeesJP)->orderBy('KODE_BAGIAN', 'ASC')->orderBy('SUBDIVISI', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        return view('template.report2', compact('employees', 'employeeGroup'));
    }

    public function auditnonsewing()
    {
        $employeeGroupChutex = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->distinct()->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employeeGroupOut = DB::connection('sqlsrv')->table('BIODATA_KELUAR')->select('BIODATA_KELUAR.NPK', 'BIODATA_KELUAR.NAMA_KARYAWAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->distinct()->leftJoin('AUDIT', 'BIODATA_KELUAR.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employeeGroupJp = DB::connection('sqlsrv')->table('BIODATA_JP')->select('BIODATA_JP.NPK', 'BIODATA_JP.NAMA_KARYAWAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->distinct()->leftJoin('AUDIT', 'BIODATA_JP.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employeeGroup = $employeeGroupChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('SUBDIVISI', 'ASC')->get();
        // $employeeGroup = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'SUBDIVISI')->where('SUBDIVISI', 'NOT LIKE', "%LINE%")->orderBy('ASC')->orderBy('BIODATA.NPK', 'ASC')->get();

        $employeesChutex = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employeesOut = DB::connection('sqlsrv')->table('BIODATA_KELUAR')->select('BIODATA_KELUAR.NPK', 'BIODATA_KELUAR.NAMA_KARYAWAN', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA_KELUAR.NPK', '=', 'AUDIT.NPK')->where('AUDIT.SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employeesJP = DB::connection('sqlsrv')->table('BIODATA_JP')->select('BIODATA_JP.NPK', 'BIODATA_JP.NAMA_KARYAWAN', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN', 'AUDIT.KODE_BAGIAN', 'AUDIT.SUBDIVISI')->leftJoin('AUDIT', 'BIODATA_JP.NPK', '=', 'AUDIT.NPK')->where('AUDIT.SUBDIVISI', 'NOT LIKE', "%LINE%");
        // $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.BAG', 'BIODATA.NPK', 'BIODATA.NAMA_KARYAWAN', 'SUBDIVISI', 'AUDIT.TANGGAL', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->where('SUBDIVISI', 'NOT LIKE', "%LINE%")->orderBy('ASC')->orderBy('BIODATA.NPK', 'ASC')->orderBy('AUDIT.TANGGAL', 'ASC')->get();
        $employees = $employeesChutex->union($employeesOut)->union($employeesJP)->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('SUBDIVISI', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        // dd($employeesChutex->get());

        // $pdf = Pdf::loadView('/template/report2', compact('employees', 'employeeGroup'));
        // return $pdf->download('Data Absen.pdf');
        return view('template.report2', compact('employees', 'employeeGroup'));
    }
}
