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
                                                >Menu</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('faculty.announcement') }}"
                                                class="breadcrumb-link" style=" color: #3d405c;">Announcement</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- Page Header -->
                <!-- ============================================================== -->
                <div class="row">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="d-flex justify-content-end align-items-center">
                            <div class="col-md-4 col-sm-6 d-none d-md-block">
                                <input type="text" id="search" class="form-control ml-3 mb-3"
                                    placeholder="Search announcements..." />
                            </div>
                        </div>

                        <!-- Mobile search input -->
                        <div class="d-md-none mt-3">
                            <input type="text" id="search-mobile" class="form-control mb-3"
                                placeholder="Search announcements..." />
                        </div>
                        <div id="announcements-list">
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
                                                            {{ $email }}@if (!$loop->last)
                                                                ,
                                                            @endif
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

                                  <!-- Pagination -->
                                  <div class="row ">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center my-4">
                                            <div class="pagination-info">
                                                Showing {{ $announcements->firstItem() ?? 0 }} to
                                                {{ $announcements->lastItem() ?? 0 }} of {{ $announcements->total() }}
                                                announcements
                                            </div>

                                            @if ($announcements->hasPages())
                                                <nav aria-label="Announcements pagination">
                                                    <ul class="pagination mb-0">
                                                        {{-- Previous Page --}}
                                                        @if ($announcements->onFirstPage())
                                                            <li class="page-item disabled">
                                                                <span class="page-link">
                                                                    <i class="fas fa-chevron-left small"></i>
                                                                </span>
                                                            </li>
                                                        @else
                                                            <li class="page-item">
                                                                <a class="page-link"
                                                                    href="{{ $announcements->previousPageUrl() }}"
                                                                    rel="prev">
                                                                    <i class="fas fa-chevron-left small"></i>
                                                                </a>
                                                            </li>
                                                        @endif

                                                        {{-- Numbered Pages --}}
                                                        @foreach ($announcements->getUrlRange(1, $announcements->lastPage()) as $page => $url)
                                                            @if ($page == $announcements->currentPage())
                                                                <li class="page-item active">
                                                                    <span class="page-link">{{ $page }}</span>
                                                                </li>
                                                            @else
                                                                <li class="page-item">
                                                                    <a class="page-link"
                                                                        href="{{ $url }}">{{ $page }}</a>
                                                                </li>
                                                            @endif
                                                        @endforeach

                                                        {{-- Next Page --}}
                                                        @if ($announcements->hasMorePages())
                                                            <li class="page-item">
                                                                <a class="page-link"
                                                                    href="{{ $announcements->nextPageUrl() }}"
                                                                    rel="next">
                                                                    <i class="fas fa-chevron-right small"></i>
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li class="page-item disabled">
                                                                <span class="page-link">
                                                                    <i class="fas fa-chevron-right small"></i>
                                                                </span>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </nav>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Page Header -->
        <!-- ============================================================== -->


        @include('partials.faculty-footer')
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#search, #search-mobile').on('keyup', function() {
                    let query = $(this).val();

                    $.ajax({
                        url: '{{ route('faculty.announcement.search') }}',
                        method: 'GET',
                        data: {
                            search: query
                        },
                        success: function(data) {
                            $('#announcements-list').html(data);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>


</body>

</html>
