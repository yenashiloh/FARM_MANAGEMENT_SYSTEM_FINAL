@include('partials.tables-header')
<title>Completed Reviews </title>
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
                            <h2 class="pageheader-title">Completed Reviews</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                         <li class="breadcrumb-item">
                                             <a href=""
                                                class="breadcrumb-link">Menu</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                             <a href="{{route ('admin.admin-dashboard')}}"
                                                class="breadcrumb-link" ">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{route ('admin.dashboard-totals.completed-reviews')}}"
                                                class="breadcrumb-link" style=" color: #3d405c;">Completed Reviews</a>
                                        </li>
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
                                                <th>Faculty Name</th>
                                                <th>Subject</th>
                                                <th>Created Date</th>
                                                <th>Documents</th>
                                                <th>Files</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($processedFiles as $index => $file)
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
                                                                    // If files is a JSON string, decode it
                                                                    if (is_string($file['files'])) {
                                                                        $fileInfoArray = json_decode($file['files'], true);
                                                                    } elseif (is_array($file['files'])) {
                                                                        $fileInfoArray = $file['files'];
                                                                    }
                                                                    
                                                                    // If it's not an array of objects but a single object, wrap it in an array
                                                                    if (isset($fileInfoArray['path'])) {
                                                                        $fileInfoArray = [$fileInfoArray];
                                                                    }
                                                                }
                                                            } catch (\Exception $e) {
                                                                // Handle any JSON decode errors
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
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No completed reviews found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.tables-footer')
</body>

</html>
