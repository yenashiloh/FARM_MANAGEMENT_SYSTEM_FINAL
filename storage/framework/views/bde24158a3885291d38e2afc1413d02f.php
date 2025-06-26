<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <?php echo $__env->make('partials.director-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <title>View Account</title>
    <style>
        .toggle-dropdown {
            position: absolute;
            margin-left: 20px;
            top: 100%;
            transform: translateX(-100%);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: white;
            border-radius: 4px;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <?php echo $__env->make('partials.director-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="loading-spinner" class="loading-spinner">
        <div class="spinner"></div>
    </div>
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">View Account</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Account</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->
                <div class="ecommerce-widget">
                    <div class="row">
                        <!-- ============================================================== -->
                        <!-- basic form -->
                        <!-- ============================================================== -->
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header">Account Details</h5>
                                <div class="card-body">
                                    <form action="<?php echo e(route('updateDirectorAccount')); ?>" method="POST" id="basicform" data-parsley-validate="">
                                        <?php echo csrf_field(); ?>
                                        <!-- Success Message -->
                                        <?php if(session('success')): ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <?php echo e(session('success')); ?>

                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Error Messages -->
                                        <?php if($errors->any()): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <ul>
                                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li><?php echo e($error); ?></li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="inputFirstName">First Name</label>
                                                    <input id="inputFirstName" type="text" name="first-name" value="<?php echo e(old('first-name', $user->first_name)); ?>" data-parsley-trigger="change" required="" placeholder="Enter first name" autocomplete="off" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputEmail">Email address</label>
                                                    <input id="inputEmail" type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" data-parsley-trigger="change" required="" placeholder="Enter email" autocomplete="off" class="form-control">
                                                </div>
                                              
                                              
                                                <div class="form-group">
                                                    <label for="inputConfirmPassword">Confirm Password</label>
                                                    <input id="inputConfirmPassword" type="password" name="confirm-password" data-parsley-equalto="#inputNewPassword" placeholder="Confirm new password" autocomplete="off" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="inputLastName">Last Name</label>
                                                    <input id="inputLastName" type="text" name="last-name" value="<?php echo e(old('last-name', $user->surname)); ?>" data-parsley-trigger="change" required="" placeholder="Enter last name" autocomplete="off" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputRecentPassword">Recent Password</label>
                                                    <input id="inputRecentPassword" type="password" name="recent-password" data-parsley-trigger="change" required="" placeholder="Enter recent password" autocomplete="off" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputNewPassword">New Password</label>
                                                    <input id="inputNewPassword" type="password" name="new-password" data-parsley-trigger="change" placeholder="Enter new password" autocomplete="off" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 mt-3">
                                                <p>
                                                    <button type="submit" class="btn btn-space btn-primary">Submit</button>
                                                    <button type="reset" class="btn btn-space btn-secondary">Cancel</button>
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                        <!-- ============================================================== -->
                        <!-- end main wrapper  -->
                        <!-- ============================================================== -->

                        <?php echo $__env->make('partials.director-footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

                       
</body>

</html>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/director/director-account.blade.php ENDPATH**/ ?>