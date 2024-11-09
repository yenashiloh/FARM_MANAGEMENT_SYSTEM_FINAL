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
                                        <li class="breadcrumb-item"><a href="#!"
                                                class="breadcrumb-link">Accomplishment</a></li>
                                        <li class="breadcrumb-item"><a href="" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">{{ $folderName }}</a></li>
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
                                <h5 class="mb-0">{{ $folderName }} (an academic document that communicates
                                    information about a specific course and explains the rules, responsibilities, and
                                    expectations associated with it.)</h5>
                                <form method="GET"
                                    action="{{ route('director.accomplishment.show', ['folder_name_id' => $folder_name_id]) }}">
                                    <div class="form-group row align-items-center">
                                        <label for="semester" class="col-form-label ml-3">Select Semester:</label>
                                        <div class="col-sm-3 position-relative">
                                            <select name="semester" id="semester" class="form-control"
                                                onchange="this.form.submit()">
                                                <option value="">All Semesters</option>
                                                @foreach ($allSemesters as $semester)
                                                    <option value="{{ $semester }}"
                                                        {{ request('semester') == $semester ? 'selected' : '' }}>
                                                        {{ $semester }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <i class="fas fa-chevron-down position-absolute"
                                                style="top: 50%; right: 20px; transform: translateY(-50%); pointer-events: none;"></i>
                                        </div>
                                    </div>
                                </form>


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
                                        @php $rowNumber = 1; @endphp
                                        @foreach ($groupedFiles as $file)
                                            <tr>
                                                <td>{{ $rowNumber++ }}</td>
                                                <td>{{ $file['created_at']->timezone('Asia/Manila')->format('F j, Y, g:i A') }}
                                                </td>
                                                <td>{{ $file['user_name'] }}</td>
                                                <td>{{ $file['semester'] }}</td>
                                                <td>
                                                    <a href="{{ route('director.accomplishment.view-accomplishment', ['user_login_id' => $file['user_login_id'], 'folder_name_id' => $folder->folder_name_id, 'semester' => $file['semester']]) }}"
                                                        class="btn btn-info text-white">
                                                        View Files
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
            </div>
        </div>
    </div>

    <script></script>
    @include('partials.tables-footer')
</body>

</html>
