<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Archive</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../../asset/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="<?php echo e(asset('assets/images/pup-logo.png')); ?>" type="image/x-icon">
    <link href="../../../../asset/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../../asset/libs/css/style.css">
    <link rel="stylesheet" href="../../../../asset/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/buttons.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/select.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/fixedHeader.bootstrap4.css">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        .body-modal {
            max-height: 480px;
            overflow-y: auto;
        }

        .view-modal {
            max-height: 400px;
            overflow-y: auto;
        }

        .bordered-file-input {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            background-color: #fff;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        }

        .modal-dialog {
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        p {
            color: #3d405c;
        }

        strong {
            color: rgb(27, 27, 27);
        }
         .table td {
        color: #3c3d43;
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
                            <h2 class="pageheader-title">Archive Files</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                >Menu</a></li>
                                        <li class="breadcrumb-item"><a href="<?php echo e(route('faculty.view-archive')); ?>"
                                                class="breadcrumb-link" style=" color: #3d405c;">Archive</a></li>
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
                        <div class="card">
                            <div class="card-header fw-bold">
                                All Archive Files
                            </div>
                            <div class="card-body">
                                <?php if(session('success')): ?>
                                    <div class="alert alert-success text-center">
                                        <?php echo e(session('success')); ?>

                                    </div>
                                <?php endif; ?>

                                <form id="bulk-restore-form" action="<?php echo e(route('files.bulkUnarchive')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-primary btn-sm mb-3"
                                        id="restore-selected">Restore</button>

                                <div class="row">
                                    <!-- Filter by Semester Dropdown -->
                                    <div class="col-md-3 mb-3 position-relative">
                                        <select id="semesterFilter" class="form-control">
                                            <option value="">Select Semester</option>
                                            <option value="First Semester">First Semester</option>
                                            <option value="Second Semester">Second Semester</option>
                                            <option value="Summer">Summer</option>
                                            
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                
                                    <!-- Filter by School Year Dropdown -->
                                    <div class="col-md-3 mb-3 position-relative">
                                        <select id="schoolYearFilter" class="form-control">
                                             <option value="">Select School Year</option>
                                            <?php $__currentLoopData = $availableSchoolYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schoolYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($schoolYear); ?>"><?php echo e($schoolYear); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                         
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                
                                    <!-- Filter by Requirements Dropdown -->
                                   <div class="col-md-3 mb-3 position-relative">
                                    <select name="folder_name" class="form-control">
                                        <option value="">Select Documents</option>
                                        <?php $__currentLoopData = $folders->unique('folder_name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($folder->folder_name_id); ?>"><?php echo e($folder->folder_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                       <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                </div>
                                
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered first" id="courseTable">
                                            <thead>
                                                <tr>
                                                <th><input type="checkbox" id="select-all"></th>
                                                <th>Subject</th>
                                                <th>Created Date</th>
                                                <th>Documents</th>
                                                <th>Academic Year</th>
                                                <th>Files</th>
                                                <th>Status</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__empty_1 = true; $__currentLoopData = $uploadedFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td><input type="checkbox" name="file_ids[]"
                                                                value="<?php echo e($file->courses_files_id); ?>"
                                                                class="file-checkbox"></td>
                                                        <td><?php echo e($file->subject); ?></td>
                                                         <td><?php echo e(\Carbon\Carbon::parse($file->created_at)->locale('en_PH')->format('F j, Y, g:i A')); ?>

                                                        </td>
                                                        <td><?php echo e($file->folderName->folder_name); ?></td>
                                                       
                                                        <td><?php echo e($file->semester); ?> <?php echo e($file->school_year); ?></td>
                                           
                                                        <td>
                                                           <?php $__currentLoopData = $file['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                               <div class="mb-1">
                                                                   <a href="<?php echo e(Storage::url($fileInfo['path'])); ?>" target="_blank" style="text-decoration: underline; color: #3c3d43;">
                                                                       <?php echo e(Str::limit($fileInfo['name'], 8, '...')); ?>

                                                                   </a>
                                                               </div>
                                                           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </td>
                                                        <td>
                                                            <?php if($file->status === 'To Review'): ?>
                                                                <span><?php echo e($file->status); ?></span>
                                                            <?php elseif($file->status === 'Approved'): ?>
                                                                <span><?php echo e($file->status); ?></span>
                                                            <?php elseif($file->status === 'Declined'): ?>
                                                                <span><?php echo e($file->status); ?></span>
                                                                <?php if($file->declined_reason): ?>
                                                                    <div class="mt-2">Declined Reason: <?php echo e($file->declined_reason); ?></div>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <span><?php echo e($file->status); ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                   
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end wrapper  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- end main wrapper  -->
        <!-- ============================================================== -->

       <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const fileCheckboxes = document.querySelectorAll('.file-checkbox');
            const bulkRestoreForm = document.getElementById('bulk-restore-form');
            const restoreSelectedButton = document.getElementById('restore-selected');
    
            selectAllCheckbox.addEventListener('change', function() {
                fileCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateRestoreButtonVisibility();
            });
    
            fileCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateRestoreButtonVisibility);
            });
    
            function updateRestoreButtonVisibility() {
                const checkedBoxes = document.querySelectorAll('.file-checkbox:checked');
                restoreSelectedButton.style.display = checkedBoxes.length > 0 ? 'inline-block' : 'none';
            }
    
            bulkRestoreForm.addEventListener('submit', function(e) {
                e.preventDefault();
    
                const checkedBoxes = document.querySelectorAll('.file-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No files selected',
                        text: 'Please select at least one file to restore.'
                    });
                    return;
                }
    
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to restore the selected files.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restore them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkRestoreForm.submit();
                    }
                });
            });
    
            updateRestoreButtonVisibility();
        });
        
        document.addEventListener('DOMContentLoaded', function () {
            const semesterFilter = document.getElementById('semesterFilter');
            const schoolYearFilter = document.getElementById('schoolYearFilter');
            const folderFilter = document.querySelector('select[name="folder_name"]');
            
            const dataTable = $('#courseTable').DataTable({
                pageLength: 10,
                lengthMenu: [[10, 15, 20, -1], [10, 15, 20, "All"]]
            });
            
            // Custom filtering function
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Get filter values
                    const selectedSemester = semesterFilter ? semesterFilter.value.trim() : '';
                    const selectedSchoolYear = schoolYearFilter ? schoolYearFilter.value.trim() : '';
                    const selectedFolder = folderFilter ? folderFilter.options[folderFilter.selectedIndex].text : '';
                    
                    // Skip filters if nothing selected
                    const semesterMatch = !selectedSemester || selectedSemester === 'Select Semester' || 
                                        data[4].includes(selectedSemester);
                    
                    const schoolYearMatch = !selectedSchoolYear || selectedSchoolYear === 'Select School Year' || 
                                           data[4].includes(selectedSchoolYear);
                    
                    const folderMatch = !selectedFolder || selectedFolder === 'Select Documents' || 
                                      data[3] === selectedFolder;
                    
                    // Return true if all applicable filters match
                    return semesterMatch && schoolYearMatch && folderMatch;
                }
            );
            
            function filterTable() {
                dataTable.draw();
            }
            
            if (semesterFilter) semesterFilter.addEventListener('change', filterTable);
            if (schoolYearFilter) schoolYearFilter.addEventListener('change', filterTable);
            if (folderFilter) folderFilter.addEventListener('change', filterTable);
        });
        </script>
           <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="../../../../asset/vendor/jquery/jquery-3.3.1.min.js"></script>
            <script src="../../../../asset/vendor/bootstrap/js/bootstrap.bundle.js"></script>
            <script src="../../../../asset/vendor/slimscroll/jquery.slimscroll.js"></script>
            <script src="../../../../asset/vendor/multi-select/js/jquery.multi-select.js"></script>
            <script src="../../../../asset/libs/js/main-js.js"></script>
            <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
            <script src="../../../../asset/vendor/datatables/js/dataTables.bootstrap4.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
            <script src="../../../../asset/vendor/datatables/js/buttons.bootstrap4.min.js"></script>
            <script src="../../../../asset/vendor/datatables/js/data-table.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
            <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
            <script src="https://cdn.datatables.net/rowgroup/1.0.4/js/dataTables.rowGroup.min.js"></script>
            <script src="https://cdn.datatables.net/select/1.2.7/js/dataTables.select.min.js"></script>
            <script src="https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
            <script src="../../../../asset/vendor/datatables/js/loading.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
         
</body>

</html>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/faculty/view-archive.blade.php ENDPATH**/ ?>