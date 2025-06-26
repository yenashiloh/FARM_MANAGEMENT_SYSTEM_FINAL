<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pending Files</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
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

        .file-input-container {
            display: flex;
            flex-direction: column;
        }

        .file-input-container input[type="file"] {
            margin-bottom: 5px;
        }

        .file-input-container small {
            order: 1;
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
                            <h2 class="pageheader-title">Pending Files</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                       <li class="breadcrumb-item"><a href="#!"
                                                class="breadcrumb-link">Menu</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:history.back();"
                                                class="breadcrumb-link"  >Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="" class="breadcrumb-link"
                                                style=" color: #3d405c;">
                                              Pending Files</a></li>
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
                            <div class="card-header">
                                <h5 class="mb-0">List of All Pending Files</h5>
                            </div>
                            <div class="card-body">
                             <?php if(session('success')): ?>
                                <div id="successAlert" class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                    <?php echo e(session('success')); ?>

                                </div>
                                
                                <script>
                                    setTimeout(function() {
                                        var alert = document.getElementById('successAlert');
                                        if (alert) {
                                            alert.classList.remove('show');  
                                            alert.classList.add('fade');     
                                            
                                            setTimeout(function() {
                                                alert.remove(); 
                                            }, 150);  
                                        }
                                    }, 6000);
                                </script>
                            <?php endif; ?>

                                <?php if($errors->any()): ?>
                                    <div class="alert alert-danger alert-dismissible fade show text-center"
                                        role="alert">
                                        <ul>
                                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($error); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                
                             <div class="row">
                            <!-- Filter by Date Range -->
                            <div class="col-md-3 mb-2 position-relative">
                                <div class="form-group">
                                    <input type="text" name="dates" id="archive-dates" class="form-control" placeholder="Select Archive" />
                                </div>
                            </div>
                        
                            <!-- Filter by Semester Dropdown -->
                            <div class="col-md-3 mb-2 position-relative">
                                <select id="semesterFilter" class="form-control">
                                     <option value="">Select Semester</option>
                                            <?php $__currentLoopData = $availableSemesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($semester); ?>"><?php echo e($semester); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                            </div>
                        
                            <!-- Filter by School Year Dropdown -->
                            <div class="col-md-3 mb-2 position-relative">
                                <select id="schoolYearFilter" class="form-control">
                                      <option value="">Select School Year</option>
                                            <?php $__currentLoopData = $availableSchoolYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schoolYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($schoolYear); ?>"><?php echo e($schoolYear); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                            </div>
                        
                            <!-- Filter by Requirements Dropdown -->
                            <div class="col-md-3 mb-2 position-relative">
                               <select name="folder_name" class="form-control">
                                    <option value="">Select Requirements</option>
                                    <?php $__currentLoopData = $folders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($folder->folder_name_id); ?>"><?php echo e($folder->folder_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                               <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 40%; transform: translateY(-50%); pointer-events: none;"></i>
                            </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered first"  id="courseTable">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Created Date</th>
                                                <th>Folder Name</th>
                                                <th>Requirements</th>
                                                <th>Academic Year</th>
                                                <th>Files</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                              <?php $__currentLoopData = $consolidatedFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="file-row" data-semester="<?php echo e($file['semester']); ?>" data-school-year="<?php echo e($file['school_year']); ?>">
                                                    <td> <?php echo e($file['subject']); ?></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($file['files'][0]['created_at'])->timezone('Asia/Manila')->format('F j, Y, g:iA')); ?></td>
                                                      <td><?php echo e($file['folder_name']); ?></td>
                                                    <td><?php echo e(Str::limit($file['folder_name'], 15, '...')); ?></td>
                                                    <td><?php echo e($file['semester']); ?> <?php echo e($file['school_year']); ?></td>
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
                                                      <?php echo e($fileInfo['status']); ?>

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
        <!-- ============================================================== -->
        <!-- end wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper  -->
    <!-- ============================================================== -->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.js"></script>
    
    <script>
    //filter table
    document.addEventListener('DOMContentLoaded', function () {
        const semesterFilter = document.getElementById('semesterFilter');
        const schoolYearFilter = document.getElementById('schoolYearFilter');
        const folderFilter = document.querySelector('select[name="folder_name"]');
        const table = document.getElementById('courseTable');
    
        function createNoDataRow() {
            const tbody = table.getElementsByTagName('tbody')[0];
            const existingNoDataRow = tbody.querySelector('.no-data-row');
    
            if (!existingNoDataRow) {
                const noDataRow = document.createElement('tr');
                noDataRow.className = 'no-data-row';
                const noDataCell = document.createElement('td');
                noDataCell.colSpan = 10; 
                noDataCell.className = 'text-center py-4';
                noDataCell.innerHTML = '<div class="text-muted">No files found matching the selected filters</div>';
                noDataRow.appendChild(noDataCell);
                tbody.appendChild(noDataRow);
            }
        }
    
        function filterTable() {
            const selectedSemester = semesterFilter.value.trim();
            const selectedSchoolYear = schoolYearFilter.value.trim();
            const selectedFolder = folderFilter ? folderFilter.options[folderFilter.selectedIndex].text.trim() : '';
    
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = tbody.getElementsByTagName('tr');
            let visibleRows = 0;
    
            const existingNoDataRow = tbody.querySelector('.no-data-row');
            if (existingNoDataRow) {
                existingNoDataRow.remove();
            }
    
            for (let row of rows) {
                if (row.classList.contains('no-data-row')) continue;
    
                let showRow = true;
    
                const semester = row.getAttribute('data-semester')?.trim();
                const schoolYear = row.getAttribute('data-school-year')?.trim();
                const folder = row.cells[2]?.textContent.trim(); 
    
                if (selectedSemester && semester !== selectedSemester) {
                    showRow = false;
                }
                if (selectedSchoolYear && schoolYear !== selectedSchoolYear) {
                    showRow = false;
                }
                if (selectedFolder && selectedFolder !== 'Select Documents' && folder !== selectedFolder) {
                    showRow = false;
                }
    
                row.style.display = showRow ? '' : 'none';
                if (showRow) {
                    visibleRows++;
                }
            }
    
            if (visibleRows === 0) {
                createNoDataRow();
            }
        }
    
        semesterFilter.addEventListener('change', filterTable);
        schoolYearFilter.addEventListener('change', filterTable);
        if (folderFilter) folderFilter.addEventListener('change', filterTable);
    });

    //archive
    $(document).ready(function() {
        $('#archive-dates').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                applyLabel: 'Apply'
            }
        });
    
        $('#archive-dates').on('apply.daterangepicker', function(ev, picker) {
            const startDate = picker.startDate.format('MM/DD/YYYY');
            const endDate = picker.endDate.format('MM/DD/YYYY');
            
            console.log('Sending dates:', {startDate, endDate}); 
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to archive all files created between ${startDate} and ${endDate}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive them!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('archive.by.date')); ?>",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            start_date: startDate,
                            end_date: endDate
                        },
                        success: function(response) {
                            console.log('Server response:', response); 
                            
                            if (response.success) {
                                if (response.count > 0) {
                                    Swal.fire(
                                        'Archived!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'No Files Found',
                                        response.message,
                                        'info'
                                    );
                                }
                            } else {
                                Swal.fire(
                                    'Notice',
                                    response.message,
                                    'info'
                                );
                            }
                        },
                        error: function(xhr) {
                            console.error('Ajax error:', xhr); 
                            Swal.fire(
                                'Error',
                                'An error occurred while archiving files',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    
        $('#archive-dates').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

    </script>
</body>

</html>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/faculty/accomplishment/pending-files.blade.php ENDPATH**/ ?>