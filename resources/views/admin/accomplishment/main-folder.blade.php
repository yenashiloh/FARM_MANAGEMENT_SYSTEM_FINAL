@include('partials.tables-header')
<title>Main Requirements</title>
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
                            <h2 class="pageheader-title">Main Requirements </h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Accomplishment</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.accomplishment.accomplishment') }}"
                                                class="breadcrumb-link">Department</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('viewAccomplishmentDepartment', ['department' => urlencode($department)]) }}"
                                                class="breadcrumb-link">Faculty</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('faculty.accomplishments', ['user_login_id' => $faculty->user_login_id]) }}"
                                                class="breadcrumb-link">Main Requirements</a></li>
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
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered first">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Main Folder Name</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($folders as $index => $folder)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $folder->main_folder_name }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.accomplishment.viewFolderNames', ['user_login_id' => $faculty->user_login_id, 'main_folder_name' => $folder->main_folder_name]) }}"
                                                            class="btn btn-info text-white">
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
                </div>

                @include('partials.tables-footer')
</body>

</html>
