<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    @include('partials.faculty-header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>Dashboard</title>
    <style>
        .icon {
            font-size: 30px;
            color: #800000;
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
                            <h2 class="pageheader-title"> Dashboard</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                >Menu</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('faculty.faculty-dashboard') }}"
                                                class="breadcrumb-link" style=" color: #3d405c;">Dashboard</a></li>
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
                    <div class="row">
                        <!-- Total Faculty Card -->
                        <!-- Completed Reviews Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-inline-block">
                                        <h5 class="text-muted">Total of Approved</h5>
                                        <h2 class="mb-0">{{ $approvedCount }}</h2>
                                    </div>
                                    <div class="float-right icon-circle-medium icon-box-lg bg-success-light mt-1">
                                        <i class="fa fa-check-circle fa-fw fa-sm text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total of Pending Reviews Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-inline-block">
                                        <h5 class="text-muted">Total of Pending Review</h5>
                                        <h2 class="mb-0">{{ $toReviewCount }}</h2>
                                    </div>
                                    <div class="float-right icon-circle-medium icon-box-lg bg-primary-light mt-1">
                                        <i class="fa fa-tasks fa-fw fa-sm text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Storage Card -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-inline-block">
                                        <h5 class="text-muted">Storage Used</h5>
                                        <h2 class="mb-0"> {{ $formattedTotalStorageUsed }}</h2>
                                    </div>
                                    <div class="float-right icon-circle-medium icon-box-lg bg-brand-light mt-1">
                                        <i class="fa fa-database fa-fw fa-sm text-brand"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bar Chart Section -->
                    <div class="row">
                        <div class="col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Submitted Status per Folder</h5>
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <canvas id="statusBarChart" style="width: 100%; height: 100px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Storage Usage</h5>
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <canvas id="storageChart" style="width: 100%;  height: 20px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Passed Files Percentage per Main Requirements</h5>
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <canvas id="folderBarChart" style="width: 100%; "></canvas>
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

        @include('partials.faculty-footer')
        <script>
            //status per folder
            function initializeStatusBarChart() {
                const ctx = document.getElementById('statusBarChart');
                if (!ctx) {
                    console.error('statusBarChart canvas not found');
                    return;
                }
                const chartData = @json($chartData);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.map(data => data.folder_name),
                        datasets: [{
                                label: 'Approved',
                                data: chartData.map(data => data.approved),
                                backgroundColor: 'rgba(144, 238, 144, 0.5)',
                            },
                            {
                                label: 'Declined',
                                data: chartData.map(data => data.declined),
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                            },
                            {
                                label: 'To Review',
                                data: chartData.map(data => data.to_review),
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true,
                            },
                            y: {
                                stacked: true,
                            }
                        }
                    }
                });
            }
            //storage
            function initializeStorageChart() {
                var ctx = document.getElementById('storageChart');
                if (!ctx) {
                    console.error('storageChart canvas not found');
                    return;
                }
                var storageChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Used Storage ({{ $formattedTotalStorageUsed }})',
                            'Available Storage ({{ $formattedStorageAvailable }})'
                        ],
                        datasets: [{
                            data: [{{ $totalStorageUsed }}, {{ $storageAvailable }}],
                            backgroundColor: ['#FF6384', '#36A2EB'],
                            hoverBackgroundColor: ['#FF6384', '#36A2EB']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '50%',
                        plugins: {
                            title: {
                                display: true,
                                text: 'Storage Usage'
                            }
                        }
                    }
                });
            }

            //generate random color
            function generateRandomLightColor() {
                const hue = Math.floor(Math.random() * 360);
                const saturation = 60 + Math.floor(Math.random() * 20);
                const lightness = 75 + Math.floor(Math.random() * 10);

                return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
            }

            //percentage per main requirements
            function initializeFolderChart() {
                const folderCtx = document.getElementById('folderBarChart');
                if (!folderCtx) {
                    console.error('folderBarChart canvas not found');
                    return;
                }

                const folderChartData = @json($folderChartData);
                const datasets = [];
                const labels = folderChartData.map(folder => folder.name);

                const subfolderColors = {};

                folderChartData.forEach((mainFolder, folderIndex) => {
                    mainFolder.subfolders.forEach((subfolder, subfolderIndex) => {
                        if (!subfolderColors[subfolder.name]) {
                            subfolderColors[subfolder.name] = generateRandomLightColor();
                        }

                        datasets.push({
                            label: `${mainFolder.name} - ${subfolder.name}`,
                            data: folderChartData.map(folder => {
                                const subfolderData = folder.subfolders.find(sub => sub.name ===
                                    subfolder.name);
                                return subfolderData ? subfolderData.percentage : 0;
                            }),
                            backgroundColor: subfolderColors[subfolder.name],
                            borderColor: 'rgba(0, 0, 0, 0.1)',
                            borderWidth: 1
                        });
                    });
                });

                new Chart(folderCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: false,
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Percentage (%)'
                                }
                            },
                            y: {
                                stacked: false,
                                title: {
                                    display: true,
                                    text: 'Main Folders'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const mainFolder = folderChartData[context.dataIndex];
                                        const subfolder = mainFolder.subfolders.find(sub => sub.name === context
                                            .dataset.label.split(' - ')[1]);
                                        if (subfolder) {
                                            return `${subfolder.name}: ${subfolder.percentage.toFixed(2)}% (${subfolder.user_files_count}/${subfolder.total_files_count}) - ${subfolder.academic_year}`;
                                        }
                                        return `${context.dataset.label}: 0%`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function initializeAllCharts() {
                initializeStatusBarChart();
                initializeStorageChart();
                initializeFolderChart();
            }

            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initializeAllCharts, 100);
            });
        </script>
</body>

</html>
