<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <?php echo $__env->make('partials.admin-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <link rel="stylesheet" href="../../../../asset/vendor/select2/css/select2.css">
    <link rel="stylesheet" href="../../../../asset/vendor/summernote/css/summernote-bs4.css">
    <title>Announcement</title>

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
                    <div class="col-lg-12 ">
                        <div class="page-header">
                            <h2 class="pageheader-title">Announcement</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="#!" class="breadcrumb-link">Maintenance</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo e(route('admin.announcement.admin-announcement')); ?>"
                                                class="breadcrumb-link">Announcement</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo e(route('admin.announcement.add-announcement')); ?>"
                                                class="breadcrumb-link" style="color: #3d405c;">Edit</a>
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
                <div class="ecommerce-widget">
                    <div class="row">
                        <div class="col-12">
                            <form method="POST"
                                action="<?php echo e(route('admin.announcement.update-announcement', $announcement->id_announcement)); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="email-compose-fields">
                                    <div class="to">
                                          <h5 >This edit will only apply to the announcement on this website and not in the email received by the faculty.</h5>
                                          <div class="form-group row pt-0">
                                            <label class="col-md-1 control-label">To:</label>
                                            <div class="col-md-11">
                                                <select id="recipientEmails" class="js-example-basic-multiple form-control" name="recipient_emails[]" multiple="multiple" required>
                                                    <option value="all-faculty" <?php echo e($announcement->type_of_recepient === 'All Faculty' ? 'selected' : ''); ?>>All Faculty</option>
                                                    
                                                    <!-- Add Departments -->
                                                    <optgroup label="Departments">
                                                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="department-<?php echo e($department->department_id); ?>" 
                                                                <?php echo e(in_array($department->name, explode(', ', $announcement->type_of_recepient)) ? 'selected' : ''); ?>>
                                                                <?php echo e($department->name); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </optgroup>                                                   
                                                    
                                                    <!-- Add Faculty Emails -->
                                                    <optgroup label="Faculty Emails">
                                                    <?php $__currentLoopData = $facultyEmails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($email); ?>" 
                                                            <?php echo e(in_array($email, explode(', ', $announcement->type_of_recepient)) ? 'selected' : ''); ?>>
                                                            <?php echo e($email); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="subject">
                                        <div class="form-group row pt-2">
                                            <label class="col-md-1 control-label">Subject</label>
                                            <div class="col-md-11">
                                                <input class="form-control"   value="<?php echo e(old('announcement_subject', $announcement->subject)); ?>" type="text" id="announcement_subject"
                                                    placeholder="Enter subject" id="announcement_subject"
                                                    name="announcement_subject" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 
                                <div class="email editor">
                                    <div class="col-md-12 pl-4 px-4">
                                
                                        <div class="form-group">
                                            <label class="control-label sr-only" for="summernote">Descriptions </label>
                                            <textarea class="form-control" id="summernote" id="announcement-editor" name="announcement_message" rows="6"
                                                placeholder="Write Descriptions" required><?php echo old('announcement_message', $announcement->message); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="email action-send">
                                        <div class="col-md-12 pl-4">
                                            <div class="form-group">
                                                <button class="btn btn-primary btn-space" type="submit"><i
                                                        class="icon s7-mail"></i>Update</button>
                                                <button class="btn btn-secondary btn-space" type="button"><i
                                                        class="icon s7-close"></i> Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Toast Container -->
                <div class="toast-container position-fixed p-3 end-0 bottom-0">

                    <div class="toast-container position-fixed p-3 end-0 bottom-0">
                        <div class="toast align-items-center text-white bg-danger border-0" id="errorToast"
                            role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body" data-message="<?php echo e($errors->first()); ?>"
                                    style="text-align: center;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- end wrapper  -->
                <!-- ============================================================== -->
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end main wrapper  -->
        <!-- ============================================================== -->
    </div>



    <script src="../../../../asset/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script src="../../../../asset/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../../../../asset/vendor/slimscroll/jquery.slimscroll.js"></script>
    <script src="../../../../asset/vendor/select2/js/select2.min.js"></script>
    <script src="../../../../asset/vendor/summernote/js/summernote-bs4.js"></script>
    <script src="../../../../asset/libs/js/main-js.js"></script>
    <script src="../../../../asset/vendor/timeline/js/announcement-js."></script>
    <script src="../../../../asset/vendor/datatables/js/loading.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                tags: true
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 300

            });
        });
    </script>
</body>

</html>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/admin/announcement/edit-announcement.blade.php ENDPATH**/ ?>