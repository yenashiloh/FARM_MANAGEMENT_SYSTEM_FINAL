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
                     <li class="nav-item">
                         <div id="custom-search" class="top-search-bar">
                             <input class="form-control" type="text" placeholder="Search..">
                         </div>
                     </li>
                     <li class="nav-item dropdown notification">
                         <a class="nav-link nav-icons" href="#" id="navbarDropdownMenuLink1"
                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                 class="fas fa-fw fa-bell"></i> <span class="indicator"></span></a>
                         <ul class="dropdown-menu dropdown-menu-right notification-dropdown">
                             <li>
                                 <div class="notification-title"> Notification</div>
                                 <div class="notification-list">
                                     <div class="list-group">
                                         <a href="#" class="list-group-item list-group-item-action active">
                                             <div class="notification-info">
                                                 <div class="notification-list-user-img"><img
                                                         src="asset/images/avatar-2.jpg" alt=""
                                                         class="user-avatar-md rounded-circle"></div>
                                                 <div class="notification-list-user-block"><span
                                                         class="notification-list-user-name">Jeremy
                                                         Rakestraw</span>accepted your invitation to join the team.
                                                     <div class="notification-date">2 min ago</div>
                                                 </div>
                                             </div>
                                         </a>
                                         <a href="#" class="list-group-item list-group-item-action">
                                             <div class="notification-info">
                                                 <div class="notification-list-user-img"><img
                                                         src="asset/images/avatar-3.jpg" alt=""
                                                         class="user-avatar-md rounded-circle"></div>
                                                 <div class="notification-list-user-block"><span
                                                         class="notification-list-user-name">John Abraham </span>is
                                                     now following you
                                                     <div class="notification-date">2 days ago</div>
                                                 </div>
                                             </div>
                                         </a>
                                         <a href="#" class="list-group-item list-group-item-action">
                                             <div class="notification-info">
                                                 <div class="notification-list-user-img"><img
                                                         src="asset/images/avatar-4.jpg" alt=""
                                                         class="user-avatar-md rounded-circle"></div>
                                                 <div class="notification-list-user-block"><span
                                                         class="notification-list-user-name">Monaan Pechi</span> is
                                                     watching your main repository
                                                     <div class="notification-date">2 min ago</div>
                                                 </div>
                                             </div>
                                         </a>
                                         <a href="#" class="list-group-item list-group-item-action">
                                             <div class="notification-info">
                                                 <div class="notification-list-user-img"><img
                                                         src="asset/images/avatar-5.jpg" alt=""
                                                         class="user-avatar-md rounded-circle"></div>
                                                 <div class="notification-list-user-block"><span
                                                         class="notification-list-user-name">Jessica
                                                         Caruso</span>accepted your invitation to join the team.
                                                     <div class="notification-date">2 min ago</div>
                                                 </div>
                                             </div>
                                         </a>
                                     </div>
                                 </div>
                             </li>
                             <li>
                                 <div class="list-footer"> <a href="#">View all notifications</a></div>
                             </li>
                         </ul>
                     </li>
                     <li class="nav-item dropdown nav-user">
                         <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2"
                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-circle user-avatar-md rounded-circle" style="font-size:25px;"></i>
                            
                              
                         <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                             aria-labelledby="navbarDropdownMenuLink2">
                             <div class="nav-user-info">
                                 <h5 class="mb-0 text-white nav-user-name">John Abraham </h5>
                                 <span class="status"></span><span class="ml-2">Available</span>
                             </div>
                             <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>Account</a>
                             <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Setting</a>
                             <a class="dropdown-item" href="#" id="logout-link"><i
                                     class="fas fa-power-off mr-2"></i>Logout</a>
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
                         <li class="nav-item ">
                             <a class="nav-link {{ Request::routeIs('admin.admin-dashboard') ? 'active' : '' }}"
                                 href="{{ route('admin.admin-dashboard') }}" aria-expanded="false"
                                 data-target="#submenu-1" aria-controls="submenu-1"><i
                                     class="fas fa-tachometer-alt"></i>
                                 Dashboard <span class="badge badge-success">6</span></a>

                         <li class="nav-divider">
                             Maintenance
                         </li>
                         <li class="nav-item">
                             <a class="nav-link {{ Request::routeIs('admin.maintenance.create-folder') ? 'active' : '' }}"
                                 href="{{ route('admin.maintenance.create-folder') }}" aria-expanded="false"
                                 data-target="#submenu-3" aria-controls="submenu-3">
                                 <i class="fas fa-folder"></i> Manage Main Folder
                             </a>
                         </li>
                         <li class="nav-item ">
                             <a class="nav-link" href="{{ route('admin.announcement.admin-announcement') }}"
                                 aria-expanded="false" data-target="#submenu-4" aria-controls="submenu-4"><i
                                     class="fas fa-bullhorn"></i>Announcement</a>
                         </li>
                         <li class="nav-divider">
                             Accomplishment
                         </li>
                         <li class="nav-item">
                             <a class="nav-link {{ request()->route('folder_name_id') && in_array(request()->route('folder_name_id'), $folders->where('main_folder_name', 'Classroom Management')->pluck('folder_name_id')->toArray()) ? 'active' : '' }}"
                                 href="#" data-toggle="collapse" aria-expanded="false"
                                 data-target="#submenu-6" aria-controls="submenu-6">
                                 <i class="fas fa-book"></i> Classroom Management
                             </a>
                             <div id="submenu-6" class="collapse submenu">
                                 <ul class="nav flex-column">
                                     @foreach ($folders as $folder)
                                         @if ($folder->main_folder_name == 'Classroom Management')
                                             <li class="nav-item">
                                                 <a class="nav-link {{ request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                     href="{{ route('admin.accomplishment.admin-uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}">
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
                                                     href="{{ route('admin.accomplishment.admin-uploaded-files', ['folder_name_id' => $folder->folder_name_id]) }}">
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
 <script>
     document.getElementById('logout-link').addEventListener('click', function(e) {
         e.preventDefault();

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
                     window.location.href = '{{ route('login.form') }}';
                 } else {
                     console.error('Logout failed');
                 }
             })
             .catch(error => console.error('Error:', error));
     });
 </script>
