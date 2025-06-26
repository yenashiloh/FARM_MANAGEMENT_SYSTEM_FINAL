<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Classroom Management</title>
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
        .body-modal {
            max-height: 540px;
            overflow-y: auto;
        }

        .view-modal {
            max-height: 700px;
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
        
        .message {
            word-wrap: break-word;
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
                            <h2 class="pageheader-title">Classroom Management</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!"
                                                class="breadcrumb-link">Accomplishment</a></li>
                                        <li class="breadcrumb-item"><a href="" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">
                                                Classroom Management </a></li>
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
                    <?php if(auth()->user()->role == 'faculty-coordinator'): ?>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                            <div class="simple-card">
                                <ul class="nav nav-tabs" id="myTab5" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active border-left-0" id="home-tab-simple" data-toggle="tab"
                                            href="#home-simple" role="tab" aria-controls="home"
                                            aria-selected="true">My Document Upload Progress</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="department-tab" data-toggle="tab" href="#department"
                                            role="tab" aria-controls="department" aria-selected="false">All
                                            Departments</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="myTabContent5">
                                    <div class="tab-pane fade show active" id="home-simple" role="tabpanel"
                                        aria-labelledby="home-tab-simple">
                                        <div class="card-body">
                                            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                <p class="mb-0">This progress includes all documents that have been
                                                    approved by the admin.</p>
                                            </div>
                                            <h5 class="mb-3">Overall Progress</h5>
                                            <div class="progress mb-4">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="width: <?php echo e($overallProgress); ?>%;"
                                                    aria-valuenow="<?php echo e($overallProgress); ?>" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    <?php echo e(number_format($overallProgress, 2)); ?>%
                                                </div>
                                            </div>
                                            <hr>

                                            <?php
                                                $currentMainFolder = null;
                                                $currentFolderId = request()->route('folder_name_id');
                                                $currentFolder = $folders->firstWhere(
                                                    'folder_name_id',
                                                    $currentFolderId,
                                                );
                                                if ($currentFolder) {
                                                    $currentMainFolder = $currentFolder->main_folder_name;
                                                }
                                            ?>

                                            <?php if($currentMainFolder && isset($folderProgress[$currentMainFolder])): ?>
                                                <h5 class="mb-3"><?php echo e($currentMainFolder); ?> Progress</h5>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: <?php echo e($folderProgress[$currentMainFolder]); ?>%;"
                                                        aria-valuenow="<?php echo e($folderProgress[$currentMainFolder]); ?>"
                                                        aria-valuemin="0" aria-valuemax="100">
                                                        <?php echo e(number_format($folderProgress[$currentMainFolder], 2)); ?>%
                                                    </div>
                                                </div>
                                                <hr>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="department" role="tabpanel" aria-labelledby="department-tab" style="padding: 20px;">
                                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <p class="mb-0">This progress shows the overall progress for each department.</p>
                                        </div>
                                        <div id="departmentList"></div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    <?php elseif(auth()->user()->role == 'faculty'): ?>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <p class="mb-0">This progress includes all documents that have been approved
                                            by the admin.</p>
                                    </div>
                                    <h6>Overall Progress</h6>
                                    <div class="progress mb-3">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                            style="width: <?php echo e($overallProgress); ?>%;"
                                            aria-valuenow="<?php echo e($overallProgress); ?>" aria-valuemin="0"
                                            aria-valuemax="100">
                                            <?php echo e(number_format($overallProgress, 2)); ?>%
                                        </div>
                                    </div>

                                    <?php
                                        $currentMainFolder = null;
                                        $currentFolderId = request()->route('folder_name_id');
                                        $currentFolder = $folders->firstWhere('folder_name_id', $currentFolderId);
                                        if ($currentFolder) {
                                            $currentMainFolder = $currentFolder->main_folder_name;
                                        }
                                    ?>

                                    <?php if($currentMainFolder && isset($folderProgress[$currentMainFolder])): ?>
                                        <h6><?php echo e($currentMainFolder); ?> Progress</h6>
                                        <div class="progress mb-3">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                style="width: <?php echo e($folderProgress[$currentMainFolder]); ?>%;"
                                                aria-valuenow="<?php echo e($folderProgress[$currentMainFolder]); ?>"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <?php echo e(number_format($folderProgress[$currentMainFolder], 2)); ?>%
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="alert alert-warning" role="alert">
                                        You do not have permission to view this content.
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"> Classroom Management (an academic document that communicates
                                    information about a specific course and
                                    explains the rules, responsibilities, and expectations associated with it.)</h5>
                            </div>
                            <div class="card-body">

                                <!-- Upload Files Button -->
                                <?php if($isUploadOpen): ?>
                                    <p style="color: #222222;">
                                        <strong>Opened:</strong> <?php echo e($formattedStartDate); ?><br>
                                        <strong>Due:</strong> <?php echo e($formattedEndDate); ?><br>
                                    </p>
                                
                                    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal"
                                        data-bs-target="#addFolderModal">
                                        <i class="fas fa-plus"></i> Upload Files
                                    </a>
                                <?php else: ?>
                                    <p class="text-danger">
                                        <?php echo e($statusMessage); ?>

                                        <?php if($statusMessage !== 'No upload schedule set.'): ?>
                                            <br><br>
                                            <strong style="color: #222222;">Opened:</strong>
                                            <span style="color: #222222;"><?php echo e($formattedStartDate); ?></span><br>
                                            <strong style="color: #222222;">Due:</strong>
                                            <span style="color: #222222;"><?php echo e($formattedEndDate); ?></span><br>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>

                                <?php if(!$isUploadOpen): ?>
                                    <button type="button" class="btn btn-primary mb-4" data-toggle="modal"
                                        data-target="#requestModal">
                                        Request Upload Access
                                    </button>
                                <?php endif; ?>

                                <!-- Modal for Request to Open -->
                                <div class="modal fade" id="requestModal" tabindex="-1" role="dialog"
                                    aria-labelledby="requestModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="requestModalLabel">Request Upload Access
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="<?php echo e(route('request.upload.access')); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="reason" class="form-label">Reason for
                                                            Request</label>
                                                        <textarea class="form-control" id="reason" name="reason" rows="6" required></textarea>
                                                    </div>
                                                    <input type="hidden" name="user_login_id"
                                                        value="<?php echo e(auth()->id()); ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Submit
                                                        Request</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

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
                                            <input type="text" name="dates" id="archive-dates" class="form-control" style="height: 38px;" placeholder="Archive by Date" />
                                        </div>
                                    </div>
        
                                    <!-- Filter by Semester Dropdown -->
                                    <div class="col-md-3 mb-3 position-relative">
                                        <select id="semesterFilter" class="form-control">
                                            <option value="">Select Semester</option>
                                            <?php $__currentLoopData = $availableSemesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($semester); ?>">
                                                    <?php switch($semester):
                                                        case ('First Sem'): ?>
                                                            First Semester
                                                            <?php break; ?>
                                                        <?php case ('Second Sem'): ?>
                                                            Second Semester
                                                            <?php break; ?>
                                                        <?php default: ?>
                                                            <?php echo e($semester); ?>

                                                    <?php endswitch; ?>
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                                            <?php $__currentLoopData = $folders->where('main_folder_name', 'Classroom Management'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                                <th>Subject</th>
                                                <th>Created Date</th>
                                                <th>Documents</th>
                                                <th>Academic Year</th>
                                                <th>Files</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                              <?php $__currentLoopData = $consolidatedFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="file-row" data-semester="<?php echo e($file['semester']); ?>" data-school-year="<?php echo e($file['school_year']); ?>">
                                                    <td> <?php echo e($file['subject']); ?></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($file['files'][0]['created_at'])->timezone('Asia/Manila')->format('F j, Y, g:iA')); ?></td>
                                                    <td><?php echo e($file['folder_name']); ?></td>
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
                                                  <td class="text-center">
                                                    <?php echo e($fileInfo['status']); ?>

                                                    <?php if(!empty($file['files'][0]['declined_reason'])): ?>
                                                        <div class="small text-danger">
                                                            <button type="button" 
                                                                    class="btn btn-link btn-sm view-messages-btn" 
                                                                    data-id="<?php echo e($file['courses_files_id']); ?>"
                                                                    data-declined-reason="<?php echo e($file['files'][0]['declined_reason']); ?>">
                                                                View Messages
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                  <td>
                                                    <?php if($file['status'] !== 'Approved'): ?>
                                                        <button type="button" 
                                                                class="btn btn-warning btn-sm edit-files-btn" 
                                                                data-id="<?php echo e($file['courses_files_id']); ?>"
                                                                data-original-filename="<?php echo e($file['files'][0]['name']); ?>"
                                                                data-subject="<?php echo e($file['subject']); ?>">
                                                           </i> Edit
                                                        </button>
                                                    <?php endif; ?>
                                                    
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
            
           <!-- Messages Modal -->
            <div class="modal fade" id="messagesModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header ">
                            <h5 class="modal-title">Messages</h5>
                            <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-3" style="background-color: #f9f9f9;">
                            <div class="message-container mb-3" style="max-height: 400px; overflow-y: auto;"></div>
                            <form id="messageForm" class="mt-3 d-flex align-items-center">
                                <input type="hidden" name="courses_files_id">
                                <textarea name="message_body" class="form-control mr-2" rows="1" required placeholder="Type your message..." style="resize: none;"></textarea>
                                <button type="submit" class="btn btn-primary">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit File Modal -->
            <div class="modal fade" id="editFileModal" tabindex="-1" role="dialog" aria-labelledby="editFileModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editFileModalLabel">Edit Files</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editFileForm" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" id="fileId" name="fileId">
                                <div class="form-group">
                                    <h5><strong>Subject: </strong>
                                </div>
                                
                                <div class="form-group">
                                    <label>Current Files:</label>
                                    <div id="currentFilesList"></div>
                                </div>
            
                                <div class="form-group">
                                    <label for="newFiles">Upload New Files </label>
                                    <input type="file" class="form-control" id="newFiles" name="new_files[]" multiple>
                                </div>
                                
                                  <div class="progress mt-3" id="editProgress" style="display: none;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                         aria-valuemin="0" aria-valuemax="100">0%
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       <!-- Upload Files Modal -->
        <div class="modal fade" id="addFolderModal" tabindex="-1" aria-labelledby="addFolderModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFolderModalLabel">Upload Files</h5>
                    </div>
                    <div class="modal-body body-modal">
                        <div class="d-flex justify-content-center mb-4">
                            <h5 class="m-0">
                                <strong>Instructions:</strong>
                                Upload the files related to your teaching courses. All input fields with the symbol (<span style="color: red;">*</span>) are required. Only <strong>PDF</strong> and <strong>image</strong> files is accepted. 
                                Please make sure to submit all the requirements related to the subject.
                            </h5>
                        </div>
            
                        <form id="uploadForm" action="<?php echo e(route('files.store')); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="folder_name_id" value="">
                
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="position-relative">
                                        <label for="semester">
                                            <strong>Select Semester </strong> <span style="color: red;">*</span>
                                        </label>
                                        <select class="form-control mb-2" name="semester" id="semester" required>
                                            <option value="">Select Semester</option>
                                            <?php $__currentLoopData = $availableSemesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($semester); ?>"><?php echo e($semester); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 70%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative">
                                        <label for="school_year">
                                            <strong>Select School Year </strong> <span style="color: red;">*</span>
                                        </label>
                                        <select class="form-control mb-2" name="school_year" id="school_year" required>
                                            <option value="">Select School Year</option>
                                            <?php $__currentLoopData = $availableSchoolYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schoolYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($schoolYear); ?>"><?php echo e($schoolYear); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 70%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="position-relative mt-1">
                                <label for="classroom_folder">
                                    <strong>Select Documents </strong> <span style="color: red;">*</span>
                                </label>
                                <select class="form-control mb-2" name="classroom_folder" id="classroom_folder" required>
                                    <option value="">Select Documents</option>
                                    <?php $__currentLoopData = $folders->where('main_folder_name', 'Classroom Management'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($folder->folder_name_id); ?>"><?php echo e($folder->folder_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <i class="fas fa-chevron-down position-absolute" style="right: 25px; top: 70%; transform: translateY(-50%); pointer-events: none;"></i>
                            </div>
                            
                            <!-- Course container for dynamic content -->
                            <div id="course-container">
                               <div class="alert alert-info text-center mt-3 mb-4" role="alert">
                                    <h4 class="mb-0">Please select a semester and school year to view available schedules.</h4>
                                </div>

                            </div>
                               
                            <div class="progress mt-3 d-none" id="uploadProgress">
                                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="uploadButton">Submit</button>
                            </div>
                        </form>
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
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.js"></script>
    
  <script>
    $(document).ready(function() {
        // Parse the data from the server safely with fallbacks
        let availableSemesters = [];
        let availableSchoolYears = [];
        let allSchedules = [];
        
        try {
            availableSemesters = JSON.parse('<?php echo json_encode($availableSemesters ?? [], 15, 512) ?>');
            availableSchoolYears = JSON.parse('<?php echo json_encode($availableSchoolYears ?? [], 15, 512) ?>');
            allSchedules = JSON.parse('<?php echo json_encode($courseSchedules ?? [], 15, 512) ?>');
        } catch (e) {
            console.error("Error parsing JSON data:", e);
        }
        
        console.log("Available Semesters:", availableSemesters);
        console.log("Available School Years:", availableSchoolYears);
        console.log("Course Schedules:", allSchedules);
        
        // Function to update available documents based on what's already uploaded
        function updateAvailableDocuments(semester, schoolYear) {
            const periodKey = semester + '_' + schoolYear;
            let uploadedFoldersByPeriod = {};
            
            try {
                uploadedFoldersByPeriod = JSON.parse('<?php echo json_encode($uploadedFoldersByPeriod ?? [], 15, 512) ?>');
            } catch (e) {
                console.error("Error parsing uploadedFoldersByPeriod:", e);
            }
            
            const uploadedFolders = uploadedFoldersByPeriod[periodKey] || [];
            
            console.log("Uploaded folders for period", periodKey, ":", uploadedFolders);
            
            // Reset all options
            $('#classroom_folder option').removeAttr('disabled').show();
            
            // Disable already uploaded folders for this period
            if (uploadedFolders.length > 0) {
                uploadedFolders.forEach(folderId => {
                    $('#classroom_folder option[value="' + folderId + '"]').attr('disabled', 'disabled').hide();
                });
            }
        }
        
        // Function to display schedules based on selected semester and school year
        function displaySchedules(semester, schoolYear) {
            console.log("Displaying schedules for:", semester, schoolYear);
            
            // Filter schedules based on the selected semester and school year
            const filteredSchedules = allSchedules.filter(schedule => 
                schedule.semester === semester && schedule.school_year === schoolYear
            );
            
            console.log("Filtered schedules:", filteredSchedules);
            
            // Clear the existing course container
            const courseContainer = $('#course-container');
            courseContainer.empty();
            
            if (filteredSchedules && filteredSchedules.length > 0) {
                // Create a container for the schedules
                const scheduleContainer = $('<div id="scheduleContainer"></div>');
                courseContainer.append(scheduleContainer);
                
                // Display each filtered schedule
                filteredSchedules.forEach(schedule => {
                    const scheduleCard = `
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="file${schedule.course_code}" style="display: inline-block; margin-bottom: 0;">
                                        <strong>Subject: </strong> <span style="color: red;">*</span> ${schedule.course_subjects}<br>
                                        <strong>Subject Code:</strong> ${schedule.course_code}<br>
                                    </label>
                                    <input type="file" class="form-control mb-2 mt-2 w-100"
                                           id="fileInput${schedule.course_code}"
                                           name="files[${schedule.course_subjects}][]" 
                                           multiple
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           required>
                                    <div id="preview${schedule.course_code}" class="preview-container"></div>
                                </div>
                            </div>
                        </div>
                    `;
                    scheduleContainer.append(scheduleCard);
                });
            } else {
                // Display "No schedules available" message
                courseContainer.html(`
                   <div class="alert alert-warning text-center mt-3 mb-4" role="alert">
                        This year and semester is not active or has no schedule yet.
                    </div>
                `);
            }
        }
        
        // Add event listeners for semester and school year dropdowns
        $('#semester, #school_year').on('change', function() {
            const semester = $('#semester').val();
            const schoolYear = $('#school_year').val();
            
            console.log("Selection changed - Semester:", semester, "School Year:", schoolYear);
            
            if (semester && schoolYear) {
                displaySchedules(semester, schoolYear);
                updateAvailableDocuments(semester, schoolYear);
            }
        });
        
        // Initial check if values are already selected (in case of page reload)
        const initialSemester = $('#semester').val();
        const initialSchoolYear = $('#school_year').val();
        
        if (initialSemester && initialSchoolYear) {
            console.log("Initial values - Semester:", initialSemester, "School Year:", initialSchoolYear);
            displaySchedules(initialSemester, initialSchoolYear);
            updateAvailableDocuments(initialSemester, initialSchoolYear);
        }
    
    
        // File upload preview functionality
        $(document).on('change', 'input[type="file"]', function() {
            const inputId = $(this).attr('id');
            const previewId = inputId.replace('fileInput', 'preview');
            const previewContainer = $(`#${previewId}`);
            
            previewContainer.empty();
            
            if (this.files && this.files.length > 0) {
                for (let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    const fileType = file.type;
                    const fileName = file.name;
                    
                    let iconClass = 'fas fa-file';
                    if (fileType.includes('pdf')) {
                        iconClass = 'fas fa-file-pdf text-danger';
                    } else if (fileType.includes('image')) {
                        iconClass = 'fas fa-file-image text-primary';
                    }
                    
                    const filePreview = `
                        <div class="file-preview">
                            <div class="file-info">
                                <i class="${iconClass}"></i>
                                <span>${fileName}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-file" data-file-index="${i}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    previewContainer.append(filePreview);
                }
            }
        });
        
        // Remove file functionality
        $(document).on('click', '.remove-file', function() {
            const fileIndex = $(this).data('file-index');
            const inputId = $(this).closest('.preview-container').attr('id').replace('preview', 'fileInput');
            const input = $(`#${inputId}`)[0];
            
            // Create a new DataTransfer object
            const dt = new DataTransfer();
            for (let i = 0; i < input.files.length; i++) {
                if (i !== fileIndex) {
                    dt.items.add(input.files[i]);
                }
            }
            input.files = dt.files;
            
            // Update the preview
            $(this).closest('.file-preview').remove();
        });
    });

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
            noDataCell.colSpan = 7;
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
    
        const dataTable = $('#courseTable').DataTable();
        
        dataTable.columns(3).search('');
        
        let filterString = '';
        if (selectedSemester && selectedSchoolYear) {
            filterString = `^${selectedSemester}\\s${selectedSchoolYear}$`;
        }
        
        dataTable
            .column(3)
            .search(filterString, true, false)
            .column(2)
            .search(selectedFolder === 'Select Documents' ? '' : selectedFolder)
            .draw();
    }
    
    const dataTable = $('#courseTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 15, 20, -1], [10, 15, 20, "All"]],
        columnDefs: [
            { 
                targets: [3],
                type: 'string' 
            }
        ]
    });
    
    semesterFilter.addEventListener('change', filterTable);
    schoolYearFilter.addEventListener('change', filterTable);
    if (folderFilter) folderFilter.addEventListener('change', filterTable);
});

//progress
$(document).ready(function() {
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#uploadProgress').removeClass('d-none');
        $('#uploadButton').text('Submitting...').prop('disabled', true);
        
        // Create FormData object
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                var xhr = new XMLHttpRequest();
                
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        var percent = Math.round((e.loaded / e.total) * 100);
                        $('#uploadProgress .progress-bar')
                            .css('width', percent + '%')
                            .attr('aria-valuenow', percent)
                            .text(percent + '%');
                    }
                });
                
                return xhr;
            },
            success: function(response) {
                $('#uploadProgress .progress-bar')
                    .css('width', '0%')
                    .attr('aria-valuenow', 0)
                    .text('0%');
                $('#uploadProgress').addClass('d-none');
                $('#uploadButton').text('Submit').prop('disabled', false);
                
                location.reload(); 
            },
            error: function(xhr, status, error) {
                $('#uploadProgress .progress-bar')
                    .css('width', '0%')
                    .attr('aria-valuenow', 0)
                    .text('0%');
                $('#uploadProgress').addClass('d-none');
                $('#uploadButton').text('Submit').prop('disabled', false);
                
                alert('An error occurred during upload. Please try again.');
            }
        });
    });
});

//edit documents
$('.edit-files-btn').on('click', function() {
    const fileId = $(this).data('id');
    const subject = $(this).data('subject');
    const files = $(this).closest('tr').find('td:nth-child(5) a');
    
    $('#subject').text(subject);
    $('#fileId').val(fileId);
    
    const $currentFilesList = $('#currentFilesList').empty();
    files.each(function(index) {
        $currentFilesList.append(`
            <div class="col-6 mb-2">
                <div class="border rounded px-3 py-1 d-flex align-items-center" 
                     style="background-color: #f8f9fa; border-color: #ddd; position: relative; padding-right: 25px;">
                    <span class="mr-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        ${$(this).text()}
                    </span>
                    <button type="button" class="btn btn-link text-danger p-0 remove-current-file" 
                            data-index="${index}" 
                            style="position: absolute; top: -5px; right: -5px; background: white; border-radius: 50%; width: 18px; height: 18px; display: flex; justify-content: center; align-items: center; border: 1px solid #ddd;">
                        <i class="fas fa-times" style="font-size: 12px;"></i>
                    </button>
                </div>
            </div>
        `);
    });
    
    $('#currentFilesList').addClass('row');
    if ($currentFilesList.children().length === 1) {
        $currentFilesList.find('.remove-current-file').prop('disabled', true);
    }
    $('.remove-current-file').on('click', function() {
        if ($currentFilesList.children().length > 1) {
            $(this).closest('div').remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Remove Last File',
                text: 'You cannot remove the last file.'
            });
        }
    });
    
    $('#editFileModal').modal('show');
});

$('#saveChanges').on('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to update the files?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',  
        cancelButtonColor: '#d33',      
        confirmButtonText: 'Yes, Update!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData($('#editFileForm')[0]);

            const remainingFileIndices = [];
            $('#currentFilesList > div').each(function() {
                remainingFileIndices.push($(this).find('button').data('index'));
            });
            formData.append('remaining_file_indices', JSON.stringify(remainingFileIndices));

            // Show the progress bar
            $('#editProgress').show();
            const progressBar = $('#editProgress .progress-bar');

            $.ajax({
                url: '/update-files',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();

                    // Progress event handler
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = Math.round((e.loaded / e.total) * 100);
                            progressBar.css('width', percentComplete + '%');
                            progressBar.attr('aria-valuenow', percentComplete);
                            progressBar.text(percentComplete + '%');
                        }
                    }, false);

                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        progressBar.css('width', '100%').text('100%');

                        setTimeout(() => {
                            $('#editFileModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Files Updated',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        }, 500);  
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                        $('#editProgress').hide();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'An error occurred'
                    });
                    $('#editProgress').hide();
                }
            });
        }
    });
});

//archive documents
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
            text: `Do you want to archive all approved files created between ${startDate} and ${endDate}?`,
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

//edit files close button
$(document).ready(function() {
    $('#editFileModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });
    
    $('.close, [data-dismiss="modal"]').on('click', function() {
        $('#editFileModal').modal('hide');
    });
});

//messages for declined
document.addEventListener('DOMContentLoaded', function() {
    const messagesModal = document.getElementById('messagesModal');
    const messageContainer = messagesModal.querySelector('.message-container');
    let messageForm = document.getElementById('messageForm');
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.view-messages-btn');
        if (button) {
            const coursesFilesId = button.dataset.id;
            const declinedReason = button.dataset.declinedReason;
            const currentUserId = <?php echo e(auth()->id()); ?>;
            const facultyRole = '<?php echo e(auth()->user()->role); ?>';
            const relevantMessages = JSON.parse('<?php echo json_encode($messages, 15, 512) ?>').filter(
                msg => msg.courses_files_id == coursesFilesId
            );
            messageContainer.innerHTML = `
                ${relevantMessages.map(msg => {
                    const isCurrentUserMessage = msg.user_login_id === currentUserId && facultyRole === 'faculty';
                    
                    return `
                        <div class="d-flex ${isCurrentUserMessage ? 'justify-content-end' : 'justify-content-start'} mb-2">
                            <div class="alert ${isCurrentUserMessage ? 'bg-primary text-white' : 'alert-secondary text-dark'} p-2 rounded" style="max-width: 70%;">
                                ${!isCurrentUserMessage ? `<div class="font-weight-bold small mb-1" style="font-weight:bold; font-size:13px;">${msg.user_login.first_name} ${msg.user_login.surname}</div>` : ''}
                                <div>${msg.message_body}</div>
                                <div class="small mt-1">${formatDate(msg.created_at)}</div>
                            </div>
                        </div>
                    `;
                }).join('')}
            `;
            messageForm.courses_files_id.value = coursesFilesId;
            $(messagesModal).modal('show');
        }
    });
    function formatDate(createdAt) {
        const now = new Date();
        const messageDate = new Date(createdAt);
        const timeDifference = now - messageDate;
        const oneDay = 24 * 60 * 60 * 1000;
        if (timeDifference < oneDay) {
            const seconds = Math.floor(timeDifference / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            if (seconds < 1) return '1 second ago';
            if (seconds < 60) return `${seconds} second${seconds !== 1 ? 's' : ''} ago`;
            if (minutes < 60) return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
            if (hours < 24) return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
        }
        return messageDate.toLocaleString('en-US', { 
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', 
            hour: '2-digit', minute: '2-digit', hour12: true 
        });
    }
    document.addEventListener('submit', function(event) {
        if (event.target.id === 'messageForm') {
            event.preventDefault();
            const formData = Object.fromEntries(new FormData(event.target).entries());
            const facultyRole = '<?php echo e(auth()->user()->role); ?>';
            fetch('<?php echo e(route("send.file.message")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.error || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    messageContainer.innerHTML += `
                        <div class="d-flex ${facultyRole === 'faculty' ? 'justify-content-end' : 'justify-content-start'} mb-2">
                            <div class="alert ${facultyRole === 'faculty' ? 'bg-primary text-white' : 'alert-secondary text-dark'} p-2 rounded" style="max-width: 70%;">
                                <div>${data.message.message_body}</div>
                                <div class="small mt-1">${formatDate(data.message.created_at)}</div>
                            </div>
                        </div>
                    `;
                    event.target.reset();
                    messageContainer.scrollTop = messageContainer.scrollHeight;
                } else {
                    alert('Failed to send: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Send failed: ' + error.message);
            });
        }
    });
    $('.close').on('click', function() {
        $('#messagesModal').modal('hide');
    });
});

//select documents filter
document.addEventListener('DOMContentLoaded', function() {
    const semesterSelect = document.getElementById('semester');
    const schoolYearSelect = document.getElementById('school_year');
    const classroomFolderSelect = document.getElementById('classroom_folder');
    
    // Parse the PHP data into JavaScript
    const uploadedFoldersByPeriod = JSON.parse('<?php echo json_encode($uploadedFoldersByPeriod, 15, 512) ?>');
    const allFolders = JSON.parse('<?php echo json_encode($allFolders, 15, 512) ?>');
    
    function updateAvailableFolders() {
        const selectedSemester = semesterSelect.value;
        const selectedSchoolYear = schoolYearSelect.value;
        const periodKey = `${selectedSemester}_${selectedSchoolYear}`;
        
        // Clear current options
        classroomFolderSelect.innerHTML = '<option value="">Select Documents</option>';
        
        if (!selectedSemester || !selectedSchoolYear) {
            return;
        }
        
        // Get uploaded folders for this period
        const uploadedFolders = uploadedFoldersByPeriod[periodKey] || [];
        
        // Filter and add available folders
        allFolders.forEach(folder => {
            if (!uploadedFolders.includes(folder.folder_name_id)) {
                const option = new Option(folder.folder_name, folder.folder_name);
                classroomFolderSelect.add(option);
            }
        });
    }
    
    // Add event listeners
    semesterSelect.addEventListener('change', updateAvailableFolders);
    schoolYearSelect.addEventListener('change', updateAvailableFolders);
});
</script>
</body>

</html>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/faculty/accomplishment/uploaded-files.blade.php ENDPATH**/ ?>