<!DOCTYPE html>
<html lang="en">
@include('layout.header')
<body id="page-top">
<!-- Page Wrapper -->
@include('sweetalert::alert')
<div id="wrapper">
@include('layout.sidebar')
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">
            @include('layout.navbar')
            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Attendance List</h1>
                    <div>
                        <form method="GET" action="" >
                            <button id="submit" type="submit" class="btn btn-sm btn-primary shadow-s"><i
                            class="fas fa-qrcode fa-sm text-white-50"></i> Generate QR</a></button>
                        <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#importModal"><i
                            class="fas fa-upload fa-sm text-white-50"></i> Upload Data</a>
                        {{-- <a href="{{ route('pdf.generatePDF') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm"><i
                            class="fas fa-download fa-sm text-white-50"></i> Download Sticker A4</a> --}}
                        </form>
                    </div>
                </div>
                
                <!-- DataTales Example -->
                <div class="card shadow mb-2">
                    <div class="card-header py-3 d-sm-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Attendance Data</h6>
                        <form method="GET" id="form-void">
                                <select name="void" id="void" class="form-control" onchange="document.getElementById('form-void').submit()" style="width: 300px;">
                                    <option disabled selected hidden>Select Status</option>
                                    <option value="false" {{ app('request')->input('void') == 'false'  ? 'selected' : ''}}>Active</option>
                                    <option value="true" {{ app('request')->input('void') == 'true'  ? 'selected' : ''}}>Void</option>
                                </select>
                        </form>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('attendance.export') }}" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-xl-3 col-md-6">
                                    <div>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <label>From Date :</label>
                                        <input class="date form-control" type="date" id="fromdate" name="fromdate" value="">
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div>
                                        <label>To Date :</label>
                                        <input class="date form-control" type="date" id="todate" name="todate" value="">
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div>
                                        <label>Department</label>
                                        <select name="department" id="department" class="form-control">
                                            <option disabled selected>Select Department</option>
                                            <option value="sewing">Sewing</option>
                                            <option value="nonsewing">Non Sewing</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6 mt-2">
                                    <br>
                                    <div>
                                        <button id='filter-data' type="button" class="btn btn-primary">Filter</button>
                                        <button id='sewing' type="button" class="btn btn-info">Sewing</button>
                                        <button id='nonsewing' type="button" class="btn btn-warning">Non Sewing</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>NPK</th>
                                        <th>Nama</th>
                                        <th>Tanggal</th>
                                        <th>Subdivisi</th>
                                        <th>Jam Pagi</th>
                                        <th>Jam Siang</th>
                                        <th>Jam Malam</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $employee->NPK }}</td>
                                        <td>{{ $employee->NAMA_KARYAWAN }}</td>
                                        <td>{{ $employee->TANGGAL }}</td>
                                        <td>{{ $employee->SUBDIVISI }}</td>
                                        <td>{{ $employee->JAM_PAGI }}</td>
                                        <td>{{ $employee->JAM_SIANG }}</td>
                                        <td>{{ $employee->JAM_MALAM }}</td>
                                        <td>{{ $employee->KETERANGAN }}</td>
                                        <td class="text-center">
                                            @if (request()->get('void') == 'false' || request()->get('void') == '')
                                            {{-- <a class="btn btn-danger btn-circle btn-sm btn-void-record" data-void-link="{{ route('attendance.void', ['id' => $attendance->id]) }}" data-void-name="{{ $attendance->item_name }}" data-toggle="modal" data-target="#voidModal">
                                                <i class="fas fa-ban"></i>
                                            </a> --}}
                                            @elseif (request()->get('void') == 'true')
                                            {{-- <a class="btn btn-success btn-circle btn-sm btn-restore-record" data-restore-link="{{ route('attendance.restore', ['id' => $attendance->id]) }}" data-restore-name="{{ $attendance->item_name }}" data-toggle="modal" data-target="#restoreModal">
                                                <i class="fas fa-history"></i>
                                            </a> --}}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Content Row -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Modal -->
        <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="attendance" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="pdf-title" class="modal-title" id="exampleModalLabel">Inventory QR Name</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body"><iframe id="pdf-src" src ="" width="100%" height="480px"></iframe></div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="attendance" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="delete-title" class="modal-title" id="exampleModalLabel">Delete Record</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body"><p id="modal-text-record"></p></div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                        <a id="btn-confirm" href=""><button class="btn btn-primary" type="button">Confirm</button></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="voidModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="attendance" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="void-title" class="modal-title" id="exampleModalLabel">Void Record</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body"><p id="modal-text-record-void"></p></div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                        <a id="btn-confirm-void" href=""><button class="btn btn-danger" type="button">Confirm</button></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="attendance" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="restore-title" class="modal-title" id="exampleModalLabel">Restore Record</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <div class="modal-body"><p id="modal-text-record-restore"></p></div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                        <a id="btn-confirm-restore" href=""><button class="btn btn-success" type="button">Confirm</button></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="modal-title" class="modal-title" id="exampleModalLabel">Import Attendance Data</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                    </div>
                    <form action="{{ route('attendance.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>PILIH FILE</label>
                                    <input type="file" name="file" accept=".xls,.xlsx">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success">Import</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>


@include('layout.footer')
</body>
<!-- Page level plugins -->
<script src="{{asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('js/demo/datatables-demo.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $('.btn-show-pdf').on('click', function () {
        $('#pdf-src').attr('src', '../../storage/attendance/' + $(this).data('show-link'));
        $("#pdf-title").text($(this).data('show-title'));
    });
    $('.btn-delete-record').on('click', function () {
            $('#btn-confirm').attr('href', $(this).data('delete-link'));
            $("#modal-text-record").text('Apakah anda yakin ingin menghapus Inventory QR ' + $(this).data('delete-name') + '?');
    });
    $('.btn-void-record').on('click', function () {
            $('#btn-confirm-void').attr('href', $(this).data('void-link'));
            $("#modal-text-record-void").text('Apakah anda yakin ingin menghapus Inventory QR ' + $(this).data('void-name') + '?');
    });
    $('.btn-restore-record').on('click', function () {
            $('#btn-confirm-restore').attr('href', $(this).data('restore-link'));
            $("#modal-text-record-restore").text('Apakah anda yakin ingin mengembalikan Inventory QR ' + $(this).data('restore-name') + '?');
    });
    // $("#submit").click(function() {
    //     $(this).hide();
    //     Swal.fire({
    //         title: "Process",
    //         html: "Generating All QR Code.. Please Wait!!",
    //         timerProgressBar: true,
    //         didOpen: () => {
    //             Swal.showLoading();
    //         },
    //     })
    // });

    $('#sewing').on('click', function () {
        const url = "{{ route('attendance.auditsewing') }}";
        window.open(url, '_blank');
    });

    $('#nonsewing').on('click', function () {
        // const url = "{{ route('attendance.auditnonsewing') }}";
        // window.open(url, '_blank');
        $.ajax({
            url: "{{ route('attendance.auditnonsewing') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                fromdate: $('#fromdate').val(),
                todate: $('#todate').val(),
            },
            success: function(response) {
                console.log("Success:", response);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });
</script>
</html>