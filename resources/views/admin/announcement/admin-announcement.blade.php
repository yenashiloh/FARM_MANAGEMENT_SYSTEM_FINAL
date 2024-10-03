<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    @include('partials.admin-header')


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
                            <h2 class="pageheader-title">Announcement</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Maintenance</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.announcement.admin-announcement') }}"
                                                class="breadcrumb-link">Announcement</a></li>
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
                        <div class="container">
                            <div class="dropdown-container">
                                <a href="{{ route('admin.announcement.add-announcement') }}"
                                    class="btn btn-primary mb-4"><i class="fas fa-plus fs-6"></i> Create
                                    Announcement</a>
                            </div>
                            @if (session('success'))
                                <div id="success-message"
                                    class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                    {{ session('success') }}

                                </div>
                            @endif

                            <div class="container">
                                <div class="row justify-content-end">
                                    <div class="col-md-4 col-sm-6 col-auto mb-3">
                                        <input type="text" id="search" class="form-control" placeholder="Search announcements..." />
                                    </div>
                                </div>
                            </div>
                            
                            

                            <div id="announcements-list">
                                @if ($announcements->isEmpty())
                                    <div class="alert alert-info" role="alert">
                                        No announcements available.
                                    </div>
                                @else
                                    @foreach ($announcements as $announcement)
                                        <div class="main-content container-fluid p-0 mb-4 announcement-item">
                                            <div class="email-head d-flex justify-content-between align-items-center">
                                                <div class="email-head-subject d-flex align-items-center">
                                                    <div class="title">
                                                        <a class="active" href="#"></a>
                                                        <span
                                                            style="font-size: 20px; mt-0">{{ $announcement->subject }}</span>
                                                        <div class="date" style="font-size:12px;">
                                                            {{ \Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:i a') }}
                                                        </div>
                                                        <div class="date mt-2" style="font-size:12px;">
                                                            To: @foreach ($announcement->displayEmails as $email)
                                                                {{ $email }}@if (!$loop->last)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                            @if ($announcement->moreEmailsCount > 0)
                                                                and {{ $announcement->moreEmailsCount }} more
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="me-3">
                                                        @if ($announcement->published)
                                                            <span class="badge badge-success">Published</span>
                                                        @else
                                                            <span class="badge badge-warning">Unpublished</span>
                                                        @endif
                                                    </span>
                                                    <div class="dropdown">
                                                        <i class="fas fa-ellipsis-h dropdown-trigger mr-4 ml-3"
                                                            onclick="toggleDropdown({{ $announcement->id_announcement }})"></i>
                                                        <div class="dropdown-menu toggle-dropdown"
                                                            id="dropdownMenu{{ $announcement->id_announcement }}">
                                                            <a href="{{ route('admin.announcement.edit-announcement', $announcement->id_announcement) }}"
                                                                class="dropdown-item">Edit</a>
                                                            <button type="button" class="dropdown-item delete-btn"
                                                                data-id="{{ $announcement->id_announcement }}">Delete</button>
                                                            @if ($announcement->published)
                                                                <a href="{{ route('admin.announcement.unpublish-announcement', $announcement->id_announcement) }}"
                                                                    class="dropdown-item">Unpublish</a>
                                                            @else
                                                                <a href="{{ route('admin.announcement.publish-announcement', $announcement->id_announcement) }}"
                                                                    class="dropdown-item">Publish</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="email-body" style="margin-bottom:20px;">
                                                <p style="font-size: 18px;">{!! $announcement->message !!}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>



                            <!-- ============================================================== -->
                            <!-- end wrapper  -->
                            <!-- ============================================================== -->
                        </div>
                        <!-- ============================================================== -->
                        <!-- end main wrapper  -->
                        <!-- ============================================================== -->

                        @include('partials.admin-footer')
                        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                        <script>
                            //dropdown toggle
                            function toggleDropdown(id) {
                                var dropdownMenu = document.getElementById('dropdownMenu' + id);
                                var rect = dropdownMenu.getBoundingClientRect();

                                if (rect.right > window.innerWidth) {
                                    dropdownMenu.style.left = 'auto';
                                    dropdownMenu.style.right = '0';
                                } else {
                                    dropdownMenu.style.left = 'auto';
                                    dropdownMenu.style.right = 'initial';
                                }

                                dropdownMenu.classList.toggle('show');
                            }

                            //delete Sweet Alert
                            document.addEventListener('DOMContentLoaded', function() {
                                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                                document.querySelectorAll('.delete-btn').forEach(button => {
                                    button.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const id = this.getAttribute('data-id');
                                        const url = `/admin/announcement/delete/${id}`;

                                        Swal.fire({
                                            title: 'Are you sure?',
                                            text: "You won't be able to revert this!",
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Yes, delete it!'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                fetch(url, {
                                                        method: 'DELETE',
                                                        headers: {
                                                            'X-CSRF-TOKEN': csrfToken,
                                                            'Content-Type': 'application/json',
                                                        },
                                                    })
                                                    .then(response => {
                                                        if (response.ok) {
                                                            Swal.fire(
                                                                'Deleted!',
                                                                'Your announcement has been deleted.',
                                                                'success'
                                                            ).then(() => {
                                                                location.reload();
                                                            });
                                                        } else {
                                                            throw new Error(
                                                                'Error deleting the announcement');
                                                        }
                                                    })
                                                    .catch(error => {
                                                        Swal.fire(
                                                            'Error!',
                                                            'There was an error deleting the announcement.',
                                                            'error'
                                                        );
                                                    });
                                            }
                                        });
                                    });
                                });
                            });

                            //success message
                            document.addEventListener('DOMContentLoaded', function() {
                                if (document.getElementById('success-message')) {
                                    setTimeout(function() {
                                        $('#success-message').alert('close');
                                    }, 8000);
                                }
                            });

                            $(document).ready(function() {
                                $('#search').on('input', function() {
                                    var query = $(this).val();
                                    $.ajax({
                                        url: "{{ route('admin.announcement.admin-announcement') }}",
                                        method: 'GET',
                                        data: {
                                            query: query
                                        },
                                        success: function(response) {
                                            $('#announcements-list').html($(response).find('#announcements-list')
                                                .html());
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('AJAX Error: ' + status + error);
                                        }
                                    });
                                });
                            });
                        </script>
</body>

</html>
