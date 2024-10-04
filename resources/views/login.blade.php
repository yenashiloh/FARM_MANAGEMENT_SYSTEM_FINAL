<!DOCTYPE html>
<html lang="en">
<title>Login</title>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/role.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body, html {
        height: 100%;
        margin: 0;
        overflow: hidden;
        font-family: 'Poppins', sans-serif;
        background-color: #f7f7f7;
    }

    @keyframes fadeInPage {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }

    .bg-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: linear-gradient(135deg, #e94e77, #c62d65);
    }

    .bg-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        z-index: 1;
        filter: brightness(80%);
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(233, 114, 118, 0.6);
        z-index: 2;
    }

    .card-login {
        z-index: 3;
        max-width: 600px;
        padding: 40px;
        border-radius: 10px;
        background-color: white;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        text-align: center;
        position: relative;
        animation: slideInUp 0.6s ease-in-out;
    }

    @keyframes slideInUp {
        0% {
            transform: translateY(20px);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .logo-login {
        width: 80px;
        display: block;
        margin: 0 auto 20px auto;
    }

    h2.text-role {
        font-size: 1.5rem;
        color: #800000;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .farm-text {
        font-size: 0.9rem;
        color: gray;
        margin-bottom: 1.5rem;
        font-style: auto;
    }

    .form-floating {
        position: relative;
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
    }

    .form-floating input {
        width: 100%;
        padding: 10px; 
        font-size: 1rem; 
        border: 1px solid #ced4da;
        border-radius: 5px; 
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-floating label {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        transition: all 0.3s ease;
        pointer-events: none;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .form-floating input:focus {
        border-color: #800000;
        box-shadow: 0 0 8px rgba(128, 0, 0, 0.5);
    }

    .form-floating input:focus + label,
    .form-floating input:not(:placeholder-shown) + label {
        top: -5%;
        left: 2px;
        font-size: 0.75rem;
        color: #800000;
    }

    .error-message {
        font-size: 0.85rem;
        color: #d9534f;
        margin-top: -10px;
        margin-bottom: 10px;
    }

    .custom-button {
        background-color: #800000;
        color: white;
        border: none;
        padding: 10px 30px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
        width: 100%;
        margin-top: 10px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .custom-button:hover {
        background-color: #440606;
        transform: scale(1.05);
    }

    .custom-button:active {
        transform: scale(0.98);
    }

    .alert {
        color: green;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .text-center {
        text-align: center;
    }

    .vh-100 {
        height: 100vh;
    }

    .btn-container form {
        width: 300px;
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
            <h2 class="text-role mb-2">PUP-T FARM SYSTEM</h2>
            <p class="farm-text">Faculty Academic Requirements Management</p>
            <div class="btn-container">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    @if (session('error'))
                        <div class="error-message text-center" id="errorMessage">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert text-center">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="form-floating d-flex justify-content-center mb-3">
                        <input type="email" name="email" class="form-control custom-width" id="floatingInput" placeholder="name@example.com" required>
                        <label for="floatingInput">Email</label>
                    </div>
                    <div class="form-floating d-flex justify-content-center mb-3">
                        <input type="password" name="password" class="form-control custom-width" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword">Password</label>
                    </div>
                    <button type="submit" value="login" class="custom-button mb-3">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>