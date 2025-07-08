<?php

namespace App\Http\Controllers;

use App\Imports\AttendanceImport;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceController extends Controller
{
    public function index()
    {
        // Logic to display attendance records
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*', 'AUDIT.TANGGAL', 'AUDIT.SUBDIVISI', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK');
        $employeesLeaves = DB::connection('sqlsrv')->table('BIODATA_KELUAR')->select('BIODATA_KELUAR.*', 'AUDIT.TANGGAL', 'AUDIT.SUBDIVISI', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM',  'AUDIT.STATUS AS KETERANGAN')->leftJoin('AUDIT', 'BIODATA_KELUAR.NPK', '=', 'AUDIT.NPK');

        $unionEmployees = $employees->union($employeesLeaves)->get()->groupBy(['BAG', 'NPK', 'TANGGAL']);

        // $unionEmployees = [];
        // dd($unionEmployees['GA MANAGER']);

        return view('attendance.index', compact('unionEmployees'));
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');
        $nama_file = $file->hashName();
        $path = $file->storeAs('public/excel/', $nama_file);
        $import = Excel::import(new AttendanceImport(), storage_path('app/public/excel/' . $nama_file));
        Storage::delete($path);

        if ($import) {
            Alert::success('Import Successfully!', 'Attendance data successfully imported!');
            return redirect()->intended('attendance/index')->with(['success' => 'Data Berhasil Diimport!']);
        } else {
            return redirect()->intended('attendance/index')->with(['error' => 'Data Gagal Diimport!']);
        }
    }

    public function export(Request $request)
    {
        // Logic to display attendance records
        $employees = DB::connection('sqlsrv')->table('BIODATA')->select('BIODATA.*', 'AUDIT.TANGGAL', 'AUDIT.SUBDIVISI', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM', 'AUDIT.STATUS AS KETERANGAN', 'PKWT.TMK')->leftJoin('AUDIT', 'BIODATA.NPK', '=', 'AUDIT.NPK')->leftJoin('PKWT', 'BIODATA.NPK', '=', 'PKWT.NPK')->where('AUDIT.TANGGAL', '>=', $request->fromdate)->where('AUDIT.TANGGAL', '<=', $request->todate)->where('PKWT.TMK', '<=', $request->todate);
        $employeesLeaves = DB::connection('sqlsrv')->table('BIODATA_KELUAR')->select('BIODATA_KELUAR.*', 'AUDIT.TANGGAL', 'AUDIT.SUBDIVISI', 'AUDIT.JAM_PAGI', 'AUDIT.JAM_SIANG', 'AUDIT.JAM_MALAM',  'AUDIT.STATUS AS KETERANGAN', 'PKWT.TMK')->leftJoin('AUDIT', 'BIODATA_KELUAR.NPK', '=', 'AUDIT.NPK')->leftJoin('PKWT', 'BIODATA_KELUAR.NPK', '=', 'PKWT.NPK')->where('AUDIT.TANGGAL', '>=', $request->fromdate)->where('AUDIT.TANGGAL', '<=', $request->todate)->where('PKWT.TMK', '<=', $request->todate);

        $unionEmployees = $employees->union($employeesLeaves)->get();

        $groupedByBag = $unionEmployees->groupBy('BAG');
        $groupedByNPK = $unionEmployees->groupBy('NPK');
        $groupedByTanggal = $unionEmployees->groupBy('TANGGAL');

        // dd($groupedByTanggal);

        $pdf = Pdf::loadView('template.report', compact('unionEmployees'));

        return $pdf;
    }
}
