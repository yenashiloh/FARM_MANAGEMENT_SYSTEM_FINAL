<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <?php echo $__env->make('partials.admin-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


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
    <?php echo $__env->make('partials.admin-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="loading-spinner" class="loading-spinner">
        <div class="spinner"></div>
    </div>
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content">
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
                                        <li class="breadcrumb-item">
                                            <a href="#!" class="breadcrumb-link text-secondary">Maintenance</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo e(route('admin.announcement.admin-announcement')); ?>"
                                                class="breadcrumb-link" style="color: #3d405c;">Announcement</a>
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <!-- Create Announcement Button -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?php echo e(route('admin.announcement.add-announcement')); ?>" class="btn btn-primary mb-3">
                                <i class="fas fa-plus"></i> Create Announcement
                            </a>
                            <div class="d-none d-md-block col-md-4 col-sm-6">
                                <input type="text" id="search" class="form-control ml-3 mb-3"
                                    placeholder="Search announcements..." />
                            </div>
                        </div>
                        <!-- Mobile search input -->
                        <div class="d-md-none mt-3">
                            <input type="text" id="search-mobile" class="form-control mb-3"
                                placeholder="Search announcements..." />
                        </div>


                        <!-- Success Message -->
                        <?php if(session('success')): ?>
                            <div id="success-message"
                                class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>

                        <!-- Announcements List -->
                        <div id="announcements-list">
                            <?php if($announcements->isEmpty()): ?>
                                <div class="alert alert-info text-center" role="alert">
                                    No announcements found.
                                </div>
                            <?php else: ?>
                                <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="card mb-4">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1"><?php echo e($announcement->subject); ?></h5>
                                                <small class="text-muted">
                                                    <?php echo e(\Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:i a')); ?>

                                                </small>
                                                <div class="mt-2">
                                                    <span class="text-muted">To:</span>
                                                    <?php $__currentLoopData = $announcement->displayEmails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span
                                                            class="badge bg-light text-dark"><?php echo e($email); ?></span>
                                                        <?php if(!$loop->last): ?>
                                                            <span class="mx-1">,</span>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($announcement->moreEmailsCount > 0): ?>
                                                        <span class="text-muted">and
                                                            <?php echo e($announcement->moreEmailsCount); ?> more</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <?php if($announcement->published): ?>
                                                    <span class="badge badge-success mr-3">Published</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning mr-3">Unpublished</span>
                                                <?php endif; ?>
                                                <div class="dropdown">
                                                    <i class="fas fa-ellipsis-h" role="button"
                                                        id="dropdownMenuButton<?php echo e($announcement->id_announcement); ?>"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        style="cursor: pointer;"></i>
                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="dropdownMenuButton<?php echo e($announcement->id_announcement); ?>">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="<?php echo e(route('admin.announcement.edit-announcement', $announcement->id_announcement)); ?>">
                                                                <i class="fas fa-edit me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item delete-btn"
                                                                data-id="<?php echo e($announcement->id_announcement); ?>">
                                                                <i class="fas fa-trash-alt me-2"></i> Delete
                                                            </button>
                                                        </li>
                                                        <?php if($announcement->published): ?>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="<?php echo e(route('admin.announcement.unpublish-announcement', $announcement->id_announcement)); ?>">
                                                                    <i class="fas fa-times-circle me-2"></i> Unpublish
                                                                </a>
                                                            </li>
                                                        <?php else: ?>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="<?php echo e(route('admin.announcement.publish-announcement', $announcement->id_announcement)); ?>">
                                                                    <i class="fas fa-check-circle me-2"></i> Publish
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-text"><?php echo $announcement->message; ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center my-4">
                                    <div class="pagination-info">
                                        Showing <?php echo e($announcements->firstItem() ?? 0); ?> to
                                        <?php echo e($announcements->lastItem() ?? 0); ?> of <?php echo e($announcements->total()); ?> results
                                    </div>

                                    <?php if($announcements->hasPages()): ?>
                                        <nav aria-label="Announcements pagination">
                                            <ul class="pagination mb-0">
                                                
                                                <?php if($announcements->onFirstPage()): ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </span>
                                                    </li>
                                                <?php else: ?>
                                                    <li class="page-item">
                                                        <a class="page-link"
                                                            href="<?php echo e($announcements->previousPageUrl()); ?>"
                                                            rel="prev">
                                                            <i class="fas fa-chevron-left"></i>
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
                                                        <a class="page-link" href="<?php echo e($announcements->nextPageUrl()); ?>"
                                                            rel="next">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </li>
                                                <?php else: ?>
                                                    <li class="page-item disabled">
                                                        <span class="page-link">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </span>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- end wrapper  -->
                    <!-- ============================================================== -->
                </div>
            </div>
        </div>
    </div>


    <!-- ============================================================== -->
    <!-- end main wrapper  -->
    <!-- ============================================================== -->

    <?php echo $__env->make('partials.admin-footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        //dropdown toggle
        function toggleDropdown(id) {
            var dropdownMenu = document.getElementById('dropdownMenu' + id);
            var rect = dropdownMenu.getBoundingClientRect();

            if (rect.right > window.innerWidth) {
                dropdownMenu.style.left = 'auto';
                dropdownMenu.style.right = '0';
            } else {
                dropdownMenu.style.left = 'auto';
                dropdownMenu.style.right = 'initial';
            }

            dropdownMenu.classList.toggle('show');
        }

        //delete Sweet Alert
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const url = `/admin/announcement/delete/${id}`;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Content-Type': 'application/json',
                                    },
                                })
                                .then(response => {
                                    if (response.ok) {
                                        Swal.fire(
                                            'Deleted!',
                                            'Your announcement has been deleted.',
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        throw new Error(
                                            'Error deleting the announcement');
                                    }
                                })
                                .catch(error => {
                                    Swal.fire(
                                        'Error!',
                                        'There was an error deleting the announcement.',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });
        });

        //success message
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('success-message')) {
                setTimeout(function() {
                    $('#success-message').alert('close');
                }, 8000);
            }
        });

        //search announcement
        $(document).ready(function() {
            $('#search, #search-mobile').on('keyup', function() {
                let query = $(this).val();

                $.ajax({
                    url: '<?php echo e(route('admin.announcement.search')); ?>',
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
<?php /**PATH C:\Users\Ed Judah Mingo\Documents\Laravel\FARM_MANAGEMENT_SYSTEM_FINAL-3\resources\views/admin/announcement/admin-announcement.blade.php ENDPATH**/ ?>