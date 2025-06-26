<?php echo $__env->make('partials.tables-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<title><?php echo e($folderName); ?> </title>
</head>
<style>
    .form-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }

    .col-form-label {
        margin-right: 10px;
        white-space: nowrap;
    }

    .col-sm-3 {
        flex: 1;
        min-width: 200px;
        position: relative;
    }

    select.form-control {
        width: 100%;
        padding-right: 30px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .fas.fa-chevron-down {
        position: absolute;
        top: 50%;
        right: 25px;
        transform: translateY(-50%);
        pointer-events: none;
        z-index: 1;
    }

    @media (max-width: 576px) {
        .form-group {
            flex-direction: column;
            align-items: stretch;
        }

        .col-form-label {
            margin-bottom: 5px;
        }

        .col-sm-3 {
            width: 100%;
        }
    }
</style>

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
                            <h2 class="pageheader-title"><?php echo e($folderName); ?> </h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                            >Menu</a></li>
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo e(route('director.department', ['folder_name_id' => $folder_name_id])); ?>"
                                                class="breadcrumb-link" style=" color: #3d405c;">Department</a>
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered first">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Department</th>
                                                <th>Progress</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($loop->iteration); ?></td>
                                                    <td><?php echo e($department->name); ?></td>
                                                 <td>
                                                       <?php
                                                            $progressData = $departmentProgress[$department->name] ?? ['progress' => 0, 'approved' => 0, 'total' => 0, 'faculty_count' => 0];
                                                            $progress = min(100, $progressData['progress']);
                                                        ?>
                                                        <div class="progress">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: <?php echo e($progress); ?>%;"
                                                                aria-valuenow="<?php echo e($progress); ?>" 
                                                                aria-valuemin="0"
                                                                aria-valuemax="100">
                                                                <?php echo e(number_format($progress, 2)); ?>%
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">
                                                            (<?php echo e($progressData['approved']); ?>/<?php echo e($progressData['total']); ?> requirements approved)
                                                            <br>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e(route('view.accomplishment.department', [
                                                            'department' => urlencode($department->name),
                                                            'folder_name_id' => $folder->folder_name_id,
                                                        ])); ?>"
                                                            class="btn btn-primary text-white">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('partials.tables-footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>

</html>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/director/department.blade.php ENDPATH**/ ?>