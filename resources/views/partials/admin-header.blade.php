{{-- 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon"> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body style="background-color: #FEF9FF;">
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('assets/images/pup-logo.png') }}" width="50" height="50" alt="Logo">
            <span class="brand-text">PUP Farm</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('accomplishment*') || Request::is('admin/accomplishments/*') ||  Request::is('admin/accomplishments/class-records*') || Request::routeIs('admin.admin-accomplishment') ? 'active' : '' }}" href="{{ route('admin.admin-accomplishment') }}">Accomplishments</a>
                </li>
                <li class="nav-item reports-item">
                    <a class="nav-link {{ Request::is('reports*') ? 'active' : '' }}" href="#" id="reportsLink">Reports</a>
                    <div class="reports-submenu-custom" id="reportsSubmenu">
                        <div class="submenu-content">
                            <h5 class="submenu-item" style="color: #F7D328; margin-top: 15px; font-size: 18px;">CONSOLIDATED QAR</h5>
                            <a class="submenu-item" href="#">My accomplishments</a>
                            <a class="submenu-item" href="" style="margin-bottom: 15px;">HAP - TG</a>
                        </div>
                    </div>
                </li>
                <li class="nav-item maintenance-item">
                    <a class="nav-link {{ Request::is('maintenance*') ? 'active' : '' }}" href="#" id="maintenanceLink">Maintenance</a>
                    <div class="maintenance-submenu-custom" id="maintenanceSubmenu">
                        <div class="submenu-content">
                            <h5 class="submenu-item" style="color: #F7D328; margin-top: 15px; font-size: 18px;">MAINTENANCE</h5>
                            <a class="submenu-item" href="{{route ('admin.maintenance.create-folder')}}">Manage Main Folder</a>
                            <a class="submenu-item" href="#">Manage Accomplishment Form</a>

                        </div>
                    </div>
                </li>
            </ul>
            <span class="navbar-text ml-auto">
                <i class="fas fa-user"></i>
                <span id="dropdown-trigger">
                    James Nabayra
                    <i class="dropdown-toggle mr-2"></i>
                </span>
                <div class="dropdown-content" id="dropdown-content">
                    <a href="#" id="logout-link">Logout</a>
                </div>
            </span>
        </div>
    </nav>

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

           //Dropdown logout
           document.addEventListener('DOMContentLoaded', function() {
            const dropdownTrigger = document.getElementById('dropdown-trigger');
            const dropdownContent = document.getElementById('dropdown-content');

            dropdownTrigger.addEventListener('click', function() {
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' :
                'block';
            });

            document.addEventListener('click', function(event) {
                if (!dropdownContent.contains(event.target) && !dropdownTrigger.contains(event.target)) {
                    dropdownContent.style.display = 'none';
                }
            });
        });
    </script> --}}

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon"> 
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../asset/vendor/bootstrap/css/bootstrap.min.css">
    <link href="../../../asset/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../asset/libs/css/style.css">
    <link rel="stylesheet" href="../../../asset/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="../../../asset/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="../../../asset/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="../../../asset/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../../asset/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="../../../asset/vendor/fonts/flag-icon-css/flag-icon.min.css">
