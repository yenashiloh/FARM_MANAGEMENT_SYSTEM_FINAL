<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    @include('partials.admin-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard</title>
    <style>
        .icon {
            font-size: 30px;
            color: #800000;
        }

        .small-chart {
            width: 50vw;
            height: 50vw;
            max-width: 400px;
            max-height: 400px;
            display: block;
        }
    </style>
</head>

<body>
    @include('partials.admin-sidebar')
    <div id="loading-spinner" class="loading-spinner">
        <div class="spinner"></div>
    </div>
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content">
                <!-- pageheader -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <div class="col d-flex justify-content-between align-items-center" style="padding: 0">
                                <h2 class="pageheader-title mb-0">Admin Dashboard</h2>
                                <div class="ml-auto text-right">
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            Generate Reports
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @foreach ($semesters as $semester)
                                                <a class="dropdown-item"
                                                    href="{{ route('generate.all.reports', ['semester' => $semester->semester]) }}">
                                                    {{ $semester->semester }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                               >Menu</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('admin.admin-dashboard') }}"
                                            style="cursor: default; color: #3d405c;" class="breadcrumb-link">Dashboard</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end pageheader -->

                <div class="row">
                    <!-- Total of Faculty Users -->
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Total of Faculty Users</h5>
                                    <h2 class="mb-0"> {{ $facultyCount }}</h2>
                                </div>
                                <div class="float-right icon-circle-medium  icon-box-lg  bg-info-light mt-1">
                                    <i class="fa fa-users fa-fw fa-sm text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- end total views   -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- total followers   -->
                    <!-- ============================================================== -->
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Total of Files Submitted</h5>
                                    <h2 class="mb-0">{{ $filesCount }}</h2>
                                </div>
                                <div class="float-right icon-circle-medium  icon-box-lg  bg-secondary-light mt-1">
                                    <i class="fa fa-file fa-fw fa-sm text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Total of Pending Review</h5>
                                    <h2 class="mb-0">{{ $toReviewCount }}</h2>
                                </div>
                                <div class="float-right icon-circle-medium  icon-box-lg  bg-primary-light mt-1">
                                    <i class="fa fa-tasks fa-fw fa-sm text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-inline-block">
                                    <h5 class="text-muted">Completed Reviews</h5>
                                    <h2 class="mb-0">{{ $completedReviewsCount }}</h2>
                                </div>
                                <div class="float-right icon-circle-medium  icon-box-lg  bg-success-light mt-1">
                                    <i class="fa fa-check-circle fa-fw fa-sm text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Rates -->
                    <div class="col-12">
                        <div class="card">
                            <h5 class="card-header">Status Rates</h5>
                            <div class="card-body">
                                <div class="d-flex justify-content-center">
                                    <canvas id="statusPieChart" class="small-chart"
                                        style="width: 50%; height: 200px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submitted Files per Requirement -->
                    <div class="col-12">
                        <div class="card">
                            <h5 class="card-header">Submitted Files per Requirement</h5>
                            <div class="card-body">
                                <div class="d-flex justify-content-center">
                                    <canvas id="filesBarChart" style="width: 100%; height: 428px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submitted Status per Requirement -->
                    <div class="col-12">
                        <div class="card">
                            <h5 class="card-header">Status Status per Requirement</h5>
                            <div class="card-body">
                                <div class="d-flex justify-content-center">
                                    <canvas id="statusChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper  -->
    <!-- ============================================================== -->

    @include('partials.admin-footer')
    <script>
        //pie chart - status rates
        var ctx = document.getElementById('statusPieChart').getContext('2d');
        var statusPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['To Review', 'Approved', 'Declined'],
                datasets: [{
                    label: 'File Status Distribution',
                    data: [
                        {{ $toReviewCount }},
                        {{ $approvedCount }},
                        {{ $declinedCount }}
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(144, 238, 144, 0.5)',
                        'rgba(255, 99, 132, 0.5)'
                    ],
                    borderColor: [
                        'rgba(41, 121, 255, 1)',
                        'rgba(100, 200, 100, 1)',
                        'rgba(255, 77, 77, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        //bar chart - submitted files per folder
        var ctxBar = document.getElementById('filesBarChart').getContext('2d');
        var filesBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($folderCounts as $folder)
                        "{{ $folder->folder_name }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'Number of Files',
                    data: [
                        @foreach ($folderCounts as $folder)
                            {{ $folder->courses_files_count }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        //stacked by chart - submitted status per folder
        var ctx = document.getElementById('statusChart').getContext('2d');
        var chartData = @json($chartData);

        var labels = chartData.map(data => data.folder_name);
        var toReviewCounts = chartData.map(data => data.to_review_count);
        var approvedCounts = chartData.map(data => data.approved_count);
        var declinedCounts = chartData.map(data => data.declined_count);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'To Review',
                        data: toReviewCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    },
                    {
                        label: 'Approved',
                        data: approvedCounts,
                        backgroundColor: 'rgba(144, 238, 144, 0.5)',
                    },
                    {
                        label: 'Declined',
                        data: declinedCounts,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });
    </script>
</body>

</html>
