<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
</head>
<style>

</style>
<body style="background-color: #FEF9FF;">
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('assets/images/pup-logo.png') }}" width="50" height="50" alt="Logo">
            <span class="brand-text">PUP Farm</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('home') ? 'active' : '' }}" href="">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('accomplishment*') || Request::is('faculty/accomplishment/folders*') || Request::routeIs('faculty.faculty-accomplishment') ? 'active' : '' }}"
                        href="{{ route('faculty.faculty-accomplishment') }}">Accomplishments</a>
                </li>
                <li class="nav-item reports-item">
                    <a class="nav-link" href="#" id="reportsLink">Reports</a>
                    <div class="reports-submenu-custom" id="reportsSubmenu">
                        <div class="submenu-content">
                            <h5 class="submenu-item" style="color: #F7D328; margin-top: 15px; font-size: 18px;">
                                CONSOLIDATED QAR</h5>
                            <a class="submenu-item" href="#">My accomplishments</a>
                            <a class="submenu-item" href="" style="margin-bottom: 15px;">HAP - TG</a>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="ml-auto d-flex align-items-center">
                <li class="nav-item dropdown mr-2">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="glyphicon glyphicon-bell alertNotificacao">
                            <i class="fas fa-bell" style="color: white;"></i>
                        </span>
                        <span class='badgeAlert'>{{ $notificationCount }}</span> 
                        <span class="caret"></span>
                    </a>
                    <ul class="list-notificacao dropdown-menu">
                        @foreach($notifications as $notification)
                            <li id='item_notification_{{ $notification->id }}'>
                                <div class="media">
                                    <div class="media-left"></div>
                                    <div class="media-body">
                                        <div class='exclusaoNotificacao'></div>
                                        <h4 class="media-heading">{{ $notification->sender }}</h4>
                                        <p>{{ $notification->notification_message }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </div>
            
            
            
                <span class="navbar-text d-flex align-items-center">
                    <i class="fas fa-user" style="margin-left: 15px;"></i>
                    <span id="dropdown-trigger" class="ml-2">
                        Diana Rose
                        <i class="dropdown-toggle mr-2"></i>
                    </span>
                    <div class="dropdown-content" id="dropdown-content">
                        <a href="#" id="logout-link">Logout</a>
                    </div>
                </span>
            </div>
        </div>
    </nav>
    

    <script>
        //Logout
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

        function excluirItemNotificacao(e){
  $('#item_notification_'+e.id).remove()
}

    </script>
