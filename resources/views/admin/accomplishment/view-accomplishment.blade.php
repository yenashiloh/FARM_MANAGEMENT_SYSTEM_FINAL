{{-- <!DOCTYPE html>
<html lang="en">
<title>Accomplishments</title>
@include('partials.admin-header')

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 col-lg-10 mx-auto">
            <div class="header-container">
                <h5 class="academic">
                    {{ $folderName }} (an academic document that communicates information about a specific course and
                    explains the rules, responsibilities, and expectations associated with it.)
                </h5>
                <a href="javascript:history.back()" class="btn btn-danger">
                    <i class="fas fa-arrow-left"></i> Back to previous page
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-10 mx-auto">
            <div class="card mt-3">
                <div class="card-body">
                    <table id="dataTable" class="table table-striped">
                        <div id="alertContainer">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show text-center" role="alert"
                                    id="successAlert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show text-center" role="alert"
                                    id="errorAlert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Date & Time</th>
                                <th>Employee Name</th>
                                <th>Code & Subject</th>
                                <th>Year & Program</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedFiles as $semester => $files)
                                @foreach ($files as $index => $file)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $file->created_at->timezone('Asia/Manila')->format('F j, Y, g:i A') }}
                                        </td>
                                        <td>{{ $file->user_name }}</td>
                                        <td>
                                            <div style="display: flex; flex-direction: column; margin-bottom: 20px;">
                                                {{ $file->code }}
                                                <span style="margin-top: 5px;">{{ $file->subject_name ?? 'N/A' }}</span>
                                            </div>
                                        </td>

                                        <td>{{ $file->year ?? 'N/A' }} -
                                            <span>{{ $file->program ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ Storage::url('/' . $file->files) }}"
                                                target="_blank">{{ $file->original_file_name }}</a>
                                        </td>
                                        <td>
                                            @if ($file->status === 'To Review')
                                                <span class="badge badge-primary">{{ $file->status }}</span>
                                            @elseif ($file->status === 'Declined')
                                                <span class="badge badge-danger">{{ $file->status }}</span>
                                                <br>
                                                <span>Declined Reason: {{ $file->declined_reason }}</span>
                                            @elseif ($file->status === 'Approved')
                                                <span class="badge badge-success">{{ $file->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                @if ($file->status === 'To Review')
                                                    <a href="{{ route('approveFile', ['courses_files_id' => $file->courses_files_id]) }}"
                                                        class="btn btn-success btn-sm mb-2">
                                                        Approve
                                                    </a>

                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        data-toggle="modal" data-target="#declineModal"
                                                        data-id="{{ $file->courses_files_id }}">
                                                        Decline
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Decline Modal -->
    <div class="modal fade" id="declineModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="declineModalLabel">Decline File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('declineFile', ['courses_files_id' => $file->courses_files_id]) }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="courses_files_id" id="declineModalId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="declineReason">Decline Reason</label>
                            <textarea class="form-control" id="declineReason" name="declineReason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Decline</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // When the Decline button is clicked
            $('button[data-target="#declineModal"]').on('click', function() {
                var fileId = $(this).data('id'); // Get the file ID from the button's data-id attribute
                var actionUrl = "{{ route('declineFile', ['courses_files_id' => ':courses_files_id']) }}"
                    .replace(':courses_files_id', fileId); // Set the form action URL

                $('#declineModalId').val(fileId); // Set the file ID in the hidden input field of the modal
                $('#declineModal form').attr('action', actionUrl); // Set the form action URL
            });

            // Handle the form submission
            $('#declineModal form').on('submit', function() {
                // Close the modal
                $('#declineModal').modal('hide');
            });
        });

        $(document).ready(function() {
            // Automatically hide success alert after 3 seconds
            setTimeout(function() {
                $('#successAlert').fadeOut('slow');
            }, 3000); // 3000 milliseconds = 3 seconds

            // Automatically hide error alert after 3 seconds
            setTimeout(function() {
                $('#errorAlert').fadeOut('slow');
            }, 3000); // 3000 milliseconds = 3 seconds
        });

       
    </script>


    @include('partials.admin-footer') --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    @include('partials.admin-header')
    <title>Dashboard</title>
</head>

<body>
    @include('partials.admin-sidebar')

    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Accomplishment</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#"
                                                class="breadcrumb-link">{{ $folderName }}</a></li>
                                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">View
                                                Accomplishment</a></li>

                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                @include('partials.admin-header')
                <div class="row">
                    <!-- ============================================================== -->
                    <!-- data table  -->
                    <!-- ============================================================== -->
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"> {{ $folderName }} (an academic document that communicates
                                    information about a specific course and
                                    explains the rules, responsibilities, and expectations associated with it.)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered second"
                                        style="width:100%">
                                        <thead>
                                            <div id="alertContainer">
                                                @if (session('success'))
                                                    <div class="alert alert-success alert-dismissible fade show text-center"
                                                        role="alert" id="successAlert">
                                                        {{ session('success') }}
                                                    </div>
                                                @endif

                                                @if (session('error'))
                                                    <div class="alert alert-danger alert-dismissible fade show text-center"
                                                        role="alert" id="errorAlert">
                                                        {{ session('error') }}
                                                        <button type="button" class="close" data-dismiss="alert"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>

                                            <tr>
                                                <th>No.</th>
                                                <th>Date & Time</th>
                                                <th>Employee Name</th>
                                                <th>Code & Subject</th>
                                                <th>Year & Program</th>
                                                <th>File</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($groupedFiles as $semester => $files)
                                                @foreach ($files as $index => $file)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $file->created_at->timezone('Asia/Manila')->format('F j, Y, g:i A') }}
                                                        </td>
                                                        <td>{{ $file->user_name }}</td>
                                                        <td>
                                                            <div
                                                                style="display: flex; flex-direction: column; margin-bottom: 20px;">
                                                                {{ $file->code }}
                                                                <span
                                                                    style="margin-top: 5px;">{{ $file->subject_name ?? 'N/A' }}</span>
                                                            </div>
                                                        </td>

                                                        <td>{{ $file->year ?? 'N/A' }} -
                                                            <span>{{ $file->program ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ Storage::url('/' . $file->files) }}"
                                                                target="_blank">{{ $file->original_file_name }}</a>
                                                        </td>
                                                        <td>
                                                            @if ($file->status === 'To Review')
                                                                <span
                                                                    class="badge badge-primary">{{ $file->status }}</span>
                                                            @elseif ($file->status === 'Declined')
                                                                <span
                                                                    class="badge badge-danger">{{ $file->status }}</span>
                                                                <br>
                                                                <span>Declined Reason:
                                                                    {{ $file->declined_reason }}</span>
                                                            @elseif ($file->status === 'Approved')
                                                                <span
                                                                    class="badge badge-success">{{ $file->status }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                @if ($file->status === 'To Review')
                                                                    <a href="{{ route('approveFile', ['courses_files_id' => $file->courses_files_id]) }}"
                                                                        class="btn btn-success btn-sm mb-2">
                                                                        Approve
                                                                    </a>

                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                        data-toggle="modal" data-target="#declineModal"
                                                                        data-id="{{ $file->courses_files_id }}">
                                                                        Decline
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Decline Modal -->
                    <div class="modal fade" id="declineModal" tabindex="-1" role="dialog"
                        aria-labelledby="declineModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="declineModalLabel">Decline File</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form
                                    action="{{ route('declineFile', ['courses_files_id' => $file->courses_files_id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="courses_files_id" id="declineModalId">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="declineReason">Decline Reason</label>
                                            <textarea class="form-control" id="declineReason" name="declineReason" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-danger">Decline</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- end data table  -->
                    <!-- ============================================================== -->
                </div>
                <script>
                    $(document).ready(function() {
                        // When the Decline button is clicked
                        $('button[data-target="#declineModal"]').on('click', function() {
                            var fileId = $(this).data('id'); // Get the file ID from the button's data-id attribute
                            var actionUrl = "{{ route('declineFile', ['courses_files_id' => ':courses_files_id']) }}"
                                .replace(':courses_files_id', fileId); // Set the form action URL

                            $('#declineModalId').val(fileId); // Set the file ID in the hidden input field of the modal
                            $('#declineModal form').attr('action', actionUrl); // Set the form action URL
                        });

                        // Handle the form submission
                        $('#declineModal form').on('submit', function() {
                            // Close the modal
                            $('#declineModal').modal('hide');
                        });
                    });

                    $(document).ready(function() {
                        // Automatically hide success alert after 3 seconds
                        setTimeout(function() {
                            $('#successAlert').fadeOut('slow');
                        }, 3000); // 3000 milliseconds = 3 seconds

                        // Automatically hide error alert after 3 seconds
                        setTimeout(function() {
                            $('#errorAlert').fadeOut('slow');
                        }, 3000); // 3000 milliseconds = 3 seconds
                    });
                </script>

                <script src="../../../../asset/vendor/jquery/jquery-3.3.1.min.js"></script>
                <script src="../../../../asset/vendor/bootstrap/js/bootstrap.bundle.js"></script>
                <script src="../../../../asset/vendor/slimscroll/jquery.slimscroll.js"></script>
                <script src="../../../../asset/vendor/multi-select/js/jquery.multi-select.js"></script>
                <script src="../../../../asset/libs/js/main-js.js"></script>
                <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
                <script src="../../../../asset/vendor/datatables/js/dataTables.bootstrap4.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
                <script src="../../../../asset/vendor/datatables/js/buttons.bootstrap4.min.js"></script>
                <script src="../../../../asset/vendor/datatables/js/data-table.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
                <script src="https://cdn.datatables.net/rowgroup/1.0.4/js/dataTables.rowGroup.min.js"></script>
                <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
                <script src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
