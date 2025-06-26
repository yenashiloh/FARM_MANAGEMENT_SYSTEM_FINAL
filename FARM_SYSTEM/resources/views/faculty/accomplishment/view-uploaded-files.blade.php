<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ $folderName }}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../../asset/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon">
    <link href="../../../../asset/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../../asset/libs/css/style.css">
    <link rel="stylesheet" href="../../../../asset/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/buttons.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/select.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/fixedHeader.bootstrap4.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .body-modal {
            max-height: 480px;
            overflow-y: auto;
        }

        .view-modal {
            max-height: 400px;
            overflow-y: auto;
        }

        .bordered-file-input {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            background-color: #fff;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        }

        .modal-dialog {
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        p {
            color: #3d405c;
        }

        strong {
            color: rgb(27, 27, 27);
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>

<body>
    @include('partials.faculty-sidebar')
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
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('faculty.accomplishment.uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}"
                                                class="breadcrumb-link">
                                                {{ $folder->folder_name }}
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item"><a href="" class="breadcrumb-link">
                                                View Uploaded Files </a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                <div class="ecommerce-widget">
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
                                        <input type="text" name="semester" value="{{ $selectedSemester }}"
                                            class="form-control" disabled style="width: 250px;">
                                    </div>
                                </div>
                                @if ($uploadedFiles->contains('status', 'Approved'))
                                    <form id="archive-all-form" action="{{ route('files.archiveAll') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm mb-3 fs-6">Archive</button>
                                    </form>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success text-center">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered first">
                                        <thead>
                                            <tr>
                                                <th>
                                                    @if ($uploadedFiles->contains('status', 'Approved'))
                                                        <input type="checkbox" id="select-all">
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </th>
                                                <th>No.</th>
                                                <th>Date & Time</th>
                                                <th>Program</th>
                                                <th>Course & Course Code</th>
                                                <th>Year & Section</th>
                                                <th>File Name</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($uploadedFiles as $file)
                                                <tr>
                                                    <td>
                                                        @if ($file->status === 'Approved')
                                                            <input type="checkbox" class="file-checkbox"
                                                                value="{{ $file->courses_files_id }}">
                                                        @else
                                                            &nbsp;
                                                        @endif
                                                    </td>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($file->created_at)->locale('en_PH')->format('F j, Y, g:i A') }}
                                                    </td>
                                                    <td>{{ $file->courseSchedule->program }}</td>
                                                    <td>{{ $file->subject }} -
                                                        {{ $file->courseSchedule->course_code }}</td>
                                                    <td>{{ $file->courseSchedule->year_section }}</td>
                                                    <td>
                                                        <a href="{{ Storage::url('/' . $file->files) }}"
                                                            target="_blank"
                                                            style="color: rgb(65, 65, 231); text-decoration: underline;">
                                                            {{ $file->original_file_name }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($file->status === 'To Review')
                                                            <span
                                                                class="badge badge-primary">{{ $file->status }}</span>
                                                        @elseif($file->status === 'Approved')
                                                            <span
                                                                class="badge badge-success">{{ $file->status }}</span>
                                                        @elseif($file->status === 'Declined')
                                                            <span class="badge badge-danger">{{ $file->status }}</span>
                                                            @if ($file->declined_reason)
                                                                <div class="mt-2">Declined Reason:
                                                                    {{ $file->declined_reason }}</div>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">{{ $file->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($file->status === 'Declined' || $file->status === 'To Review')
                                                            <button class="btn btn-warning btn-sm edit-file-btn"
                                                                data-file-id="{{ $file->courses_files_id }}"
                                                                data-semester="{{ $file->courseSchedule->sem_academic_year }}"
                                                                data-program="{{ $file->courseSchedule->program }}"
                                                                data-course-subject-code="{{ $file->subject }} - {{ $file->courseSchedule->course_code }}"
                                                                data-year-section="{{ $file->courseSchedule->year_section }}"
                                                                data-original-file-name="{{ $file->original_file_name }}">
                                                                Edit
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Edit File Modal -->
                    <div class="modal fade" id="editFileModal" tabindex="-1" role="dialog"
                        aria-labelledby="editFileModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title" id="editFileModalLabel">Edit File</h3>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Reminder:</strong> Files with an <strong>Approved</strong> status
                                        cannot be edited. You can only make changes to files with a status of
                                        <strong>Declined</strong> or <strong>To Review</strong>.
                                    </p>
                                    <form id="editFileForm" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" id="editFileId" name="id">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="semester">Semester:</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="semester"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="program">Program:</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="program"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="courseSubjectCode">Course & Course
                                                            Code:</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control"
                                                            id="courseSubjectCode" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="yearSection">Year & Section:</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" id="yearSection"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="files">Upload New File:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="file" class="form-control-file" id="files"
                                                        name="files" accept=".pdf">
                                                    <div class="mt-2 mt-0">
                                                        <span style="font-size: 12px;">Current File: <span
                                                                id="currentFileName"></span></span>
                                                        <br>
                                                        <small class="text-danger" id="fileError"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="saveChanges">Save
                                        changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end wrapper  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- end main wrapper  -->
        <!-- ============================================================== -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
        <script src="../../../../asset/vendor/datatables/js/loading.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.edit-file-btn').on('click', function() {
                    var fileId = $(this).data('file-id');
                    var semester = $(this).data('semester');
                    var program = $(this).data('program');
                    var courseSubjectCode = $(this).data('course-subject-code');
                    var yearSection = $(this).data('year-section');
                    var originalFileName = $(this).data('original-file-name');

                    $('#editFileId').val(fileId);
                    $('#semester').val(semester);
                    $('#program').val(program);
                    $('#courseSubjectCode').val(courseSubjectCode);
                    $('#yearSection').val(yearSection);
                    $('#currentFileName').text(originalFileName);

                    $('#editFileModal').modal('show');
                });

                $('#saveChanges').on('click', function() {
                    var formData = new FormData($('#editFileForm')[0]);
                    var fileId = $('#editFileId').val();

                    formData.append('_method', 'PUT');

                    $.ajax({
                        url: '/update-file/' + fileId,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                window.location
                                    .reload();
                            } else {
                                alert('Error updating file');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            alert('' + (xhr.responseJSON ? xhr.responseJSON
                                .message : error));
                        }
                    });
                });
            });

            $(document).ready(function() {
                $('.archive-file-btn').on('click', function() {
                    var fileId = $(this).data('file-id');

                    $.ajax({
                        url: '/files/archive/' + fileId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert(response.message);
                            location.reload();
                        },
                        error: function(xhr) {
                            alert('An error occurred.');
                        }
                    });
                });
            });
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('select-all');
                const fileCheckboxes = document.querySelectorAll('.file-checkbox');

                selectAllCheckbox.addEventListener('change', function() {
                    fileCheckboxes.forEach(checkbox => {
                        if (checkbox.offsetParent !== null) {
                            checkbox.checked = this.checked;
                        }
                    });
                });

                document.getElementById('archive-all-form').addEventListener('submit', function(e) {
                    e.preventDefault();

                    let selectedIds = [];
                    const fileCheckboxes = document.querySelectorAll('.file-checkbox');
                    fileCheckboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            selectedIds.push(checkbox.value);
                        }
                    });

                    if (selectedIds.length > 0) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You are about to archive the selected files.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, archive them!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'file_ids';
                                input.value = JSON.stringify(selectedIds);
                                this.appendChild(input);

                                this.submit();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'No files selected',
                            text: 'Please select at least one file to archive.'
                        });
                    }
                });
            });
        </script>
</body>

</html>
