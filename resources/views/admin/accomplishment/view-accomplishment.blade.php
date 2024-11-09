<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>View Uploaded Files</title>
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
                            <h2 class="pageheader-title">View Uploaded Files</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('admin.accomplishment.department', ['folder_name_id' => $folder_name_id]) }}"
                                                class="breadcrumb-link">
                                                Department
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('viewAccomplishmentDepartment', [
                                                    'department' => urlencode($department),
                                                    'folder_name_id' => $folder->folder_name_id,
                                                ]) }}"
                                                class="breadcrumb-link">Faculty</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.accomplishment.view-accomplishment', [
                                                    'user_login_id' => $faculty->user_login_id,
                                                    'folder_name_id' => $folder->folder_name_id,
                                                ]) }}"
                                                class="breadcrumb-link" style=" color: #3d405c;">View Uploaded Files</a>
                                        </li>
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
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select id="semesterFilter" class="form-control">
                                            <option value="">Filter Semester</option>
                                            @foreach ($semesters as $semester)
                                                <option value="{{ $semester }}">{{ $semester }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute"
                                            style="right: 25px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="schoolYearFilter" class="form-control">
                                            <option value="">Filter School Year</option>
                                            @foreach ($schoolYears as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute"
                                            style="right: 25px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                </div>

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                
                                <div class="table-responsive">
                                    <div class="table-responsive">
                                        <table id="example" class="table table-striped table-bordered second"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Date & Time</th>
                                                    <th>Faculty Name</th>
                                                    <th>Course Code & Course</th>
                                                    <th>Year & Program</th>
                                                    <th>Semester</th>
                                                    <th>File</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="fileTableBody">
                                                @php $index = 1; @endphp
                                                @foreach ($groupedFiles as $key => $group)
                                                    @php
                                                        $attributes = explode('|', $key);
                                                        $courseCode = $attributes[0];
                                                        $yearSection = $attributes[1];
                                                        $program = $attributes[2];
                                                        $semester = $attributes[3];
                                                        $schoolYear = $attributes[4];
                                                        $status = $attributes[5];
                                                        $firstFile = $group->first();
                                                    @endphp
                                                    <tr data-semester="{{ $semester }}"
                                                        data-school-year="{{ $schoolYear }}">
                                                        <td class="row-number">{{ $index++ }}</td>
                                                        <td>{{ $firstFile->created_at->timezone('Asia/Manila')->format('F j, Y, g:i A') }}
                                                        </td>
                                                        <td>{{ $firstFile->userLogin->first_name }}
                                                            {{ $firstFile->userLogin->surname }}</td>
                                                        <td>
                                                            <div
                                                                style="display: flex; flex-direction: column; margin-bottom: 20px;">
                                                                {{ $courseCode ?? 'N/A' }}
                                                                <span
                                                                    style="margin-top: 5px;">{{ $firstFile->subject ?? 'N/A' }}</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{ $yearSection ?? 'N/A' }} -
                                                            <span>{{ $program ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>{{ $semester ?? 'N/A' }} {{ $schoolYear ?? 'N/A' }}</td>
                                                        <td>
                                                            @foreach ($group as $file)
                                                                <a href="{{ Storage::url($file->files) }}"
                                                                    target="_blank"
                                                                    style="color: rgb(65, 65, 231); text-decoration: underline;">
                                                                    {{ $file->original_file_name }}
                                                                </a><br>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            @if ($status === 'To Review')
                                                                <span
                                                                    class="badge badge-primary">{{ $status }}</span>
                                                            @elseif ($status === 'Declined')
                                                                <span
                                                                    class="badge badge-danger">{{ $status }}</span>
                                                                <br>
                                                                <span>Declined Reason:
                                                                    {{ $firstFile->declined_reason }}</span>
                                                            @elseif ($status === 'Approved')
                                                                <span
                                                                    class="badge badge-success">{{ $status }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                @if ($status === 'To Review')
                                                                    <a href="{{ route('approveFile', ['courses_files_id' => $firstFile->courses_files_id]) }}"
                                                                        class="btn btn-success btn-sm mb-2">
                                                                        Approve
                                                                    </a>
                                                                    <button type="button"
                                                                        class="btn btn-warning btn-sm mb-2"
                                                                        data-toggle="modal"
                                                                        data-target="#declineModal"
                                                                        data-id="{{ $firstFile->courses_files_id }}">
                                                                        Decline
                                                                    </button>
                                                                @endif
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm delete-button"
                                                                    data-id="{{ $firstFile->courses_files_id }}">
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
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="declineForm" method="POST">
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
                    $('#declineForm').attr('action', actionUrl);
                });

                $('#declineForm').on('submit', function() {
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

            //semesters
            document.addEventListener('DOMContentLoaded', function() {
                const semesterFilter = document.getElementById('semesterFilter');
                const schoolYearFilter = document.getElementById('schoolYearFilter');
                const tableBody = document.getElementById('fileTableBody');

                function filterTable() {
                    const selectedSemester = semesterFilter.value.trim();
                    const selectedSchoolYear = schoolYearFilter.value.trim();
                    let visibleIndex = 1;

                    const rows = tableBody.getElementsByTagName('tr');

                    Array.from(rows).forEach(row => {
                        const rowSemester = row.getAttribute('data-semester').trim();
                        const rowSchoolYear = row.getAttribute('data-school-year').trim();

                        const semesterMatch = !selectedSemester || rowSemester === selectedSemester;
                        const schoolYearMatch = !selectedSchoolYear || rowSchoolYear === selectedSchoolYear;

                        if (semesterMatch && schoolYearMatch) {
                            row.style.display = '';
                            const numberCell = row.querySelector('.row-number');
                            if (numberCell) {
                                numberCell.textContent = visibleIndex++;
                            }
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

                semesterFilter.addEventListener('change', filterTable);
                schoolYearFilter.addEventListener('change', filterTable);

                filterTable();
            });
        </script>

        @include('partials.tables-footer')
