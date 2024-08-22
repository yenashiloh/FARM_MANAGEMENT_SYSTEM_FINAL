<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    @include('partials.admin-header')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard</title>
    <style>
    .icon{
        font-size: 30px;
        color: #800000;
    }
    </style>
</head>

<body>
    @include('partials.admin-sidebar')

    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Admin Dashboard</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link" style="cursor: default; color: #3d405c;">Menu</a></li>
                                        <li class="breadcrumb-item"><a href="{{route ('admin.admin-dashboard')}}" class="breadcrumb-link">Dashboard</a></li>
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
                 
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-inline-block">
                                        <h5 class="text-muted">Total of Faculty</h5>
                                        <h2 class="mb-0"> {{$facultyCount}}</h2>
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
                                        <i class="fa fa-handshake fa-fw fa-sm text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ============================================================== -->
                        <!-- end partnerships   -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- total earned   -->
                        <!-- ============================================================== -->
                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-inline-block">
                                        <h5 class="text-muted">Storage</h5>
                                        <h2 class="mb-0"> </h2>
                                    </div>
                                    <div class="float-right icon-circle-medium  icon-box-lg  bg-brand-light mt-1">
                                        <i class="fa fa-database fa-fw fa-sm text-brand"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="row">
                        <!-- ============================================================== -->

                        <!-- ============================================================== -->

                        <!-- recent orders  -->
                        <!-- ============================================================== -->
                        {{-- <div class=" col-12">
                            <div class="card">
                                <h5 class="card-header">Recent Orders</h5>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="bg-light">
                                                <tr class="border-0">
                                                    <th class="border-0">#</th>
                                                    <th class="border-0">Image</th>
                                                    <th class="border-0">Product Name</th>
                                                    <th class="border-0">Product Id</th>
                                                    <th class="border-0">Quantity</th>
                                                    <th class="border-0">Price</th>
                                                    <th class="border-0">Order Time</th>
                                                    <th class="border-0">Customer</th>
                                                    <th class="border-0">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <div class="m-r-10"><img src="asset/images/product-pic.jpg"
                                                                alt="user" class="rounded" width="45"></div>
                                                    </td>
                                                    <td>Product #1 </td>
                                                    <td>id000001 </td>
                                                    <td>20</td>
                                                    <td>$80.00</td>
                                                    <td>27-08-2018 01:22:12</td>
                                                    <td>Patricia J. King </td>
                                                    <td><span class="badge-dot badge-brand mr-1"></span>InTransit
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>
                                                        <div class="m-r-10"><img src="asset/images/product-pic-2.jpg"
                                                                alt="user" class="rounded" width="45">
                                                        </div>
                                                    </td>
                                                    <td>Product #2 </td>
                                                    <td>id000002 </td>
                                                    <td>12</td>
                                                    <td>$180.00</td>
                                                    <td>25-08-2018 21:12:56</td>
                                                    <td>Rachel J. Wicker </td>
                                                    <td><span class="badge-dot badge-success mr-1"></span>Delivered
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>
                                                        <div class="m-r-10"><img src="asset/images/product-pic-3.jpg"
                                                                alt="user" class="rounded" width="45">
                                                        </div>
                                                    </td>
                                                    <td>Product #3 </td>
                                                    <td>id000003 </td>
                                                    <td>23</td>
                                                    <td>$820.00</td>
                                                    <td>24-08-2018 14:12:77</td>
                                                    <td>Michael K. Ledford </td>
                                                    <td><span class="badge-dot badge-success mr-1"></span>Delivered
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>
                                                        <div class="m-r-10"><img src="asset/images/product-pic-4.jpg"
                                                                alt="user" class="rounded" width="45">
                                                        </div>
                                                    </td>
                                                    <td>Product #4 </td>
                                                    <td>id000004 </td>
                                                    <td>34</td>
                                                    <td>$340.00</td>
                                                    <td>23-08-2018 09:12:35</td>
                                                    <td>Michael K. Ledford </td>
                                                    <td><span class="badge-dot badge-success mr-1"></span>Delivered
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9"><a href="#"
                                                            class="btn btn-outline-light float-right">View
                                                            Details</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <!-- ============================================================== -->
                        <!-- end recent orders  -->
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-12">
                            <div class="card">
                                <h5 class="card-header">Approval Rates</h5>
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <canvas id="statusPieChart" style="width: 100%; max-width: 500px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="card">
                                <h5 class="card-header">Files per Folder</h5>
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <canvas id="filesBarChart" style="width: 100%; max-width: 500px; height:428px;"></canvas>
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

                @include('partials.admin-footer')
                <script>
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
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)'
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
                </script>
</body>

</html>
