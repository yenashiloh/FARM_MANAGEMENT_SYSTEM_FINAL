<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- End layout styles -->
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
                                <div class="form-group position-relative">
                                    <label for="password">Password</label>
                                    <div class="password-container" style="position: relative;">
                                        <input 
                                            type="password" 
                                            name="password" 
                                           id="passwordInput"
                                            class="form-control form-control-lg" 
                                            placeholder="Password" 
                                            required
                                        >
                                        <button 
                                            type="button" 
                                            id="togglePassword" 
                                            class="toggle-password"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="invalid-feedback" style="display: none;">Password must be at least 6
                                            characters long.</div>
                                    </div>
                                   
                                </div>
                               
                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit"
                                        class="btn btn-block btn-lg font-weight-medium auth-form-btn ">Login</button>
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

   // password
    const passwordInput = document.querySelector('input[name="password"]');
    passwordInput.addEventListener('input', function() {
        const togglePassword = document.getElementById('togglePassword');
        const invalidFeedback = this.nextElementSibling.nextElementSibling; 
        
        if (this.value.length < 6) {
            this.classList.remove('input-success');
            this.classList.add('input-error');
            invalidFeedback.style.display = 'block';
        } else {
            this.classList.remove('input-error');
            this.classList.add('input-success');
            invalidFeedback.style.display = 'none';
        }
        
        togglePassword.style.display = 'block';
    });

    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword && passwordInput) {
        togglePassword.style.cssText = `
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
            cursor: pointer;
            pointer-events: auto;
            user-select: none;
            background: transparent;
            border: none;
            padding: 5px;
            display: block !important;
        `;
        
        togglePassword.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            if (icon) {
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            }
        });
    }
</script>
