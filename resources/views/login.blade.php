<!DOCTYPE html>
<html lang="en">
<title> Login</title>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon"> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/role.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awes+ome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" >
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href=" {{ asset('assets/fonts/feather.css') }}" >
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" >
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" >

    <style>
    .custom-button {
        background-color: #800000; 
        color: white;
        border: none; 
        padding: 10px 20px;
        border-radius: 5px; 
        cursor: pointer; 
        transition: background-color 0.3s; 
        width: 100px;
    }

    .custom-button:hover {
        background-color: #440606; 
    }
    </style>
</head>

<body>
    <div class="bg-container d-flex justify-content-center align-items-center vh-100">
        <div class="bg-image">
            <img class="bg-image" src="assets/images/PUP_1.jpeg" alt="Background Image">
        </div>
        <div class="overlay"></div>
        <div class="card-login text-center mx-auto p-4">
            <img class="logo-login mb-4 mt-4" src="../assets/images/pup-logo.png" alt="PUP Logo">
            <h2 class="text-role mb-4">PUP-T FARM SYSTEM</h2>
            <div class="btn-container">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    @if (session('error'))
                        <div class="text-center" id="errorMessage" style="color: red; font-size: 12px;">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert text-center" style="color: green; font-size: 12px;">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="form-floating d-flex justify-content-center mb-3">
                        <input type="email" name="email" class="form-control custom-width mt-3" id="floatingInput" placeholder="name@example.com" required style="width: 300px;">
                        <label for="floatingInput">Email address</label>
                    </div>
                    <div class="form-floating d-flex justify-content-center mb-3">
                        <input type="password" name="password" class="form-control custom-width" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword">Password</label>
                    </div>
                    <button type="submit" value="login" class=" mb-3 custom-button">Login</button>
                </form>
            </div>
        </div>
    </div>
@include('partials.faculty-footer')