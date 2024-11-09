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


                    <li class="nav-item dropdown nav-user">
                        <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-circle user-avatar-md rounded-circle" style="font-size:25px;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                            aria-labelledby="navbarDropdownMenuLink2">
                            <div class="nav-user-info text-center">
                                <h5 class="mb-0 text-white nav-user-name">
                                    {{ $user->first_name }} {{ $user->surname }}
                                </h5>
                                <span style="font-size:12px;">Director</span>
                            </div>
                            <a class="dropdown-item" href="{{ route('director.director-account') }}"><i
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
                <a class="d-xl-none d-lg-none" href="#">Accomplishment</a>
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
                            <a class="nav-link {{ Request::routeIs('director.director-dashboard') ? 'active' : '' }}"
                                href="{{ route('director.director-dashboard') }}" aria-expanded="false"
                                data-target="#submenu-1" aria-controls="submenu-1"><i class="fas fa-tachometer-alt"></i>
                                Dashboard <span class="badge badge-success">6</span></a>
                        <li class="nav-divider">
                            Accomplishment
                        </li>

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
                                            <a class="nav-link {{ request()->routeIs('director.department') && request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                href="{{ route('director.department', ['folder_name_id' => $folder->folder_name_id]) }}">
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
                                            <a class="nav-link {{ request()->routeIs('director.department') && request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                href="{{ route('director.department', ['folder_name_id' => $folder->folder_name_id]) }}">
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
                                            <a class="nav-link {{ request()->routeIs('director.department') && request()->route('folder_name_id') == $folder->folder_name_id ? 'active' : '' }}"
                                                href="{{ route('director.department', ['folder_name_id' => $folder->folder_name_id]) }}">
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
                fetch('{{ route('logout-director') }}', {
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
</script>
