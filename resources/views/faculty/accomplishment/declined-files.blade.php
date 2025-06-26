<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Declined Files</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
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
        .table td {
        color: #3c3d43;
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
                            <h2 class="pageheader-title">Declined Files</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!"
                                                class="breadcrumb-link">Menu</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:history.back();"
                                                class="breadcrumb-link">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="" class="breadcrumb-link"
                                                style=" color: #3d405c;">
                                              Declined Files</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                
                       <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">List of All Declined Files</h5>
                            </div>
                            <div class="card-body">
                             @if (session('success'))
                                <div id="successAlert" class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                    {{ session('success') }}
                                </div>
                                
                                <script>
                                    setTimeout(function() {
                                        var alert = document.getElementById('successAlert');
                                        if (alert) {
                                            alert.classList.remove('show');  
                                            alert.classList.add('fade');     
                                            
                                            setTimeout(function() {
                                                alert.remove(); 
                                            }, 150);  
                                        }
                                    }, 6000);
                                </script>
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
                                
                            <div class="row">
                           
                            <!-- Filter by Semester Dropdown -->
                            <div class="col-md-3 mb-2 position-relative">
                                <select id="semesterFilter" class="form-control">
                                    <option value="">Select Semester</option>
                                    @foreach($semesters as $semester)
                                        <option value="{{ $semester }}">
                                            @switch($semester)
                                                @case('First Sem')
                                                    First Semester
                                                    @break
                                                @case('Second Sem')
                                                    Second Semester
                                                    @break
                                                @default
                                                    {{ $semester }}
                                            @endswitch
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                            </div>
                        
                            <!-- Filter by School Year Dropdown -->
                            <div class="col-md-3 mb-2 position-relative">
                                <select id="schoolYearFilter" class="form-control">
                                    <option value="">Select School Year</option>
                                    @foreach($schoolYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                            </div>
                        
                            <!-- Filter by Requirements Dropdown -->
                            <div class="col-md-3 mb-2 position-relative">
                               <select name="folder_name" class="form-control">
                                    <option value="">Select Requirements</option>
                                    @foreach($folders as $folder)
                                        <option value="{{ $folder->folder_name_id }}">{{ $folder->folder_name }}</option>
                                    @endforeach
                                </select>
                               <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                            </div>
                                </div>

                                <div class="table-responsive">
                                   <table class="table table-striped table-bordered first"  id="courseTable">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Created Date</th>
                                                <th>Documents</th>
                                                <th>Academic Year</th>
                                                <th>Files</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                              @foreach ($consolidatedFiles as $file)
                                                <tr class="file-row" data-semester="{{ $file['semester'] }}" data-school-year="{{ $file['school_year'] }}">
                                                    <td> {{ $file['subject'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($file['files'][0]['created_at'])->timezone('Asia/Manila')->format('F j, Y, g:iA') }}</td>
                                                    <td>{{ $file['folder_name'] }}</td>
                                                    <td>{{ $file['semester'] }} {{ $file['school_year'] }}</td>
                                                    <td>
                                                       @foreach ($file['files'] as $fileInfo)
                                                           <div class="mb-1">
                                                               <a href="{{ Storage::url($fileInfo['path']) }}" target="_blank" style="text-decoration: underline; color: #3c3d43;">
                                                                   {{ Str::limit($fileInfo['name'], 8, '...') }}
                                                               </a>
                                                           </div>
                                                       @endforeach
                                                    </td>
                                                 <td class="text-center">
                                                    {{ $fileInfo['status'] }}
                                                    @if ($file['files'][0]['declined_reason'])
                                                        <div class="small text-danger">
                                                            <button type="button" 
                                                                class="btn btn-link btn-sm view-messages-btn" 
                                                                data-id="{{ $file['courses_files_id'] }}"
                                                                data-declined-reason="{{ $file['files'][0]['declined_reason'] }}">
                                                                View Messages
                                                            </button>
                                                        </div>
                                                    @endif
                                                </td>

                                                  <td>
                                                    @if ($file['status'] !== 'Approved')
                                                        <button type="button" 
                                                                class="btn btn-warning btn-sm edit-files-btn" 
                                                                data-id="{{ $file['courses_files_id'] }}"
                                                                data-original-filename="{{ $file['files'][0]['name'] }}"
                                                                data-subject="{{ $file['subject'] }}">
                                                           </i> Edit
                                                        </button>
                                                    @endif
                                                    
                                                </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
               <!-- Messages Modal -->
               <div class="modal fade" id="messagesModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Messages</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-3" style="background-color: #f9f9f9;">
                                <div class="message-container mb-3" style="max-height: 400px; overflow-y: auto;"></div>
                                <form id="messageForm" class="mt-3">
                                    <input type="hidden" name="courses_files_id">
                                    <div class="input-group">
                                        <textarea name="message_body" class="form-control" rows="1" required 
                                            placeholder="Type your message..." style="resize: none;"></textarea>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">Send</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit File Modal -->
                <div class="modal fade" id="editFileModal" tabindex="-1" role="dialog" aria-labelledby="editFileModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editFileModalLabel">Edit Files</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editFileForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="fileId" name="fileId">
                                    <div class="form-group">
                                        <strong>Subject: </strong>
                                        <span id="subject" class="form-control-static">Math</span>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Current Files:</label>
                                        <div id="currentFilesList"></div>
                                    </div>
                
                                    <div class="form-group">
                                        <label for="newFiles">Upload New Files</label>
                                        <input type="file" class="form-control" id="newFiles" name="new_files[]" multiple>
                                    </div>
                                    
                                      <div class="progress mt-3" id="editProgress" style="display: none;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                             aria-valuemin="0" aria-valuemax="100">0%
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                            </div>
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.js"></script>
   
    <script>
    //filter table
   document.addEventListener('DOMContentLoaded', function () {
    const semesterFilter = document.getElementById('semesterFilter');
    const schoolYearFilter = document.getElementById('schoolYearFilter');
    const folderFilter = document.querySelector('select[name="folder_name"]');
    const table = document.getElementById('courseTable');
    
    function createNoDataRow() {
        const tbody = table.getElementsByTagName('tbody')[0];
        const existingNoDataRow = tbody.querySelector('.no-data-row');
    
        if (!existingNoDataRow) {
            const noDataRow = document.createElement('tr');
            noDataRow.className = 'no-data-row';
            const noDataCell = document.createElement('td');
            noDataCell.colSpan = 7;
            noDataCell.className = 'text-center py-4';
            noDataCell.innerHTML = '<div class="text-muted">No files found matching the selected filters</div>';
            noDataRow.appendChild(noDataCell);
            tbody.appendChild(noDataRow);
        }
    }
    
    function filterTable() {
        const selectedSemester = semesterFilter.value.trim();
        const selectedSchoolYear = schoolYearFilter.value.trim();
        const selectedFolder = folderFilter ? folderFilter.options[folderFilter.selectedIndex].text.trim() : '';
    
        const dataTable = $('#courseTable').DataTable();
        
        dataTable.columns(3).search('');
        
        let filterString = '';
        if (selectedSemester && selectedSchoolYear) {
            filterString = `^${selectedSemester}\\s${selectedSchoolYear}$`;
        }
        
        dataTable
            .column(3)
            .search(filterString, true, false)
            .column(2)
            .search(selectedFolder === 'Select Documents' ? '' : selectedFolder)
            .draw();
    }
    
    const dataTable = $('#courseTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 15, 20, -1], [10, 15, 20, "All"]],
        columnDefs: [
            { 
                targets: [3],
                type: 'string' 
            }
        ]
    });
    
        semesterFilter.addEventListener('change', filterTable);
        schoolYearFilter.addEventListener('change', filterTable);
        if (folderFilter) folderFilter.addEventListener('change', filterTable);
    });

    //progress
     $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            
            $('#uploadProgress').removeClass('d-none');
            $('#uploadButton').text('Submitting...').prop('disabled', true);
            
            // Create FormData object
            var formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            $('#uploadProgress .progress-bar')
                                .css('width', percent + '%')
                                .attr('aria-valuenow', percent)
                                .text(percent + '%');
                        }
                    });
                    
                    return xhr;
                },
                success: function(response) {
                    $('#uploadProgress .progress-bar')
                        .css('width', '0%')
                        .attr('aria-valuenow', 0)
                        .text('0%');
                    $('#uploadProgress').addClass('d-none');
                    $('#uploadButton').text('Submit').prop('disabled', false);
                    
                    location.reload(); 
                },
                error: function(xhr, status, error) {
                    $('#uploadProgress .progress-bar')
                        .css('width', '0%')
                        .attr('aria-valuenow', 0)
                        .text('0%');
                    $('#uploadProgress').addClass('d-none');
                    $('#uploadButton').text('Submit').prop('disabled', false);
                    
                    alert('An error occurred during upload. Please try again.');
                }
            });
        });
    });
    
    //edit documents
   $('.edit-files-btn').on('click', function() {
    const fileId = $(this).data('id');
    const subject = $(this).data('subject');
    const files = $(this).closest('tr').find('td:nth-child(5) a');
    
    $('#subject').text(subject);
    $('#fileId').val(fileId);
    
    const $currentFilesList = $('#currentFilesList').empty();
    files.each(function(index) {
        $currentFilesList.append(`
            <div class="col-6 mb-2">
                <div class="border rounded px-3 py-1 d-flex align-items-center" 
                     style="background-color: #f8f9fa; border-color: #ddd; position: relative; padding-right: 25px;">
                    <span class="mr-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${$(this).text()}
                    </span>
                    <button type="button" class="btn btn-link text-danger p-0 remove-current-file" 
                            data-index="${index}" 
                            style="position: absolute; top: -5px; right: -5px; background: white; border-radius: 50%; width: 18px; height: 18px; display: flex; justify-content: center; align-items: center; border: 1px solid #ddd;">
                        <i class="fas fa-times" style="font-size: 12px;"></i>
                    </button>
                </div>
            </div>
        `);
    });
    
    $('#currentFilesList').addClass('row');

    if ($currentFilesList.children().length === 1) {
        $currentFilesList.find('.remove-current-file').prop('disabled', true);
    }

        $('.remove-current-file').on('click', function() {
            if ($currentFilesList.children().length > 1) {
                $(this).closest('div').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Remove Last File',
                    text: 'You cannot remove the last file.'
                });
            }
        });
        
        $('#editFileModal').modal('show');
    });

   $('#saveChanges').on('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to update the files?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',  
            cancelButtonColor: '#d33',      
            confirmButtonText: 'Yes, Update!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData($('#editFileForm')[0]);
    
                const remainingFileIndices = [];
                $('#currentFilesList > div').each(function() {
                    remainingFileIndices.push($(this).find('button').data('index'));
                });
                formData.append('remaining_file_indices', JSON.stringify(remainingFileIndices));
    
                // Show the progress bar
                $('#editProgress').show();
                const progressBar = $('#editProgress .progress-bar');
    
                $.ajax({
                    url: '/update-files',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
    
                        // Progress event handler
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e.total) * 100);
                                progressBar.css('width', percentComplete + '%');
                                progressBar.attr('aria-valuenow', percentComplete);
                                progressBar.text(percentComplete + '%');
                            }
                        }, false);
    
                        return xhr;
                    },
                    success: function(response) {
                        if (response.success) {
                            progressBar.css('width', '100%').text('100%');
    
                            setTimeout(() => {
                                $('#editFileModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Files Updated',
                                    text: response.message
                                }).then(() => {
                                    location.reload();
                                });
                            }, 500);  
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                            $('#editProgress').hide();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred'
                        });
                        $('#editProgress').hide();
                    }
                });
            }
        });
    });

    //archive documents
    $(document).ready(function() {
        $('#archive-dates').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                applyLabel: 'Apply'
            }
        });
    
        $('#archive-dates').on('apply.daterangepicker', function(ev, picker) {
            const startDate = picker.startDate.format('MM/DD/YYYY');
            const endDate = picker.endDate.format('MM/DD/YYYY');
            
            console.log('Sending dates:', {startDate, endDate}); 
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to archive all approved files created between ${startDate} and ${endDate}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive them!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('archive.by.date') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            start_date: startDate,
                            end_date: endDate
                        },
                        success: function(response) {
                            console.log('Server response:', response); 
                            
                            if (response.success) {
                                if (response.count > 0) {
                                    Swal.fire(
                                        'Archived!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'No Files Found',
                                        response.message,
                                        'info'
                                    );
                                }
                            } else {
                                Swal.fire(
                                    'Notice',
                                    response.message,
                                    'info'
                                );
                            }
                        },
                        error: function(xhr) {
                            console.error('Ajax error:', xhr); 
                            Swal.fire(
                                'Error',
                                'An error occurred while archiving files',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    
        $('#archive-dates').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
    
    //edit files close button
    $(document).ready(function() {
 
        $('#editFileModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        
        $('.close, [data-dismiss="modal"]').on('click', function() {
            $('#editFileModal').modal('hide');
        });
    });
    $(document).ready(function() {
        $('#editFileModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
        
        $('.close, [data-dismiss="modal"]').on('click', function() {
            $('#editFileModal').modal('hide');
        });
    });
    
    //messages for declined
   document.addEventListener('DOMContentLoaded', function() {
    const messagesModal = document.getElementById('messagesModal');
    const messageContainer = messagesModal.querySelector('.message-container');
    let messageForm = document.getElementById('messageForm');

    document.addEventListener('click', function(event) {
        const button = event.target.closest('.view-messages-btn');
        if (button) {
            const coursesFilesId = button.dataset.id;
            const declinedReason = button.dataset.declinedReason;

            const currentUserId = {{ auth()->id() }};
            const facultyRole = '{{ auth()->user()->role }}';

            const relevantMessages = @json($messages).filter(
                msg => msg.courses_files_id == coursesFilesId
            );

            messageContainer.innerHTML = `
                ${relevantMessages.map(msg => {
                    const isCurrentUserMessage = msg.user_login_id === currentUserId && facultyRole === 'faculty';
                    
                    return `
                        <div class="d-flex ${isCurrentUserMessage ? 'justify-content-end' : 'justify-content-start'} mb-2">
                            <div class="alert ${isCurrentUserMessage ? 'bg-primary text-white' : 'alert-secondary text-dark'} p-2 rounded" style="max-width: 70%;">
                                ${!isCurrentUserMessage ? `<div class="font-weight-bold small mb-1" style="font-weight:bold; font-size:13px;">${msg.user_login.first_name} ${msg.user_login.surname}</div>` : ''}
                                <div>${msg.message_body}</div>
                                <div class="small mt-1">${formatDate(msg.created_at)}</div>
                            </div>
                        </div>
                    `;
                }).join('')}
            `;

            messageForm.courses_files_id.value = coursesFilesId;

            $(messagesModal).modal('show');
        }
    });

    function formatDate(createdAt) {
        const now = new Date();
        const messageDate = new Date(createdAt);
        const timeDifference = now - messageDate;
        const oneDay = 24 * 60 * 60 * 1000;

        if (timeDifference < oneDay) {
            const seconds = Math.floor(timeDifference / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);

            if (seconds < 1) return '1 second ago';
            if (seconds < 60) return `${seconds} second${seconds !== 1 ? 's' : ''} ago`;
            if (minutes < 60) return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
            if (hours < 24) return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
        }

        return messageDate.toLocaleString('en-US', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', 
            hour: '2-digit', minute: '2-digit', hour12: true 
        });
    }

    document.addEventListener('submit', function(event) {
        if (event.target.id === 'messageForm') {
            event.preventDefault();
            const formData = Object.fromEntries(new FormData(event.target).entries());
            const facultyRole = '{{ auth()->user()->role }}';

            fetch('{{ route("send.file.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.error || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    messageContainer.innerHTML += `
                        <div class="d-flex ${facultyRole === 'faculty' ? 'justify-content-end' : 'justify-content-start'} mb-2">
                            <div class="alert ${facultyRole === 'faculty' ? 'bg-primary text-white' : 'alert-secondary text-dark'} p-2 rounded" style="max-width: 70%;">
                                <div>${data.message.message_body}</div>
                                <div class="small mt-1">${formatDate(data.message.created_at)}</div>
                            </div>
                        </div>
                    `;
                    event.target.reset();
                    messageContainer.scrollTop = messageContainer.scrollHeight;
                } else {
                    alert('Failed to send: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Send failed: ' + error.message);
            });
        }
    });

    $('.close').on('click', function() {
            $('#messagesModal').modal('hide');
        });
    });
    </script>
</body>

</html>
