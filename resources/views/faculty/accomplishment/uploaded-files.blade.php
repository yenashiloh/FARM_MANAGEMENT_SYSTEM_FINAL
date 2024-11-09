<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
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

        .file-input-container {
            display: flex;
            flex-direction: column;
        }

        .file-input-container input[type="file"] {
            margin-bottom: 5px;
        }

        .file-input-container small {
            order: 1;
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
                                        <li class="breadcrumb-item"><a href="#!"
                                                class="breadcrumb-link">Accomplishment</a></li>
                                        <li class="breadcrumb-item"><a href="" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">
                                                {{ $folderName }} </a></li>
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
                    @if (auth()->user()->role == 'faculty-coordinator')
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                            <div class="simple-card">
                                <ul class="nav nav-tabs" id="myTab5" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active border-left-0" id="home-tab-simple" data-toggle="tab"
                                            href="#home-simple" role="tab" aria-controls="home"
                                            aria-selected="true">My Document Upload Progress</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="department-tab" data-toggle="tab" href="#department"
                                            role="tab" aria-controls="department" aria-selected="false">All
                                            Departments</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="myTabContent5">
                                    <div class="tab-pane fade show active" id="home-simple" role="tabpanel"
                                        aria-labelledby="home-tab-simple">
                                        <div class="card-body">
                                            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                <p class="mb-0">This progress includes all documents that have been
                                                    approved by the admin.</p>
                                            </div>
                                            <h5 class="mb-3">Overall Progress</h5>
                                            <div class="progress mb-4">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $overallProgress }}%;"
                                                    aria-valuenow="{{ $overallProgress }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ number_format($overallProgress, 2) }}%
                                                </div>
                                            </div>
                                            <hr>

                                            @php
                                                $currentMainFolder = null;
                                                $currentFolderId = request()->route('folder_name_id');
                                                $currentFolder = $folders->firstWhere(
                                                    'folder_name_id',
                                                    $currentFolderId,
                                                );
                                                if ($currentFolder) {
                                                    $currentMainFolder = $currentFolder->main_folder_name;
                                                }
                                            @endphp

                                            @if ($currentMainFolder && isset($folderProgress[$currentMainFolder]))
                                                <h5 class="mb-3">{{ $currentMainFolder }} Progress</h5>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ $folderProgress[$currentMainFolder] }}%;"
                                                        aria-valuenow="{{ $folderProgress[$currentMainFolder] }}"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        {{ number_format($folderProgress[$currentMainFolder], 2) }}%
                                                    </div>
                                                </div>
                                                <hr>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="department" role="tabpanel"
                                        aria-labelledby="department-tab" style="padding: 20px;">
                                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <p class="mb-0">This progress shows the overall progress for each
                                                department.</p>
                                        </div>
                                        @foreach ($departmentProgress as $departmentName => $progress)
                                            <h5 class="mb-3">{{ $departmentName }}</h5>
                                            <div class="progress mb-4">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $progress }}%;"
                                                    aria-valuenow="{{ $progress }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ number_format($progress, 2) }}%
                                                </div>
                                            </div>
                                            <hr>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(auth()->user()->role == 'faculty')
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <p class="mb-0">This progress includes all documents that have been approved
                                            by the admin.</p>
                                    </div>
                                    <h6>Overall Progress</h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $overallProgress }}%;"
                                            aria-valuenow="{{ $overallProgress }}" aria-valuemin="0"
                                            aria-valuemax="100">
                                            {{ number_format($overallProgress, 2) }}%
                                        </div>
                                    </div>

                                    @php
                                        $currentMainFolder = null;
                                        $currentFolderId = request()->route('folder_name_id');
                                        $currentFolder = $folders->firstWhere('folder_name_id', $currentFolderId);
                                        if ($currentFolder) {
                                            $currentMainFolder = $currentFolder->main_folder_name;
                                        }
                                    @endphp

                                    @if ($currentMainFolder && isset($folderProgress[$currentMainFolder]))
                                        <h6>{{ $currentMainFolder }} Progress</h6>
                                        <div class="progress mb-3">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $folderProgress[$currentMainFolder] }}%;"
                                                aria-valuenow="{{ $folderProgress[$currentMainFolder] }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($folderProgress[$currentMainFolder], 2) }}%
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="alert alert-warning" role="alert">
                                        You do not have permission to view this content.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"> {{ $folderName }} (an academic document that communicates
                                    information about a specific course and
                                    explains the rules, responsibilities, and expectations associated with it.)</h5>
                            </div>
                            <div class="card-body">

                                <!-- Upload Files Button -->
                                @if ($isUploadOpen)
                                    <p style="color: #222222;">
                                        <strong>Opened:</strong> {{ $formattedStartDate }}<br>
                                        <strong>Due:</strong> {{ $formattedEndDate }}<br>
                                    </p>
                                    @if (!$hasUploaded)
                                        <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal"
                                            data-bs-target="#addFolderModal" id="uploadButton">
                                            <i class="fas fa-plus"></i> Upload Files
                                        </a>
                                    @else
                                        <p class="text-success mb-3" id="uploadMessage">Successfully Uploaded</p>
                                    @endif
                                @else
                                    <p class="text-danger">
                                        {{ $statusMessage }}
                                        @if ($statusMessage !== 'No upload schedule set.')
                                            <br><br>
                                            <strong style="color: #222222;">Opened:</strong>
                                            <span style="color: #222222;">{{ $formattedStartDate }}</span><br>
                                            <strong style="color: #222222;">Due:</strong>
                                            <span style="color: #222222;">{{ $formattedEndDate }}</span><br>
                                        @endif
                                    </p>
                                @endif

                                @if (!$isUploadOpen)
                                    <button type="button" class="btn btn-warning mb-2" data-toggle="modal"
                                        data-target="#requestModal">
                                        Request Upload Access
                                    </button>
                                @endif

                                <!-- Modal for Request to Open -->
                                <div class="modal fade" id="requestModal" tabindex="-1" role="dialog"
                                    aria-labelledby="requestModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="requestModalLabel">Request Upload Access
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('request.upload.access') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="reason" class="form-label">Reason for
                                                            Request</label>
                                                        <textarea class="form-control" id="reason" name="reason" rows="6" required></textarea>
                                                    </div>
                                                    <input type="hidden" name="user_login_id"
                                                        value="{{ auth()->id() }}">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-warning">Submit
                                                        Request</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show text-center"
                                        role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show text-center"
                                        role="alert">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="d-flex flex-column mb-3">
                                    <!-- Filter by Semester Dropdown at the top -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <select id="semesterFilter" class="form-control">
                                                <option value="">Select Semester</option>
                                                @foreach($semesters as $semester)
                                                    <option value="{{ $semester }}">{{ $semester }}</option>
                                                @endforeach
                                            </select>
                                            <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                        </div>
                                        
                                    
                                        <div class="col-md-3">
                                            <select id="schoolYearFilter" class="form-control">
                                                <option value="">Select School Year</option>
                                                @foreach($schoolYears as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                            <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                        </div>
                                    </div>

                                    <!-- Archive Buttons -->
                                    <div class="d-flex align-items-center">
                                        @if ($consolidatedFiles->contains('status', 'Approved'))
                                            <form id="archive-all-form" action="{{ route('files.archiveAll') }}"
                                                method="POST" class="mr-3">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">Archive</button>
                                            </form>

                                            <form id="archive-date-range-form"
                                                action="{{ route('files.archiveByDateRange') }}" method="POST"
                                                class="d-flex align-items-center mr-3">
                                                @csrf
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">From:</span>
                                                    </div>
                                                    <input type="date" name="from_date" id="from_date"
                                                        class="form-control form-control-sm mr-2" required>
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">To:</span>
                                                    </div>
                                                    <input type="date" name="to_date" id="to_date"
                                                        class="form-control form-control-sm" required>
                                                    <div class="input-group-append">
                                                        <button type="submit" class="btn btn-danger btn-sm">Archive
                                                            by Date</button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered first"  id="courseTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    @if ($consolidatedFiles->contains('status', 'Approved'))
                                                        <input type="checkbox" id="select-all">
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </th>
                                                <th>No.</th>
                                                <th>Date & Time</th>
                                                <th>Semester</th>
                                                <th>Program</th>
                                                <th>Course & Course Code</th>
                                                <th>Year & Section</th>
                                                <th>File Names</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($consolidatedFiles as $file)
                                                <tr class="file-row" data-semester="{{ $file['semester'] }}"
                                                    data-school-year="{{ $file['school_year'] }}">
                                                    <td>
                                                        @if ($file['status'] === 'Approved')
                                                            <input type="checkbox" class="file-checkbox"
                                                                value="{{ implode(',', $file['courses_files_ids']) }}">
                                                        @else
                                                            &nbsp;
                                                        @endif
                                                    </td>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($file['files'][0]['created_at'])->format('F j, Y, g:iA') }}
                                                    </td>
                                                    <td>{{ $file['semester'] }} {{ $file['school_year'] }}</td>
                                                    <td>{{ $file['program'] }}</td>
                                                    <td>{{ $file['subject_name'] }} ({{ $file['course_code'] }})</td>
                                                    <td>{{ $file['courseSchedule']['year_section'] }}</td>
                                                    <td>
                                                        @foreach ($file['files'] as $fileInfo)
                                                            <div class="mb-1">
                                                                <a href="{{ Storage::url($fileInfo['path']) }}"
                                                                    target="_blank"
                                                                    style="color: rgb(65, 65, 231); text-decoration: underline;">
                                                                    {{ $fileInfo['name'] }}
                                                                </a>
                                                                @if ($fileInfo['declined_reason'])
                                                                    <div class="small text-danger">Reason:
                                                                        {{ $fileInfo['declined_reason'] }}</div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $fileInfo['status'] === 'Approved' ? 'success' : ($fileInfo['status'] === 'To Review' ? 'primary' : 'danger') }} ml-2">
                                                            {{ $fileInfo['status'] }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($file['status'] !== 'Approved')
                                                            <button class="btn btn-warning btn-sm edit-files-btn"
                                                                data-file-id="{{ $file['courses_files_id'] }}"
                                                                data-files="{{ json_encode($file['files']) }}"
                                                                data-semester="{{ $file['semester'] }}"
                                                                data-school-year="{{ $file['school_year'] }}"
                                                                data-program="{{ $file['program'] }}"
                                                                data-course-subject-code="{{ $file['subject_name'] }} - {{ $file['courseSchedule']['course_code'] }}"
                                                                data-year-section="{{ $file['courseSchedule']['year_section'] }}">
                                                                Edit
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
                            <p><strong>Reminder:</strong> Files with an <strong>Approved</strong> status cannot be
                                edited. You can only make changes to files with a status of <strong>Declined</strong> or
                                <strong>To Review</strong>.
                            </p>
                            <form id="editFileForm" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="editFileId" name="id">
                                <div class="container">
                                    <!-- Form Fields -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="semester">Semester:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="semester" readonly>
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
                                                <input type="text" class="form-control" id="program" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="courseSubjectCode">Course & Course Code:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="courseSubjectCode"
                                                    readonly>
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
                                                <input type="text" class="form-control" id="yearSection" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Current Files Section -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label>Current Files:</label>
                                        </div>
                                        <div class="col-md-8">
                                            <div id="currentFiles">
                                                <!-- Current files will be dynamically added here -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Upload New Files Section -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="files">Upload New Files:</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control-file" id="files"
                                                name="files[]" accept=".pdf" multiple>
                                            <small class="text-danger" id="fileError"></small>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Files Modal -->
            <div class="modal fade" id="addFolderModal" tabindex="-1" aria-labelledby="addFolderModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addFolderModalLabel">Upload Files</h5>
                        </div>
                        <div class="modal-body body-modal">
                            <div class="d-flex justify-content-center mb-4">
                                <h5 class="m-0">
                                    <strong>Instructions:</strong>
                                    Please upload the files related to your teaching courses. All input fields
                                    with the symbol (<span style="color: red;">*</span>) are required. Only
                                    <strong>PDF</strong> file is accepted.
                                </h5>
                            </div>
                            <form id="uploadForm" action="{{ route('files.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="folder_name_id" value="{{ $folderNameId }}">

                                <!-- Dropdown for Semester and School Year -->
                                <div class="row">

                                    <div class="mb-3 col-md-6">
                                        <label for="semester" class="form-label"><strong>Semester</strong></label>
                                        <select name="semester" id="semester" class="form-control"
                                            style="min-width: 190px; width: 100%; height: calc(1.5em + .75rem + 2px); padding-right: 30px;"
                                            required>
                                            <option value="" disabled selected>Select Semester</option>
                                            <option value="First Sem">First Sem</option>
                                            <option value="Second Sem">Second Sem</option>
                                            <option value="Summer">Summer</option>
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 70%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                    
                                    <div class="mb-3 col-md-6">
                                        <label for="school_year" class="form-label"><strong>School Year</strong></label>
                                        <select name="school_year" id="school_year" class="form-control"
                                            style="min-width: 190px; width: 100%; height: calc(1.5em + .75rem + 2px); padding-right: 30px;"
                                            required>
                                            <option value="" disabled selected>Select School Year</option>
                                            <option value="{{ $currentYear . '-' . ($currentYear + 1) }}">
                                                {{ $currentYear . '-' . ($currentYear + 1) }}</option>
                                            <option value="{{ $currentYear + 1 . '-' . ($currentYear + 2) }}">
                                                {{ $currentYear + 1 . '-' . ($currentYear + 2) }}</option>
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 70%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                </div>


                                @foreach ($courseSchedules as $index => $schedule)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="file{{ $index + 1 }}"
                                                    style="display: inline-block; margin-bottom: 0;">
                                                    <strong>Subject:</strong> {{ $schedule->course_subjects }}<br>
                                                    <strong>Subject Code:</strong> {{ $schedule->course_code }}<br>
                                                    <strong>Schedule:</strong> {{ $schedule->schedule }}
                                                </label>
                                                <p>
                                                    <strong>Year & Section:</strong> {{ $schedule->year_section }}<br>
                                                    <strong>Program:</strong> {{ $schedule->program }}
                                                </p>
                                                <input type="file"
                                                    id="fileInput{{ $schedule->course_schedule_id }}"
                                                    name="files[{{ $schedule->course_schedule_id }}][]" multiple
                                                    accept=".pdf, .doc, .docx, .xls, .xlsx" required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="progress mt-3 d-none" id="uploadProgress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success" id="uploadButton">Submit</button>
                                </div>
                            </form>
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
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    const fileInput = this;
                    const errorElement = document.getElementById('error' + fileInput.id.replace(
                        'fileInput', ''));

                    if (fileInput.files.length > 0) {
                        const validTypes = ['application/pdf'];
                        let valid = true;

                        for (let i = 0; i < fileInput.files.length; i++) {
                            if (!validTypes.includes(fileInput.files[i].type)) {
                                valid = false;
                                break;
                            }
                        }

                        if (!valid) {
                            errorElement.textContent = 'Please upload only PDF files.';
                            fileInput.value = '';
                        } else {
                            errorElement.textContent = '';
                        }
                    } else {
                        errorElement.textContent = '';
                    }
                });
            });

            const form = document.getElementById('uploadForm');
            const uploadButton = document.getElementById('uploadButton');
            const progressBar = document.querySelector('#uploadProgress .progress-bar');
            const progressContainer = document.getElementById('uploadProgress');

            if (form && uploadButton) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(form);

                    uploadButton.textContent = 'Submitting...';
                    uploadButton.disabled = true;
                    uploadButton.classList.remove('btn-success');
                    uploadButton.classList.add('btn-secondary');
                    uploadButton.removeAttribute('data-bs-toggle');
                    uploadButton.removeAttribute('data-bs-target');

                    progressContainer.classList.remove('d-none');

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action, true);
                    xhr.upload.onprogress = function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            progressBar.style.width = percentComplete + '%';
                            progressBar.textContent = percentComplete.toFixed(0) + '%';
                            progressBar.setAttribute('aria-valuenow', percentComplete);
                        }
                    };
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            uploadButton.textContent = 'Successfully Uploaded';
                            window.location.reload();
                        } else {
                            const errorMessage = xhr.responseText ? JSON.parse(xhr.responseText)
                                .message : 'An error occurred during upload.';
                            alert(errorMessage);
                            uploadButton.textContent = 'Upload Files';
                            uploadButton.disabled = false;
                            uploadButton.classList.remove('btn-secondary');
                            uploadButton.classList.add('btn-success');
                        }
                    };
                    xhr.send(formData);
                });
            }
        });

        //edit files
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let filesToDelete = [];

            $('.edit-files-btn').on('click', function() {
                var fileId = $(this).data('file-id');
                var semester = $(this).data('semester');
                var program = $(this).data('program');
                var courseSubjectCode = $(this).data('course-subject-code');
                var yearSection = $(this).data('year-section');
                var files = $(this).data('files');

                // Reset the filesToDelete array
                filesToDelete = [];

                $('#editFileId').val(fileId);
                $('#semester').val(semester);
                $('#program').val(program);
                $('#courseSubjectCode').val(courseSubjectCode);
                $('#yearSection').val(yearSection);

                // Clear and populate current files
                $('#currentFiles').empty();

                // Check if files exists and is valid
                if (files && Array.isArray(files)) {
                    files.forEach(function(file) {
                        $('#currentFiles').append(`
                    <div class="file-item mb-2">
                        <span class="me-2">${file.name}</span>
                        <button type="button" class="btn btn-danger btn-sm delete-file" data-file="${file.path}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
                    });
                }

                var myModal = new bootstrap.Modal(document.getElementById('editFileModal'));
                myModal.show();
            });

            // Handle delete button clicks
            $(document).on('click', '.delete-file', function(e) {
                e.preventDefault();
                const fileToDelete = $(this).data('file');
                if (!filesToDelete.includes(fileToDelete)) {
                    filesToDelete.push(fileToDelete);
                }
                $(this).closest('.file-item').remove();
            });

            $('#saveChanges').on('click', function() {
                var formData = new FormData($('#editFileForm')[0]);
                var fileId = $('#editFileId').val();

                // Ensure filesToDelete is correctly appended as an array
                formData.delete('removed_files[]'); // Remove any existing removed_files entry
                filesToDelete.forEach(file => {
                    formData.append('removed_files[]', file);
                });
                formData.append('_method', 'PUT');

                // Get all selected files
                const fileInput = document.getElementById('files');
                if (fileInput && fileInput.files.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('files[]', fileInput.files[i]);
                    }
                }

                $.ajax({
                    url: '/update-file/' + fileId,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            window.location.reload();
                        } else {
                            alert('Error updating file');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert(xhr.responseJSON ? xhr.responseJSON.message : error);
                    }
                });
            });
        });


        //archive
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
            // Existing code for select all checkbox
            const selectAllCheckbox = document.getElementById('select-all');
            const fileCheckboxes = document.querySelectorAll('.file-checkbox');

            selectAllCheckbox.addEventListener('change', function() {
                fileCheckboxes.forEach(checkbox => {
                    if (checkbox.offsetParent !== null) {
                        checkbox.checked = this.checked;
                    }
                });
            });

            // Date range archive form submission
            document.getElementById('archive-date-range-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const fromDate = document.getElementById('from_date').value;
                const toDate = document.getElementById('to_date').value;

                if (!fromDate || !toDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Date Range Required',
                        text: 'Please select both start and end dates.'
                    });
                    return;
                }

                if (fromDate > toDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Range',
                        text: 'Start date must be before or equal to end date.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to archive all approved files from ${fromDate} to ${toDate}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, archive them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });

            // Existing archive all form submission
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

        //filter semester 
       
    // JavaScript (using jQuery)
$(document).ready(function() {
    // Get the table rows and filter dropdowns
    var $tableRows = $('#courseTable tbody tr');
    var $semesterFilter = $('#semesterFilter');
    var $schoolYearFilter = $('#schoolYearFilter');

    // Add event listeners to the filter dropdowns
    $semesterFilter.on('change', filterTable);
    $schoolYearFilter.on('change', filterTable);

    function filterTable() {
        var selectedSemester = $semesterFilter.val();
        var selectedSchoolYear = $schoolYearFilter.val();

        // Filter the table rows based on the selected values
        $tableRows.each(function() {
            var $row = $(this);
            var rowSemester = $row.data('semester');
            var rowSchoolYear = $row.data('school-year');

            if ((selectedSemester === '' || rowSemester === selectedSemester) &&
                (selectedSchoolYear === '' || rowSchoolYear === selectedSchoolYear)) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    }
});
    </script>
</body>

</html>
