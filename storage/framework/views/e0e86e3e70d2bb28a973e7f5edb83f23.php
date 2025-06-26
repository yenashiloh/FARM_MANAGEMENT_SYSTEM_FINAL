<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <?php echo $__env->make('partials.faculty-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <title>Announcement</title>
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
    <?php echo $__env->make('partials.faculty-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                            <h2 class="pageheader-title">Announcement</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                >Menu</a></li>
                                        <li class="breadcrumb-item"><a href="<?php echo e(route('faculty.announcement')); ?>"
                                                class="breadcrumb-link" style=" color: #3d405c;">Announcement</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- Page Header -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="d-flex justify-content-end align-items-center">
                            <div class="col-md-4 col-sm-6 d-none d-md-block">
                                <input type="text" id="search" class="form-control ml-3 mb-3"
                                    placeholder="Search announcements..." />
                            </div>
                        </div>

                        <!-- Mobile search input -->
                        <div class="d-md-none mt-3">
                            <input type="text" id="search-mobile" class="form-control mb-3"
                                placeholder="Search announcements..." />
                        </div>
                        <div id="announcements-list">
                            <?php if($announcements->isEmpty()): ?>
                                <div class="alert alert-info" role="alert">
                                    No announcements available.
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title" style="font-size: 20px;">
                                                        <?php echo e($announcement->subject); ?>

                                                    </h5>
                                                    <h6 class="card-subtitle text-muted" style="font-size:12px;">
                                                        <?php echo e(\Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:i a')); ?>

                                                    </h6>
                                                    <p class="card-subtitle text-muted mt-2" style="font-size:12px;">
                                                        To:
                                                        <?php $__currentLoopData = $announcement->displayEmails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php echo e($email); ?><?php if(!$loop->last): ?>
                                                                ,
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($announcement->moreEmailsCount > 0): ?>
                                                            and <?php echo e($announcement->moreEmailsCount); ?> more
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <div class="card-body">
                                                    <p class="card-text"><?php echo $announcement->message; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                  <!-- Pagination -->
                                  <div class="row ">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center my-4">
                                            <div class="pagination-info">
                                                Showing <?php echo e($announcements->firstItem() ?? 0); ?> to
                                                <?php echo e($announcements->lastItem() ?? 0); ?> of <?php echo e($announcements->total()); ?>

                                                announcements
                                            </div>

                                            <?php if($announcements->hasPages()): ?>
                                                <nav aria-label="Announcements pagination">
                                                    <ul class="pagination mb-0">
                                                        
                                                        <?php if($announcements->onFirstPage()): ?>
                                                            <li class="page-item disabled">
                                                                <span class="page-link">
                                                                    <i class="fas fa-chevron-left small"></i>
                                                                </span>
                                                            </li>
                                                        <?php else: ?>
                                                            <li class="page-item">
                                                                <a class="page-link"
                                                                    href="<?php echo e($announcements->previousPageUrl()); ?>"
                                                                    rel="prev">
                                                                    <i class="fas fa-chevron-left small"></i>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>

                                                        
                                                        <?php $__currentLoopData = $announcements->getUrlRange(1, $announcements->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($page == $announcements->currentPage()): ?>
                                                                <li class="page-item active">
                                                                    <span class="page-link"><?php echo e($page); ?></span>
                                                                </li>
                                                            <?php else: ?>
                                                                <li class="page-item">
                                                                    <a class="page-link"
                                                                        href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                                                                </li>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                        
                                                        <?php if($announcements->hasMorePages()): ?>
                                                            <li class="page-item">
                                                                <a class="page-link"
                                                                    href="<?php echo e($announcements->nextPageUrl()); ?>"
                                                                    rel="next">
                                                                    <i class="fas fa-chevron-right small"></i>
                                                                </a>
                                                            </li>
                                                        <?php else: ?>
                                                            <li class="page-item disabled">
                                                                <span class="page-link">
                                                                    <i class="fas fa-chevron-right small"></i>
                                                                </span>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </nav>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Page Header -->
        <!-- ============================================================== -->


        <?php echo $__env->make('partials.faculty-footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#search, #search-mobile').on('keyup', function() {
                    let query = $(this).val();

                    $.ajax({
                        url: '<?php echo e(route('faculty.announcement.search')); ?>',
                        method: 'GET',
                        data: {
                            search: query
                        },
                        success: function(data) {
                            $('#announcements-list').html(data);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>


</body>

</html>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/faculty/announcement.blade.php ENDPATH**/ ?>