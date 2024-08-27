@include('partials.tables-header')
<title>{{ $folderName }} </title>
</head>

<body>
    @include('partials.director-sidebar')
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
                                        <li class="breadcrumb-item"><a href=""
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
                                                    <a href="{{ route('director.accomplishment.view-accomplishment', ['user_login_id' => $user_login_id, 'folder_name_id' => $folder_name_id]) }}"
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

                <script></script>
                @include('partials.tables-footer')
</body>

</html>
