<style>
    @media (max-width: 992px) {
        .navbar {
            padding: 0.75rem 1.5rem;
        }

        .logo {
            width: 40px;
            height: 40px;
        }

        .main-title {
            font-size: 1.2rem;
        }

        .sub-title {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 768px) {
        .navbar {
            padding: 0.5rem 1rem;
        }

        .logo {
            width: 30px;
            height: 30px;
        }

        .main-title {
            font-size: 1rem;
        }

        .sub-title {
            font-size: 0.7rem;
        }
    }

    @media (max-width: 576px) {
        .navbar {
            padding: 0.25rem 0.75rem;
        }

        .logo {
            width: 25px;
            height: 25px;
        }

        .main-title {
            font-size: 0.9rem;
        }

        .sub-title {
            font-size: 0.6rem;
        }
    }


    @media (max-width: 480px) {
        .navbar {
            padding: 0.2rem 0.5rem;
        }

        .logo {
            width: 22px;
            height: 22px;
        }

        .main-title {
            font-size: 0.8rem;
        }

        .sub-title {
            font-size: 0.5rem;
        }
    }

    @media (max-width: 360px) {
        .navbar {
            padding: 0.15rem 0.4rem;
        }

        .logo {
            width: 20px;
            height: 20px;
        }

        .main-title {
            font-size: 0.7rem;
        }

        .sub-title {
            font-size: 0.45rem;
        }
    }

    .unread-notification {
        background-color: #f0f7ff !important;
        border-left: 3px solid #0d6efd !important;
    }

    .notification-visited {
        background-color: #f0f7ff !important;
        border-left: 3px solid #0d6efd !important;
    }
</style>

<!-- ============================================================== -->
<!-- main wrapper -->
<!-- ============================================================== -->
<div class="dashboard-main-wrapper">
    <!-- ============================================================== -->
    <!-- navbar -->
    <!-- ============================================================== -->
    <div class="dashboard-header">
        <nav class="navbar navbar-expand-lg bg-white fixed-top">
            <a class="navbar-brand" href="{{ route('admin.admin-dashboard') }}">
                <img src="{{ asset('assets/images/pup-logo.png') }}" width="50" height="50" alt="Logo">
                <div class="brand-info">
                    <div class="main-title">PUP-T FARM</div>
                    <div class="sub-title">Faculty Academic Requirements Management</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse " id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto navbar-right-top">
                    <li class="nav-item dropdown notification">
                        <a class="nav-link nav-icons" href="#" id="navbarDropdownMenuLink1" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-fw fa-bell"></i>
                            <span class="indicator" id="notification-count-admin" style="display: none;">0</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                            <li>
                                <div class="notification-title">Notification</div>
                                <div class="notification-list">
                                    <div class="list-group" id="notification-items">
                                        @if ($notifications->isEmpty())
                                            <div class="text-center p-3">
                                                <span>No notifications available</span>
                                            </div>
                                        @else
                                            @foreach ($notifications as $notification)
                                                @php
                                                    $coursesFile = $notification->coursesFile;
                                                    $facultyUserLoginId = $coursesFile->user_login_id;
                                                    $semester = $coursesFile->semester;
                                                @endphp
                                                <a href="{{ route('admin.accomplishment.view-accomplishment', [
                                                    'user_login_id' => $facultyUserLoginId,
                                                    'folder_name_id' => $notification->folder_name_id,
                                                    'semester' => $semester,
                                                ]) }}"
                                                    class="list-group-item list-group-item-action {{ !$notification->is_read ? 'unread-notification' : '' }}"
                                                    data-notification-id="{{ $notification->id }}"
                                                    data-read-status="{{ $notification->is_read ? 'read' : 'unread' }}">
                                                    <div class="notification-info">
                                                        <div class="notification-list-user-img">
                                                            <i class="fas fa-user-circle user-avatar-md"
                                                                style="font-size:30px;"></i>
                                                        </div>
                                                        <div class="notification-list-user-block">
                                                            <span
                                                                class="notification-list-user-name mr-0">{{ $notification->sender }}</span>
                                                            <span>{{ $notification->notification_message }}</span>
                                                            <div class="notification-date">
                                                                {{ \Carbon\Carbon::parse($notification->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:ia') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown nav-user">
                        <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle user-avatar-md rounded-circle" style="font-size:25px;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                            aria-labelledby="navbarDropdownMenuLink2">
                            <div class="nav-user-info text-center">
                                <h5 class="mb-0 text-white nav-user-name">
                                    {{ $firstName }} {{ $surname }}
                                </h5>
                                <span style="font-size:12px;">Admin</span>
                            </div>
                            <a class="dropdown-item" href="{{ route('admin.admin-account') }}"><i
                                    class="fas fa-user mr-2"></i>Account</a>

                            <a class="dropdown-item" href="#" id="logout-link">
                                <i class="fas fa-power-off mr-2"></i>Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <!-- ============================================================== -->
    <!-- end navbar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- left sidebar -->
    <!-- ============================================================== -->
    <div class="nav-left-sidebar sidebar-dark">
        <div class="menu-list">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="sidebar-scroll">
                <div class="collapse navbar-collapse" id="navbarNav">
                
                    <ul class="navbar-nav flex-column">
                        <li class="nav-divider">
                            MENU
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ Request::routeIs('admin.admin-dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.admin-dashboard') }}" aria-expanded="false"
                                data-target="#submenu-1" aria-controls="submenu-1"><i class="fas fa-tachometer-alt"></i>
                                Dashboard </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link {{ Request::routeIs('admin.maintenance.audit-trail') ? 'active' : '' }}"
                                href="{{ route('admin.maintenance.audit-trail') }}" aria-expanded="false"
                                data-target="#submenu-1" aria-controls="submenu-1"><i class="fas fa-history"></i>
                                Audit Trail </a>
                        </li>
                        <li class="nav-item" style="position: relative !important;">
                            <a id="request-upload-access"
                                class="nav-link {{ Request::routeIs('admin.request-upload-access') ? 'active' : '' }}"
                                href="{{ route('admin.request-upload-access') }}"
                                style="padding-right: 40px !important;">
                                <i class="fas fa-folder-open"></i>
                                Request Upload Access
                            </a>
                            <span id="request-badge"
                                style="position: absolute !important; right: 10px !important; top: 50% !important; transform: translateY(-50%) !important; background-color: #ff0000 !important; color: #fff !important; border-radius: 9999px !important; padding: 2px 8px !important; font-size: 0.75rem !important; z-index: 10 !important; display: none;">
                                <span id="request-count">0</span>
                            </span>
                        </li>

                        <li class="nav-divider">
                            Maintenance
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.maintenance.create-folder') ? 'active' : '' }}"
                                href="{{ route('admin.maintenance.create-folder') }}" aria-expanded="false"
                                data-target="#submenu-3" aria-controls="submenu-3">
                                <i class="fas fa-folder"></i>Main Requirements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.announcement.admin-announcement', 'admin.announcement.edit-announcement', 'admin.announcement.add-announcement') ? 'active' : '' }}"
                                href="{{ route('admin.announcement.admin-announcement') }}" aria-expanded="false"
                                data-target="#submenu-4" aria-controls="submenu-4">
                                <i class="fas fa-bullhorn"></i>Announcements
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.maintenance.upload-schedule') ? 'active' : '' }}"
                                href="{{ route('admin.maintenance.upload-schedule') }}" aria-expanded="false"
                                data-target="#submenu-4" aria-controls="submenu-4">
                                <i class="fas fa-calendar-alt uploading-manage"></i>
                                <span class="nav-text">
                                    Schedule of Uploading
                                </span>
                            </a>
                        </li>

                        <li class="nav-divider">
                            Accomplishment
                        </li>

                        {{-- <li class="nav-item">
                            <a class="nav-link {{ str_contains(request()->path(), 'accomplishment') ? 'active' : '' }}"
                                href="{{ route('admin.accomplishment.accomplishment') }}" aria-expanded="false"
                                data-target="#submenu-4" aria-controls="submenu-4">
                                <i class="fas fa-calendar-alt uploading-manage"></i>
                                <span class="nav-text">
                                    All Accomplishment
                                </span>
                            </a>
                        </li> --}}
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->route('folder_name_id') && in_array(request()->route('folder_name_id'), $folders->where('main_folder_name', 'Classroom Management')->pluck('folder_name_id')->toArray()) ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-6"
                                aria-controls="submenu-6">
                                <i class="fas fa-book"></i> Classroom Management
                            </a>
                            <div id="submenu-6" class="collapse submenu">
                                <ul class="nav flex-column">
                                    @foreach ($folders->where('main_folder_name', 'Classroom Management')->sortBy('folder_name') as $folder)
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.accomplishment.department') && request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                href="{{ route('admin.accomplishment.department', ['folder_name_id' => $folder->folder_name_id]) }}">
                                                {{ $folder->folder_name }}
                                             </a>
                                        
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->route('folder_name_id') && in_array(request()->route('folder_name_id'), $folders->where('main_folder_name', 'Test Administration')->pluck('folder_name_id')->toArray()) ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-2"
                                aria-controls="submenu-2">
                                <i class="fas fa-clipboard-list"></i> Test Administration
                            </a>
                            <div id="submenu-2" class="collapse submenu">
                                <ul class="nav flex-column">
                                    @foreach ($folders->where('main_folder_name', 'Test Administration')->sortBy('folder_name') as $folder)
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.accomplishment.department') && request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                href="{{ route('admin.accomplishment.department', ['folder_name_id' => $folder->folder_name_id]) }}">
                                                {{ $folder->folder_name }}
                                             </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->route('folder_name_id') && in_array(request()->route('folder_name_id'), $folders->where('main_folder_name', 'Syllabus Preparation')->pluck('folder_name_id')->toArray()) ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-3"
                                aria-controls="submenu-3">
                                <i class="fas fa-file-alt"></i> Syllabus Preparation
                            </a>
                            <div id="submenu-3" class="collapse submenu">
                                <ul class="nav flex-column">
                                    @foreach ($folders->where('main_folder_name', 'Syllabus Preparation')->sortBy('folder_name') as $folder)
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.accomplishment.department') && request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                href="{{ route('admin.accomplishment.department', ['folder_name_id' => $folder->folder_name_id]) }}">
                                                {{ $folder->folder_name }}
                                             </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>

                    </ul>
                </div>
                </li>

                </ul>
        </div>
        </nav>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    //logout
    document.getElementById('logout-link').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Logout',
            text: "Are you sure you want to logout?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, log me out!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route('admin-logout') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            '_token': '{{ csrf_token() }}'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route('login') }}';
                        } else {
                            Swal.fire(
                                'Error!',
                                'Logout failed.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error!',
                            'An unexpected error occurred.',
                            'error'
                        );
                        console.error('Error:', error);
                    });
            }
        });
    });

    // notification
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const visitedNotifications = new Set(
            JSON.parse(sessionStorage.getItem('adminVisitedNotifications') || '[]')
        );

        function updateNotificationCountDisplay(count) {
            var $countElement = $('#notification-count-admin');
            if (count > 0) {
                $countElement.text(count).show();
            } else {
                $countElement.hide();
            }
        }

        function updateNotificationCount() {
            $.get('{{ route('admin.notifications.count') }}', function(data) {
                updateNotificationCountDisplay(data.count);
            }).fail(function() {
                console.error('Failed to fetch notification count.');
            });
        }

        $('#navbarDropdownMenuLink1').click(function(e) {
            e.preventDefault();
            e.stopPropagation();

            $.post('{{ route('admin.notifications.markAllAsRead') }}', function() {
                $('#notification-count-admin').hide();

                $('.list-group-item').each(function() {
                    const notificationId = $(this).data('notification-id');
                    if ($(this).data('read-status') === 'unread') {
                        $(this).addClass('notification-visited');
                        visitedNotifications.add(notificationId);
                    }
                });

                sessionStorage.setItem('adminVisitedNotifications',
                    JSON.stringify(Array.from(visitedNotifications)));

            }).fail(function() {
                console.error('Failed to mark notifications as read.');
            });

            $('.notification-dropdown').toggleClass('show');
        });

        $(document).on('click', '.list-group-item', function(e) {
            e.preventDefault();
            const $this = $(this);
            const notificationId = $this.data('notification-id');

            $.post('{{ route('admin.notifications.markAsRead') }}', {
                notification_id: notificationId
            }, function() {
                if ($this.data('read-status') === 'unread') {
                    $this.addClass('notification-visited');
                    visitedNotifications.add(notificationId);
                    sessionStorage.setItem('adminVisitedNotifications',
                        JSON.stringify(Array.from(visitedNotifications)));
                }
                updateNotificationCount();
            }).fail(function() {
                console.error('Failed to mark notification as read.');
            });

            window.location.href = $this.attr('href');
        });

        function updateNotifications() {
            $.get('{{ route('admin.notifications.get') }}', function(data) {
                var $notificationList = $('#admin-notification-items');
                $notificationList.empty();

                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(function(notification) {
                        const wasVisited = visitedNotifications.has(notification.id);

                        var $notification = $('<a>')
                            .attr('href', notification.url)
                            .attr('data-notification-id', notification.id)
                            .attr('data-read-status', notification.is_read ? 'read' : 'unread')
                            .addClass('list-group-item list-group-item-action')
                            .addClass(!notification.is_read ? 'unread-notification' : '')
                            .addClass(wasVisited ? 'notification-visited' : '')
                            .html(`
                            <div class="notification-info">
                                <div class="notification-list-user-img">
                                    <i class="fas fa-user-circle user-avatar-md" style="font-size:30px;"></i>
                                </div>
                                <div class="notification-list-user-block">
                                    <span class="notification-list-user-name mr-0">${notification.sender}</span>
                                    <span>${notification.message}</span>
                                    <div class="notification-date">
                                        ${notification.created_at}
                                    </div>
                                </div>
                            </div>
                        `);

                        $notificationList.append($notification);
                    });
                } else {
                    $notificationList.html(
                        '<div class="text-center p-3">No notifications available</div>');
                }
            }).fail(function() {
                console.error('Failed to fetch notifications.');
            });
        }

        setInterval(updateNotificationCount, 30000);
        setInterval(updateNotifications, 30000);
        updateNotificationCount();
        updateNotifications();
    });

    document.addEventListener('DOMContentLoaded', () => {
        const badge = document.getElementById('request-badge');
        const countDisplay = document.getElementById('request-count');
        const requestLink = document.getElementById('request-upload-access');

        function checkNewRequests() {
            fetch('/admin/check-new-requests')
                .then(response => response.json())
                .then(data => {
                    const requestCount = data.new_requests_count;
                    countDisplay.textContent = requestCount;

                    if (requestCount > 0) {
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching new requests:', error));
        }

        function markRequestsAsRead() {
            fetch('/admin/mark-requests-as-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        badge.style.display = 'none';
                        countDisplay.textContent = '0';
                    }
                })
                .catch(error => console.error('Error marking requests as read:', error));
        }

        if (window.location.pathname.includes('request-upload-access')) {
            markRequestsAsRead();
        }

        requestLink.addEventListener('click', (event) => {
            event.preventDefault();

            markRequestsAsRead();

            setTimeout(() => {
                window.location.href = requestLink.href;
            }, 100);
        });

        setInterval(checkNewRequests, 5000);

        checkNewRequests();
    });
</script>
