<?php

namespace App\Http\Controllers;

use App\Imports\AttendanceImport;
use App\Models\Attendance;
use Dompdf\Dompdf;
use Barryvdh\Snappy\Facades\SnappyPdf;
// use Dompdf\Options;
// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
// use Knp\Snappy\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceController extends Controller
{
    public function index()
    {
        $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employees = $employeesChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        return view('attendance.index', compact('employees'));
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

    // public function export(Request $request)
    // {
    //     // dd($request->all());
    //     if ($request->department === 'sewing') {
    //         $employeeGroupChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->distinct('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->where('SUBDIVISI', 'LIKE', "%LINE%");
    //         $employeeGroup = $employeeGroupChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->get();

    //         $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')->where('SUBDIVISI', 'LIKE', "%LINE%")->where('TANGGAL', '>=', $request->fromdate)->where('TANGGAL', '<=', $request->todate);
    //         $employees = $employeesChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();
    //     } else {
    //         $employeeGroupChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->distinct('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
    //         $employeeGroup = $employeeGroupChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->get();

    //         $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')->where('SUBDIVISI', 'NOT LIKE', "%LINE%")->where('TANGGAL', '>=', $request->fromdate)->where('TANGGAL', '<=', $request->todate);
    //         $employees = $employeesChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();
    //     }

    //     // dd($employees);
    //     // $pdf = dompdf::loadView('template.report2', compact(['employees', 'employeeGroup']));
    //     $pdf = PDF::loadView('template.report2', compact(['employees', 'employeeGroup']));
    //     $pdf->setPaper('A4', 'landscape');
    //     $pdf->render();

    //     return $pdf->download('Data Absen.pdf');
    // }

    public function export(Request $request)
    {
        // Increase memory and time limits for large datasets
        ini_set('memory_limit', '5120M');
        ini_set('max_execution_time', 30000);

        // Optimize query by reducing data and using pagination/chunking approach
        $baseQuery = DB::connection('sqlsrv')->table('AUDIT')
            ->whereBetween('TANGGAL', [$request->fromdate, $request->todate]);

        if ($request->department === 'sewing') {
            $baseQuery->where('SUBDIVISI', 'LIKE', "%LINE%");
        } else {
            $baseQuery->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        }

        // Get employee groups more efficiently
        $employeeGroup = $baseQuery->clone()
            ->select('NPK', 'KODE_BAGIAN', 'SUBDIVISI')
            ->distinct()
            ->orderBy('KODE_BAGIAN', 'ASC')
            ->orderBy('NPK', 'ASC')
            ->get();

        // Get employees data with optimized query
        $employees = $baseQuery->clone()
            ->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')
            ->orderBy('KODE_BAGIAN', 'ASC')
            ->orderBy('NPK', 'ASC')
            ->orderBy('TANGGAL', 'ASC')
            ->get();

        // Check if dataset is too large
        if ($employees->count() > 50000) {
            return response()->json(['error' => 'Dataset too large. Please reduce date range.'], 422);
        }

        // Configure DomPDF for better performance
        $html = view('template.report', compact(['employees', 'employeeGroup']))->render();

        $pdf = SnappyPdf::loadHTML($html)
            ->setOption('page-width', '330mm')
            ->setOption('page-height', '210mm');

        $pdf->setTimeout(3600);
        // $pdf->setPaper('f4', 'landscape');

        return $pdf->download('Data_Absen_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    public function auditsewing()
    {
        $employeeGroupChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->distinct('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->where('SUBDIVISI', 'LIKE', "%LINE%");
        $employeeGroup = $employeeGroupChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->get();

        $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')->where('SUBDIVISI', 'LIKE', "%LINE%");
        $employees = $employeesChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        // dd($employees);  

        // $pdf = Pdf::loadView('/template/report2', compact('employees', 'employeeGroup'));
        // return $pdf->download('Data Absen.pdf');
        return view('template.report2', compact('employees', 'employeeGroup'));
    }

    public function auditnonsewing(Request $request)
    {
        dd($request->all());
        $employeeGroupChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->distinct('NPK', 'KODE_BAGIAN', 'SUBDIVISI')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employeeGroup = $employeeGroupChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->get();

        $employeesChutex = DB::connection('sqlsrv')->table('AUDIT')->select('NPK', 'NAMA_KARYAWAN', 'KODE_BAGIAN', 'SUBDIVISI', 'TANGGAL', 'JAM_PAGI', 'JAM_SIANG', 'JAM_MALAM', 'STATUS AS KETERANGAN')->where('SUBDIVISI', 'NOT LIKE', "%LINE%");
        $employees = $employeesChutex->orderBy('KODE_BAGIAN', 'ASC')->orderBy('NPK', 'ASC')->orderBy('TANGGAL', 'ASC')->get();

        // dd($employeesChutex->get());
        return view('template.report2', compact('employees', 'employeeGroup'));
    }
}
