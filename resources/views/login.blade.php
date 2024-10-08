<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon">
    <title>Admin Login</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../assets-admin/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets-admin/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets-admin/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets-admin/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets-admin/css/style.css">
    <link rel="stylesheet" href="../../assets-admin/css/login.css">
    <!-- End layout styles -->
    <style>
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/images/PUP_1.jpeg');
            background-size: cover;
            background-position: center;
            z-index: 1;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper bg-container d-flex justify-content-center">
            <!-- Background image div -->
            <div class="bg-image"></div>

            <!-- Overlay div -->
            <div class="overlay"></div>

            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo">
                                <img class="logo-login mb-3 mt-0" src="../assets/images/pup-logo.png" alt="PUP Logo">
                            </div>
                            <h3 class="text-center" style="color: #800000">PUP-T FARM SYSTEM</h3>
                            <h6 class="font-weight-light mb-4 text-center single-line-text" style="font-weight: bold;">
                                Faculty Academic Requirements Management</h6>

                            <form id="loginForm" action="{{ route('login.post') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input name="email" class="form-control form-control-lg" placeholder="Email"
                                        required>
                                    <div class="invalid-feedback" style="display: none;">Email is invalid</div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" class="form-control form-control-lg"
                                        placeholder="Password" required>
                                    <div class="invalid-feedback" style="display: none;">Password must be at least 6
                                        characters long.</div>
                                </div>
                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit"
                                        class="btn btn-block btn-lg font-weight-medium auth-form-btn">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="toast"
                style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; background-color: #dd1212; color: white; padding: 15px; border-radius: 5px; justify-content: space-between; align-items: center; min-width: 250px; max-width: 80%;">
                <span id="toastMessage"></span>
                <button id="toastClose"
                    style="background: transparent; border: none; color: white; cursor: pointer; margin-left: 10px; font-size: 18px;">×</button>
            </div>

            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
@include('partials.faculty-footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastClose = document.getElementById('toastClose');

        const flashError = "{{ session('error') }}";
        if (flashError) {
            showToast(flashError);
        }

        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const emailInput = loginForm.email;
            const passwordInput = loginForm.password;

            hideValidationMessages();

            let errors = false;

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value)) {
                emailInput.classList.add('input-error');
                document.querySelector('.invalid-feedback').style.display = 'block';
                errors = true;
            } else {
                emailInput.classList.remove('input-error');
                emailInput.classList.add('input-success');
                document.querySelector('.invalid-feedback').style.display = 'none';
            }

            if (passwordInput.value.length < 6) {
                passwordInput.classList.add('input-error');
                passwordInput.nextElementSibling.style.display = 'block';
                errors = true;
            } else {
                passwordInput.classList.remove('input-error');
                passwordInput.classList.add('input-success');
                passwordInput.nextElementSibling.style.display = 'none';
            }

            if (!errors) {
                loginForm.submit();
            }
        });

        const emailInput = document.querySelector('input[name="email"]');
        emailInput.addEventListener('input', function() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value)) {
                emailInput.classList.remove('input-success');
                emailInput.classList.add('input-error');
                document.querySelector('.invalid-feedback').style.display = 'block';
            } else {
                emailInput.classList.remove('input-error');
                emailInput.classList.add('input-success');
                document.querySelector('.invalid-feedback').style.display = 'none';
            }
        });

        const passwordInput = document.querySelector('input[name="password"]');
        passwordInput.addEventListener('input', function() {
            if (passwordInput.value.length < 6) {
                passwordInput.classList.remove('input-success');
                passwordInput.classList.add('input-error');
                passwordInput.nextElementSibling.style.display = 'block';
            } else {
                passwordInput.classList.remove('input-error');
                passwordInput.classList.add('input-success');
                passwordInput.nextElementSibling.style.display = 'none';
            }
        });

        function hideValidationMessages() {
            document.querySelectorAll('.invalid-feedback').forEach(element => {
                element.style.display = 'none';
            });
        }

        function showToast(message) {
            if (message && message.trim() !== '') {
                toastMessage.innerHTML = message;
                toast.style.display = 'flex';
            }
        }

        function hideToast() {
            toast.style.display = 'none';
            toastMessage.innerHTML = '';
        }

        toastClose.onclick = hideToast;
    });
</script>
