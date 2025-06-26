<?php echo $__env->make('partials.tables-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<title>Completed Reviews </title>
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
    <?php echo $__env->make('partials.admin-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                            <h2 class="pageheader-title">Completed Reviews</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                         <li class="breadcrumb-item">
                                             <a href=""
                                                class="breadcrumb-link">Menu</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                             <a href="<?php echo e(route ('admin.admin-dashboard')); ?>"
                                                class="breadcrumb-link" ">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo e(route ('admin.dashboard-totals.completed-reviews')); ?>"
                                                class="breadcrumb-link" style=" color: #3d405c;">Completed Reviews</a>
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
                                                <th>Faculty Name</th>
                                                <th>Subject</th>
                                                <th>Created Date</th>
                                                <th>Documents</th>
                                                <th>Files</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $processedFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td>
                                                        <?php if(isset($files[$index]->userLogin->first_name)): ?>
                                                            <?php echo e($files[$index]->userLogin->first_name); ?> <?php echo e($files[$index]->userLogin->surname); ?>

                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($file['subject'] ?? 'N/A'); ?></td>
                                                   <td>
                                                        <?php if(isset($file['created_at'])): ?>
                                                            <?php echo e(\Carbon\Carbon::parse($file['created_at'])->format('F d, Y, g:iA')); ?>

                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($files[$index]->folderName->folder_name)): ?>
                                                            <?php echo e($files[$index]->folderName->folder_name); ?>

                                                        <?php else: ?>
                                                            N/A
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            $fileInfoArray = [];
                                                            try {
                                                                if (isset($file['files'])) {
                                                                    // If files is a JSON string, decode it
                                                                    if (is_string($file['files'])) {
                                                                        $fileInfoArray = json_decode($file['files'], true);
                                                                    } elseif (is_array($file['files'])) {
                                                                        $fileInfoArray = $file['files'];
                                                                    }
                                                                    
                                                                    // If it's not an array of objects but a single object, wrap it in an array
                                                                    if (isset($fileInfoArray['path'])) {
                                                                        $fileInfoArray = [$fileInfoArray];
                                                                    }
                                                                }
                                                            } catch (\Exception $e) {
                                                                // Handle any JSON decode errors
                                                                $fileInfoArray = [];
                                                            }
                                                        ?>
                                                        
                                                        <?php if(is_array($fileInfoArray) && count($fileInfoArray) > 0): ?>
                                                            <?php $__currentLoopData = $fileInfoArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <div class="mb-1">
                                                                    <a href="<?php echo e(Storage::url($fileInfo['path'])); ?>" target="_blank" style="text-decoration: underline; color: #3c3d43;">
                                                                        <?php echo e(Str::limit($fileInfo['name'] ?? basename($fileInfo['path']), 8, '...')); ?>

                                                                    </a>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php else: ?>
                                                            No files available
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                            <?php echo e($file['status']); ?>

                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No completed reviews found</td>
                                                </tr>
                                            <?php endif; ?>
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
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/admin/dashboard-totals/completed-reviews.blade.php ENDPATH**/ ?>