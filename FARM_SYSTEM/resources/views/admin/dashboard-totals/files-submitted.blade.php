@include('partials.tables-header')
<title>Files Submitted </title>
</head>
<style>
    .form-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }

    .col-form-label {
        margin-right: 10px;
        white-space: nowrap;
    }

    .col-sm-3 {
        flex: 1;
        min-width: 200px;
        position: relative;
    }

    select.form-control {
        width: 100%;
        padding-right: 30px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .fas.fa-chevron-down {
        position: absolute;
        top: 50%;
        right: 25px;
        transform: translateY(-50%);
        pointer-events: none;
        z-index: 1;
    }

    @media (max-width: 576px) {
        .form-group {
            flex-direction: column;
            align-items: stretch;
        }

        .col-form-label {
            margin-bottom: 5px;
        }

        .col-sm-3 {
            width: 100%;
        }
    }
</style>

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
                            <h2 class="pageheader-title">Files Submitted </h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                         <li class="breadcrumb-item">
                                             <a href=""
                                                class="breadcrumb-link" style=" color: #3d405c;">Menu</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                             <a href="{{route ('admin.admin-dashboard')}}"
                                                class="breadcrumb-link" style=" color: #3d405c;">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{route ('admin.dashboard-totals.files-submitted')}}"
                                                class="breadcrumb-link" style=" color: #3d405c;">Files Submitted</a>
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
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

                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered first">
                                            <thead>
                                                <tr>
                                                    <th>Faculty Name</th>
                                                    <th>Subject</th>
                                                    <th>Created Date</th>
                                                    <th>Documents</th>
                                                    <th>Files</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($processedFiles as $index => $file)
                                                    <tr>
                                                        <td>
                                                            @if(isset($files[$index]->userLogin->first_name))
                                                                {{ $files[$index]->userLogin->first_name }} {{ $files[$index]->userLogin->surname }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>{{ $file['subject'] ?? 'N/A' }}</td>
                                                        <td>
                                                            @if(isset($file['created_at']))
                                                                {{ \Carbon\Carbon::parse($file['created_at'])->format('F d, Y, g:iA') }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(isset($files[$index]->folderName->folder_name))
                                                                {{ $files[$index]->folderName->folder_name }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $fileInfoArray = [];
                                                                try {
                                                                    if (isset($file['files'])) {
                                                                        if (is_string($file['files'])) {
                                                                            $fileInfoArray = json_decode($file['files'], true);
                                                                        } elseif (is_array($file['files'])) {
                                                                            $fileInfoArray = $file['files'];
                                                                        }
                                                                        if (isset($fileInfoArray['path'])) {
                                                                            $fileInfoArray = [$fileInfoArray];
                                                                        }
                                                                    }
                                                                } catch (\Exception $e) {
                                                                    $fileInfoArray = [];
                                                                }
                                                            @endphp
                                                            
                                                            @if(is_array($fileInfoArray) && count($fileInfoArray) > 0)
                                                                @foreach($fileInfoArray as $fileInfo)
                                                                    <div class="mb-1">
                                                                        <a href="{{ Storage::url($fileInfo['path']) }}" target="_blank" style="text-decoration: underline; color: #3c3d43;">
                                                                            {{ Str::limit($fileInfo['name'] ?? basename($fileInfo['path']), 8, '...') }}
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                No files available
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $file['status'] }}
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-start">
                                                                @if ($file['status'] === 'To Review')
                                                                    <a href="javascript:void(0)" 
                                                                        onclick="confirmApproval('{{ route('approveFile', ['courses_files_id' => $file['courses_files_id']]) }}')"
                                                                        class="btn btn-success btn-sm mb-2 mr-2">
                                                                        Approve
                                                                    </a>
                                                                    <button type="button" class="btn btn-danger btn-sm mb-2 mr-2" data-toggle="modal" data-target="#declineModal"
                                                                            data-id="{{ $file['courses_files_id'] }}">
                                                                        Decline
                                                                    </button>
                                                                @elseif ($file['status'] === 'Approved')
                                                                    <button type="button" class="btn btn-warning btn-sm mb-2 mr-2" onclick="undoApproval('{{ route('undoApproval', ['courses_files_id' => $file['courses_files_id']]) }}')">
                                                                        Undo Approval
                                                                    </button>
                                                                @elseif ($file['status'] === 'Declined')
                                                                    <button type="button" class="btn btn-warning btn-sm mb-2 mr-2" onclick="undoDeclined('{{ route('undoDeclined', ['courses_files_id' => $file['courses_files_id']]) }}')">
                                                                        Undo Declined
                                                                    </button>
                                                                @endif
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
                            
    @include('partials.tables-footer')
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    
            setTimeout(function() {
                $('#successAlert').fadeOut('slow');
            }, 3000);
    
            setTimeout(function() {
                $('#errorAlert').fadeOut('slow');
            }, 3000);
        });
    
     // confirmation approve
        function confirmApproval(approvalUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to approve this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = approvalUrl;
                }
            });
        }
        
        // confirmation for Undo Declined
       function undoDeclined(undoDeclinedUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to undo the decline of this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, undo it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = undoDeclinedUrl;
                }
            });
        }
            
        // confirmation Undo
        function undoApproval(approvalUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to undo the approval for this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, undo it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = approvalUrl;
                }
            });
        }

    </script>
</body>

</html>
