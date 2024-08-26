<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    @include('partials.faculty-header')


    <title>Announcement</title>
    <style>
        .toggle-dropdown {
            position: absolute;
            margin-left: 20px;
            top: 100%;
            transform: translateX(-100%);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: white;
            border-radius: 4px;
            z-index: 1000;
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
                            <h2 class="pageheader-title">Announcement</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Menu</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('faculty.announcement') }}"
                                                class="breadcrumb-link">Announcement</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- Page Header -->
                <!-- ============================================================== -->
                <div class="ecommerce-widget">
                    <div class="container">
                        @if ($announcements->isEmpty())
                            <div class="alert alert-info" role="alert">
                                No announcements available.
                            </div>
                        @else
                            <div class="row">
                                @foreach ($announcements as $announcement)
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title" style="font-size: 20px;">
                                                    {{ $announcement->subject }}
                                                </h5>
                                                <h6 class="card-subtitle text-muted" style="font-size:12px;">
                                                    {{ \Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:i a') }}
                                                </h6>
                                                <p class="card-subtitle text-muted mt-2" style="font-size:12px;">
                                                    To: 
                                                    @foreach ($announcement->displayEmails as $email)
                                                        {{ $email }}@if (!$loop->last), @endif
                                                    @endforeach
                                                    @if ($announcement->moreEmailsCount > 0)
                                                        and {{ $announcement->moreEmailsCount }} more
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">{!! $announcement->message !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- ============================================================== -->
                <!-- End Page Header -->
                <!-- ============================================================== -->


                @include('partials.faculty-footer')
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>


</body>

</html>
