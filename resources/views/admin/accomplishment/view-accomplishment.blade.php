<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>View Accomplishment</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../../../asset/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon">
    <link href="../../../../../asset/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../../../asset/libs/css/style.css">
    <link rel="stylesheet" href="../../../../../asset/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" type="text/css" href="../../../../../asset/vendor/datatables/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../../asset/vendor/datatables/css/buttons.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../../asset/vendor/datatables/css/select.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../../asset/vendor/datatables/css/fixedHeader.bootstrap4.css">
</head>

<body>
    @include('partials.admin-sidebar')
    <div id="loading-spinner" class="loading-spinner">
        <div class="spinner"></div>
    </div>

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
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Accomplishment</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.accomplishment.admin-uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}"
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
                                <div class="form-group row">
                                    <label for="semesterSelect" class="col-form-label ml-3">Semester:</label>
                                    <div class="col-md-10">
                                        <select id="semesterSelect" class="form-control" onchange="this.form.submit()" disabled style="width: 250px;">
                                            @foreach($allSemesters as $sem)
                                                <option value="{{ $sem }}" {{ $sem == $currentSemester ? 'selected' : '' }}>
                                                    {{ $sem }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered second" style="width:100%">
                                        <thead>
                                            <div id="alertContainer">
                                                @if (session('success'))
                                                    <div class="alert alert-success alert-dismissible fade show text-center" role="alert" id="successAlert">
                                                        {{ session('success') }}
                                                    </div>
                                                @endif
                            
                                                @if (session('error'))
                                                    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert" id="errorAlert">
                                                        {{ session('error') }}
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                            
                                            <tr>
                                                <th>No.</th>
                                                <th>Date & Time</th>
                                                <th>Employee Name</th>
                                                <th>Course Code & Course</th>
                                                <th>Year & Program</th>
                                                <th>File</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $index = 1; @endphp
                                            @foreach ($groupedFiles[$currentSemester] ?? [] as $file)
                                                <tr>
                                                    <td>{{ $index++ }}</td>
                                                    <td>{{ $file->created_at->timezone('Asia/Manila')->format('F j, Y, g:i A') }}</td>
                                                    <td>{{ $file->userLogin->first_name }} {{ $file->userLogin->surname }}</td>
                                                    <td>
                                                        <div style="display: flex; flex-direction: column; margin-bottom: 20px;">
                                                            {{ $file->courseSchedule->course_code ?? 'N/A' }}
                                                            <span style="margin-top: 5px;">{{ $file->subject ?? 'N/A' }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ $file->courseSchedule->year_section ?? 'N/A' }} -
                                                        <span>{{ $file->courseSchedule->program ?? 'N/A' }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ Storage::url($file->files) }}" target="_blank"
                                                           style="color: rgb(65, 65, 231); text-decoration: underline;">
                                                            {{ $file->original_file_name }}
                                                        </a>
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
                                                                <button type="button" class="btn btn-warning btn-sm mb-2"
                                                                        data-toggle="modal" data-target="#declineModal"
                                                                        data-id="{{ $file->courses_files_id }}">
                                                                    Decline
                                                                </button>
                                                            @endif
                                                            <button type="button" class="btn btn-primary btn-sm delete-button"
                                                                    data-id="{{ $file->courses_files_id }}">
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
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
                        $('button[data-target="#declineModal"]').on('click', function() {
                            var fileId = $(this).data('id');
                            var actionUrl = "{{ route('declineFile', ['courses_files_id' => ':courses_files_id']) }}"
                                .replace(':courses_files_id', fileId);

                            $('#declineModalId').val(fileId);
                            $('#declineModal form').attr('action', actionUrl);
                        });

                        $('#declineModal form').on('submit', function() {
                            $('#declineModal').modal('hide');
                        });
                    });

                    $(document).ready(function() {
                        setTimeout(function() {
                            $('#successAlert').fadeOut('slow');
                        }, 3000);

                        setTimeout(function() {
                            $('#errorAlert').fadeOut('slow');
                        }, 3000);
                    });

                    $(document).ready(function() {
                        $('.delete-button').on('click', function() {
                            var fileId = $(this).data('id');
                            var actionUrl = "{{ route('deleteFile', ['courses_files_id' => ':courses_files_id']) }}"
                                .replace(':courses_files_id', fileId);

                            Swal.fire({
                                title: 'Are you sure?',
                                text: "You won't be able to recover this file!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, delete it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: actionUrl,
                                        type: 'DELETE',
                                        data: {
                                            _token: '{{ csrf_token() }}'
                                        },
                                        success: function(response) {
                                            Swal.fire(
                                                'Deleted!',
                                                'Your file has been deleted.',
                                                'success'
                                            ).then(() => {
                                                location
                                                    .reload();
                                            });
                                        },
                                        error: function() {
                                            Swal.fire(
                                                'Error!',
                                                'There was an error deleting the file.',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            });
                        });
                    });
                </script>

                @include('partials.tables-footer')
