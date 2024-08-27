@include('partials.tables-header')
<title>{{ $folderName }} </title>
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
                            <h2 class="pageheader-title">{{ $folderName }} </h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Accomplishment</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.accomplishment.admin-uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}"
                                                class="breadcrumb-link">{{ $folderName }}</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-0"> {{ $folderName }} (an academic document that communicates
                                    information about a specific course and
                                    explains the rules, responsibilities, and expectations associated with it.)</h5>
                                <br>

                                <table class="table table-striped table-bordered first">

                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Date & Time</th>
                                            <th>Employee Name</th>
                                            <th>Semester</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groupedFiles as $semester => $files)
                                            @php
                                                $file = $files->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ \Carbon\Carbon::now('Asia/Manila')->locale('en_PH')->format('F j, Y, g:i A') }}
                                                </td>
                                                <td>{{ $file->user_name }}</td>
                                                <td>{{ $semester }}</td>
                                                <td>
                                                    <a href="{{ route('admin.accomplishment.view-accomplishment', ['user_login_id' => $user_login_id, 'folder_name_id' => $folder_name_id]) }}"
                                                        class="btn btn-info">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Files Modal -->
                <div class="modal fade" id="viewFilesModal" tabindex="-1" aria-labelledby="viewFilesModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewFilesModalLabel">Uploaded Files</h5>
                            </div>
                            <div class="modal-body view-modal">
                                <ul id="fileList" class="list-group">
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
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

                    document.addEventListener('DOMContentLoaded', function() {
                        const viewFilesModal = document.getElementById('viewFilesModal');
                        viewFilesModal.addEventListener('show.bs.modal', function(event) {
                            const button = event.relatedTarget;
                            const files = JSON.parse(button.getAttribute('data-files'));

                            console.log(files);
                            const fileList = document.getElementById('fileList');
                            fileList.innerHTML = '';

                            files.forEach(file => {
                                const year = file.year || 'Not specified';
                                const program = file.program || 'Not specified';
                                const code = file.code || 'Not specified';
                                const status = file.status || 'Not specified';
                                const declinedReason = file.declined_reason || '';

                                let badgeClass;
                                let declinedReasonHtml = '';
                                switch (status) {
                                    case 'Approved':
                                        badgeClass = 'bg-success';
                                        break;
                                    case 'Declined':
                                        badgeClass = 'bg-danger';
                                        if (declinedReason) {
                                            declinedReasonHtml =
                                                `<br><span><strong>Declined Reason:</strong> ${declinedReason}</span>`;
                                        }
                                        break;
                                    case 'To Review':
                                        badgeClass = 'bg-primary';
                                        break;
                                    default:
                                        badgeClass = 'bg-secondary';
                                        break;
                                }

                                const listItem = document.createElement('li');
                                listItem.className = 'list-group-item';
                                listItem.innerHTML = `
                                    <span><strong>Status:</strong> <span class="badge ${badgeClass}" style="color: white;">${status}</span>${declinedReasonHtml}</span> 
                                    <br>
                                    <span><strong>Subject Code:</strong> ${code}</span>
                                    <br>
                                    <span><strong>Subject:</strong> ${file.subject}</span>
                                    <br>
                                    <span><strong>Year:</strong> ${year}</span>
                                    <br>
                                    <span><strong>Program:</strong> ${program}</span>
                                    <br>
                                    <a href="{{ asset('storage') }}/${file.files}" target="_blank">
                                        <strong style="color:black;">File:</strong> ${file.original_file_name}
                                    </a>
                                `;
                                fileList.appendChild(listItem);
                            });
                        });
                    });
                </script>
                @include('partials.tables-footer')
</body>

</html>
