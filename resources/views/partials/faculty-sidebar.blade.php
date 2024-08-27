 <!-- ============================================================== -->
 <!-- main wrapper -->
 <!-- ============================================================== -->
 <div class="dashboard-main-wrapper">
     <!-- ============================================================== -->
     <!-- navbar -->
     <!-- ============================================================== -->
     <div class="dashboard-header">
         <nav class="navbar navbar-expand-lg bg-white fixed-top">
             <a class="navbar-brand" href="">
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
                         <a class="nav-link nav-icons"
                             @if ($folder) href="{{ route('faculty.accomplishment.uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}"
                     @else
                         href="#" @endif
                             id="navbarDropdownMenuLink1" data-toggle="dropdown" aria-haspopup="true"
                             aria-expanded="false">
                             <i class="fas fa-fw fa-bell"></i>
                             <span class="indicator" id="notification-count" style="display: none;">0</span>
                         </a>
                         <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                             <li>
                                 <div class="notification-title">Notification</div>
                                 <div class="notification-list">
                                     <div class="list-group">
                                         @foreach ($notifications as $notification)
                                             <a href="{{ route('faculty.accomplishment.uploaded-files', ['folder_name_id' => $notification->folder_name_id]) }}"
                                                 class="list-group-item list-group-item-action {{ $loop->first ? 'active' : '' }}">
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
                                     </div>
                                 </div>
                             </li>
                         </ul>
                     </li>

                     <li class="nav-item dropdown nav-user">
                         <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2"
                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                 class="fas fa-user-circle user-avatar-md rounded-circle" style="font-size:25px;"></i>

                             <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                                 aria-labelledby="navbarDropdownMenuLink2">
                                 <div class="nav-user-info text-center">
                                     <h5 class="mb-0 text-white nav-user-name">
                                         {{ $firstName }} {{ $lastName }}
                                     </h5>
                                     <span style="font-size:12px;">
                                         Faculty Member
                                     </span>
                                 </div>
                                 {{-- <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>Account</a>
                                 <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Setting</a> --}}
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
                 <div class="collapse navbar-collapse" id="navbarNav">
                     <ul class="navbar-nav flex-column">
                         <li class="nav-divider">
                             MENU
                         </li>
                         <li class="nav-item">
                             <a class="nav-link {{ request()->routeIs('faculty.faculty-dashboard') ? 'active' : '' }}"
                                 href="{{ route('faculty.faculty-dashboard') }}" aria-expanded="false"
                                 data-target="#submenu-1" aria-controls="submenu-1"><i
                                     class="fas fa-tachometer-alt"></i>
                                 Dashboard <span class="badge badge-success">6</span></a>
                         </li>
                         <li class="nav-item">
                             <a class="nav-link {{ request()->routeIs('faculty.announcement') ? 'active' : '' }}"
                                 href="{{ route('faculty.announcement') }}" aria-expanded="false"
                                 data-target="#submenu-1" aria-controls="submenu-1"><i class="fas fa-bullhorn"></i>
                                 Announcements</a>
                         </li>
                         <li class="nav-divider">
                             Accomplishment
                         </li>
                         <li class="nav-item">
                             <a class="nav-link {{ request()->routeIs('faculty.accomplishment.uploaded-files') && request()->route('folder_name_id') && in_array(request()->route('folder_name_id'), $folders->where('main_folder_name', 'Classroom Management')->pluck('folder_name_id')->toArray()) ? 'active' : '' }}"
                                 href="#" data-toggle="collapse" aria-expanded="false" data-target="#submenu-6"
                                 aria-controls="submenu-6">
                                 <i class="fas fa-book"></i> Classroom Management
                             </a>
                             <div id="submenu-6" class="collapse submenu">
                                 <ul class="nav flex-column">
                                     @foreach ($folders as $folder)
                                         @if ($folder->main_folder_name == 'Classroom Management')
                                             <li class="nav-item">
                                                 <a class="nav-link {{ request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                     href="{{ route('faculty.accomplishment.uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}">
                                                     {{ $folder->folder_name }}
                                                 </a>
                                             </li>
                                         @endif
                                     @endforeach
                                 </ul>
                             </div>
                         </li>

                         <li class="nav-item">
                             <a class="nav-link {{ request()->route('folder_name_id') && in_array(request()->route('folder_name_id'), $folders->where('main_folder_name', 'Test Administration')->pluck('folder_name_id')->toArray()) ? 'active' : '' }}"
                                 href="#" data-toggle="collapse" aria-expanded="false"
                                 data-target="#submenu-2" aria-controls="submenu-2">
                                 <i class="fas fa-clipboard-list"></i> Test Administration
                             </a>
                             <div id="submenu-2" class="collapse submenu">
                                 <ul class="nav flex-column">
                                     @foreach ($folders as $folder)
                                         @if ($folder->main_folder_name == 'Test Administration')
                                             <li class="nav-item">
                                                 <a class="nav-link {{ request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                     href="{{ route('faculty.accomplishment.uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}">
                                                     {{ $folder->folder_name }}
                                                 </a>
                                             </li>
                                         @endif
                                     @endforeach
                                 </ul>
                             </div>
                         </li>

                         <li class="nav-item">
                             <a class="nav-link {{ request()->route('folder_name_id') && in_array(request()->route('folder_name_id'), $folders->where('main_folder_name', 'Syllabus Preparation')->pluck('folder_name_id')->toArray()) ? 'active' : '' }}"
                                 href="#" data-toggle="collapse" aria-expanded="false"
                                 data-target="#submenu-3" aria-controls="submenu-3">
                                 <i class="fas fa-clipboard-list"></i> Syllabus Preparation
                             </a>
                             <div id="submenu-3" class="collapse submenu">
                                 <ul class="nav flex-column">
                                     @foreach ($folders as $folder)
                                         @if ($folder->main_folder_name == 'Syllabus Preparation')
                                             <li class="nav-item">
                                                 <a class="nav-link {{ request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                     href="{{ route('faculty.accomplishment.uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}">
                                                     {{ $folder->folder_name }}
                                                 </a>
                                             </li>
                                         @endif
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
     document.getElementById('logout-link').addEventListener('click', function(e) {
         e.preventDefault();

         Swal.fire({
             title: 'Logout',
             text: "Are you sure you want to logout?",
             icon: 'question',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Yes, logout!'
         }).then((result) => {
             if (result.isConfirmed) {
                 fetch('{{ route('logout') }}', {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
                         },
                         credentials: 'same-origin'
                     })
                     .then(response => {
                         if (response.ok) {
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

     $(document).ready(function() {
         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
         });

         function updateNotificationCount() {
             $.get('{{ route('notifications.count') }}', function(data) {
                 console.log('Notification count:', data.count);
                 if (data.count > 0) {
                     $('#notification-count').text(data.count).show();
                 } else {
                     $('#notification-count').hide();
                 }
             }).fail(function(xhr, status, error) {
                 console.error('Failed to fetch notification count:', status, error);
             });
         }

         setInterval(updateNotificationCount, 30000);
         updateNotificationCount();

         $('#navbarDropdownMenuLink2').click(function(e) {
             e.preventDefault();
             e.stopPropagation();
             $('.nav-user-dropdown').toggleClass('show');
         });

         $(document).click(function(e) {
             if (!$(e.target).closest('.nav-user').length) {
                 $('.nav-user-dropdown').removeClass('show');
             }
         });

         $('.nav-user-dropdown').click(function(e) {
             e.stopPropagation();
         });

         $(document).on('click', '.list-group-item', function(e) {
             e.preventDefault();
             var $this = $(this);
             var notificationId = $this.data('notification-id');

             $.post('{{ route('notifications.mark-read') }}', {
                 notification_id: notificationId
             }, function() {
                 $this.removeClass('new-notification');
                 updateNotificationCount();
             }).fail(function() {
                 console.error('Failed to mark notification as read.');
             });

             window.location.href = $this.attr('href');
         });

         function updateNotifications() {
             $.get('{{ route('notifications.get') }}', function(data) {
                 var $notificationList = $('.notification-list .list-group');
                 $notificationList.empty();

                 if (data.notifications.length > 0) {
                     data.notifications.forEach(function(notification) {
                         var $notification = $('<a>')
                             .attr('href', notification.url)
                             .attr('data-notification-id', notification.id)
                             .addClass('list-group-item list-group-item-action')
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

                         if (!notification.is_read) {
                             $notification.addClass('new-notification');
                         }

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

         setInterval(updateNotifications, 30000);
         updateNotifications();
     });
 </script>
