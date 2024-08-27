<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>View Accomplishment</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../../asset/vendor/bootstrap/css/bootstrap.min.css">
    <link href="../../../../asset/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../../asset/libs/css/style.css">
    <link rel="stylesheet" href="../../../../asset/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/buttons.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/select.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/fixedHeader.bootstrap4.css">
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
                            <h2 class="pageheader-title">Accomplishment</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Accomplishment</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('director.accomplishment.uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}"
                                                class="breadcrumb-link">{{ $folderName }}</a></li>
                                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">View
                                                Accomplishment</a></li>

                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                @include('partials.director-header')
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
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered second"
                                        style="width:100%">
                                        <thead>
                                            <div id="alertContainer">

                                            <tr>
                                                <th>No.</th>
                                                <th>Date & Time</th>
                                                <th>Employee Name</th>
                                                <th>Code & Subject</th>
                                                <th>Year & Program</th>
                                                <th>File</th>
                                                <th>Status</th>
                                              
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($groupedFiles as $semester => $files)
                                                @foreach ($files as $index => $file)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $file->created_at->timezone('Asia/Manila')->format('F j, Y, g:i A') }}
                                                        </td>
                                                        <td>{{ $file->user_name }}</td>
                                                        <td>
                                                            <div
                                                                style="display: flex; flex-direction: column; margin-bottom: 20px;">
                                                                {{ $file->code }}
                                                                <span
                                                                    style="margin-top: 5px;">{{ $file->subject_name ?? 'N/A' }}</span>
                                                            </div>
                                                        </td>

                                                        <td>{{ $file->year ?? 'N/A' }} -
                                                            <span>{{ $file->program ?? 'N/A' }}</span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ Storage::url('/' . $file->files) }}"
                                                                target="_blank">{{ $file->original_file_name }}</a>
                                                        </td>
                                                        <td>
                                                            @if ($file->status === 'To Review')
                                                                <span
                                                                    class="badge badge-primary">{{ $file->status }}</span>
                                                            @elseif ($file->status === 'Declined')
                                                                <span
                                                                    class="badge badge-danger">{{ $file->status }}</span>
                                                                <br>
                                                                <span>Declined Reason:
                                                                    {{ $file->declined_reason }}</span>
                                                            @elseif ($file->status === 'Approved')
                                                                <span
                                                                    class="badge badge-success">{{ $file->status }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                    <!-- ============================================================== -->
                    <!-- end data table  -->
                    <!-- ============================================================== -->
                </div>
           

                @include('partials.tables-footer')
