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
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Accomplishment</a></li>
                                        <li class="breadcrumb-item"><a href="" class="breadcrumb-link">
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
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <!-- progress  -->
                        {{-- @php
                            $currentFolderId = request()->route('folder_name_id');
                            $currentMainFolder = null;

                            if ($currentFolderId) {
                                $currentFolder = $folders->where('folder_name_id', $currentFolderId)->first();
                                if ($currentFolder) {
                                    $currentMainFolder = $currentFolder->main_folder_name;
                                }
                            }
                        @endphp

@foreach ($progress as $folderName => $progressData)
@if (!$currentMainFolder || $currentMainFolder === $folderName)
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $folderName }} Progress</h5>

            <div class="progress mb-1">
                <div class="progress-bar" role="progressbar"
                    style="width: {{ $progressData['percentage'] }}%;" 
                    aria-valuenow="{{ $progressData['percentage'] }}"
                    aria-valuemin="0" 
                    aria-valuemax="100">
                    {{ number_format($progressData['percentage'], 2) }}%
                </div>
            </div>

            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    Progress breakdown for {{ $folderName }}:
                    <ul class="list-unstyled mt-1">
                        <li>✓ {{ $progressData['approved'] }} Approved</li>
                        <li>⏳ {{ $progressData['pending'] }} Pending Approval</li>
                        <li>❌ {{ $progressData['not_uploaded'] }} Not Uploaded</li>
                        <li>📊 {{ $progressData['total'] }} Total Required Files</li>
                    </ul>
                </small>
            </div>
        </div>
    </div>
@endif
@endforeach --}}
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
                                        <button class="btn btn-secondary mb-3" disabled id="uploadButton">
                                            Successfully Uploaded
                                        </button>
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
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered first">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Semester</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedFiles as $semester => $files)
                                                @if ($files->contains('user_login_id', auth()->id()))
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        </td>
                                                        <td>{{ $semester }}</td>
                                                        <td>
                                                            <a href="{{ route('faculty.accomplishment.view-uploaded-files', ['user_login_id' => auth()->id(), 'folder_name_id' => $folder->folder_name_id, 'semester' => $semester]) }}"
                                                                class="btn btn-info text-white">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
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
                                    <div class="mb-3">
                                        <div class="">
                                            <h5 class="mb-3">Current Semester: {{ $semester }}</h5>
                                        </div>
                                    </div>
                                    <form id="uploadForm" action="{{ route('files.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="folder_name_id"
                                            value="{{ $folder->folder_name_id }}">
                                        @foreach ($courseSchedules as $index => $schedule)
                                            <input type="hidden" name="course_schedule_ids[]"
                                                value="{{ $schedule->course_schedule_id }}">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="file{{ $index + 1 }}"
                                                            style="display: inline-block; margin-bottom: 0;">
                                                            <span>
                                                                <strong>Subject:</strong>
                                                                {{ $schedule->course_subjects }} <span
                                                                    style="color: red;">*</span><br>
                                                                <strong>Subject Code:</strong>
                                                                {{ $schedule->course_code }}<br>
                                                                <strong>Schedule:</strong> {{ $schedule->schedule }}
                                                            </span>
                                                        </label>
                                                        <p>
                                                            <span><strong>Year & Section:</strong>
                                                                {{ $schedule->year_section }}</span><br>
                                                            <span><strong>Program:</strong>
                                                                {{ $schedule->program }}</span>
                                                        </p>
                                                        <div class="file-input-container">
                                                            <input type="file"
                                                                id="fileInput{{ $schedule->course_schedule_id }}"
                                                                name="files[{{ $schedule->course_schedule_id }}][]"
                                                                multiple accept=".pdf, .doc, .docx, .xls, .xlsx"
                                                                required>
                                                            <small class="text-danger"
                                                                id="error{{ $schedule->course_schedule_id }}"></small>
                                                        </div>
                                                        <input type="hidden" name="course_schedule_ids[]"
                                                            value="{{ $schedule->course_schedule_id }}">
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
                                            <button type="submit" class="btn btn-success"
                                                id="uploadButton">Submit</button>
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
            </script>
</body>

</html>
