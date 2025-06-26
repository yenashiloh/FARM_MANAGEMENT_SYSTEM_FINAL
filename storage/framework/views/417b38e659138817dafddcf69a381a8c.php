<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>View Uploaded Files</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../../../asset/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="icon" href="<?php echo e(asset('assets/images/pup-logo.png')); ?>" type="image/x-icon">
    <link href="../../../../../asset/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../../../asset/libs/css/style.css">
    <link rel="stylesheet" href="../../../../../asset/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" type="text/css" href="../../../../../asset/vendor/datatables/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../../asset/vendor/datatables/css/buttons.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../../asset/vendor/datatables/css/select.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../../asset/vendor/datatables/css/fixedHeader.bootstrap4.css">
</head>
<style>
    td{
        color: #3c3d43;
    }
    .message {
        word-wrap: break-word;
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
                            <h2 class="pageheader-title">View Uploaded Files</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="<?php echo e(route('admin.accomplishment.department', ['folder_name_id' => $folder_name_id])); ?>"
                                                class="breadcrumb-link">
                                                Department
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item"><a
                                                href="<?php echo e(route('viewAccomplishmentDepartment', [
                                                    'department' => urlencode($department),
                                                    'folder_name_id' => $folder->folder_name_id,
                                                ])); ?>"
                                                class="breadcrumb-link">Faculty</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="<?php echo e(route('admin.accomplishment.view-accomplishment', [
                                                    'user_login_id' => $faculty->user_login_id,
                                                    'folder_name_id' => $folder->folder_name_id,
                                                ])); ?>"
                                                class="breadcrumb-link" style=" color: #3d405c;">View Uploaded Files</a>
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
                <?php echo $__env->make('partials.admin-header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="row">
                    <!-- ============================================================== -->
                    <!-- data table  -->
                    <!-- ============================================================== -->
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"> <?php echo e($folderName); ?> (an academic document that communicates
                                    information about a specific course and
                                    explains the rules, responsibilities, and expectations associated with it.)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select id="semesterFilter" class="form-control">
                                            <option value="">Filter Semester</option>
                                            <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($semester); ?>"><?php echo e($semester); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute"
                                            style="right: 25px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="schoolYearFilter" class="form-control">
                                            <option value="">Filter School Year</option>
                                            <?php $__currentLoopData = $schoolYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <i class="fas fa-chevron-down position-absolute"
                                            style="right: 25px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                    </div>
                                </div>

                                <?php if(session('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo e(session('success')); ?>

                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <?php if(session('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php echo e(session('error')); ?>

                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                                
                               <div class="table-responsive">
                                <div class="table-responsive">
                                    <table id="example" class="table table-striped table-bordered second" style="width:100%">
                                        <thead>
                                            <tr>
                                                 <th>Faculty Name</th>
                                                <th>Subject</th>
                                                <th>Created Date</th>
                                                 <th>Academic Year</th>
                                                <th>File</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                            <tbody id="fileTableBody">
                                                <?php $index = 1; ?>
                                                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr data-semester="<?php echo e($file->semester); ?>" data-school-year="<?php echo e($file->school_year); ?>">
                                                            <td><?php echo e($file->userLogin->first_name); ?> <?php echo e($file->userLogin->surname); ?></td>
                                                            <td>
                                                              <?php echo e($file->subject ?? 'N/A'); ?>

                                                            </td>
                                                            <td><?php echo e($file->created_at->timezone('Asia/Manila')->format('F j, Y, g:i A')); ?></td>
                                                            
                                                            <td><?php echo e($file->semester ?? 'N/A'); ?> <?php echo e($file->school_year ?? 'N/A'); ?></td>
                                                            <td>
                                                                <?php
                                                                    $filesData = is_string($file->files) ? json_decode($file->files, true) : $file->files;
                                                                    $originalNames = is_string($file->original_file_name) 
                                                                        ? json_decode($file->original_file_name, true) 
                                                                        : $file->original_file_name;
                                                                ?>
                                                                
                                                                <?php if(is_array($filesData)): ?>
                                                                    <?php $__currentLoopData = $filesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $fileData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php
                                                                            $filePath = is_array($fileData) ? $fileData['path'] : $fileData;
                                                                            $fileName = $originalNames[$index] ?? (is_array($fileData) ? $fileData['name'] : basename($fileData));
                                                                        ?>
                                                                        <div class="mb-1">
                                                                            <a href="<?php echo e(Storage::url($filePath)); ?>" 
                                                                               target="_blank" 
                                                                               style="text-decoration: underline; color: #3c3d43;">
                                                                                <?php echo e(Str::limit($fileName, 10, '...')); ?>

                                                                            </a>
                                                                        </div>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if($file->status === 'To Review'): ?>
                                                                    <?php echo e($file->status); ?>

                                                                <?php elseif($file->status === 'Declined' || $file->status === 'Approved'): ?>
                                                                    <?php echo e($file->status); ?>

                                                                    <?php if(!empty($file->declined_reason)): ?> 
                                                                        <div class="small text-danger">
                                                                            <button type="button" 
                                                                                class="btn btn-link btn-sm view-messages-btn" 
                                                                                data-id="<?php echo e($file->courses_files_id); ?>"
                                                                                data-folder-id="<?php echo e($folder_name_id); ?>"
                                                                                data-declined-reason="<?php echo e($file->declined_reason); ?>">
                                                                                View Messages
                                                                            </button>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </td>

                                                            <td>
                                                               <div class="d-flex justify-content-start">
                                                                    <?php if($file->status === 'To Review'): ?>
                                                                        <a href="javascript:void(0)" 
                                                                            onclick="confirmApproval('<?php echo e(route('approveFile', ['courses_files_id' => $file->courses_files_id])); ?>')"
                                                                            class="btn btn-success btn-sm mb-2 mr-2">
                                                                            Approve
                                                                        </a>
                                                                        <button type="button" class="btn btn-danger btn-sm mb-2 mr-2" data-toggle="modal" data-target="#declineModal"
                                                                                data-id="<?php echo e($file->courses_files_id); ?>">
                                                                            Decline
                                                                        </button>
                                                                    <?php elseif($file->status === 'Approved'): ?>
                                                                        <button type="button" class="btn btn-warning btn-sm mb-2 mr-2" onclick="undoApproval('<?php echo e(route('undoApproval', ['courses_files_id' => $file->courses_files_id])); ?>')">
                                                                            Undo Approval
                                                                        </button>
                                                                    <?php elseif($file->status === 'Declined'): ?>
                                                                        <button type="button" class="btn btn-warning btn-sm mb-2 mr-2" onclick="undoDeclined('<?php echo e(route('undoDeclined', ['courses_files_id' => $file->courses_files_id])); ?>')">
                                                                            Undo Declined
                                                                        </button>
                                                                    <?php endif; ?>

                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Message Modal -->
                            <div class="modal fade" id="messagesModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Messages</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body p-3" style="background-color: #f9f9f9;">
                                            <div class="message-container mb-3" style="max-height: 400px; overflow-y: auto;"></div>
                                            <form id="messageForm" class="mt-3 d-flex align-items-center">
                                                <input type="hidden" name="courses_files_id">
                                                <input type="hidden" name="folder_name_id" value="<?php echo e($folder_name_id); ?>">
                                                <textarea name="message_body" class="form-control mr-2" rows="1" required placeholder="Type your message..." style="resize: none;"></textarea>
                                                <button type="submit" class="btn btn-primary">Send</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    
                            <!-- Decline Modal -->
                            <div class="modal fade" id="declineModal" tabindex="-1" role="dialog"
                                aria-labelledby="declineModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="declineModalLabel">Decline File</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form id="declineForm" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="courses_files_id" id="declineModalId">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="declineReason">Decline Reason</label>
                                                    <textarea class="form-control" id="declineReason" name="declineReason" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-danger">Decline</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- ============================================================== -->
            <!-- end data table  -->
            <!-- ============================================================== -->
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        $(document).ready(function() {
            $('button[data-target="#declineModal"]').on('click', function() {
                var fileId = $(this).data('id');
                var actionUrl = "<?php echo e(route('declineFile', ['courses_files_id' => ':courses_files_id'])); ?>"
                    .replace(':courses_files_id', fileId);
    
                $('#declineModalId').val(fileId);
                $('#declineForm').attr('action', actionUrl);
            });
    
            $('#declineForm').on('submit', function() {
                $('#declineModal').modal('hide');
            });
    
            setTimeout(function() {
                $('#successAlert').fadeOut('slow');
            }, 3000);
    
            setTimeout(function() {
                $('#errorAlert').fadeOut('slow');
            }, 3000);
        });
    
        // semesters filter
        document.addEventListener('DOMContentLoaded', function() {
            const semesterFilter = document.getElementById('semesterFilter');
            const schoolYearFilter = document.getElementById('schoolYearFilter');
            const tableBody = document.getElementById('fileTableBody');
    
            function filterTable() {
                const selectedSemester = semesterFilter.value.trim();
                const selectedSchoolYear = schoolYearFilter.value.trim();
                let visibleIndex = 1;
    
                const rows = tableBody.getElementsByTagName('tr');
    
                Array.from(rows).forEach(row => {
                    const rowSemester = row.getAttribute('data-semester').trim();
                    const rowSchoolYear = row.getAttribute('data-school-year').trim();
    
                    const semesterMatch = !selectedSemester || rowSemester === selectedSemester;
                    const schoolYearMatch = !selectedSchoolYear || rowSchoolYear === selectedSchoolYear;
    
                    if (semesterMatch && schoolYearMatch) {
                        row.style.display = '';
                        const numberCell = row.querySelector('.row-number');
                        if (numberCell) {
                            numberCell.textContent = visibleIndex++;
                        }
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
    
            semesterFilter.addEventListener('change', filterTable);
            schoolYearFilter.addEventListener('change', filterTable);
    
            filterTable();
        });
    
        // confirmation approve
        function confirmApproval(approvalUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to approve this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = approvalUrl;
                }
            });
        }
        
        // confirmation for Undo Declined
       function undoDeclined(undoDeclinedUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to undo the decline of this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, undo it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = undoDeclinedUrl;
                }
            });
        }
            
        // confirmation Undo
        function undoApproval(approvalUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to undo the approval for this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, undo it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = approvalUrl;
                }
            });
        }

        //message Faculty
       document.addEventListener('DOMContentLoaded', function() {
            const messagesModal = document.getElementById('messagesModal');
            const messageContainer = messagesModal.querySelector('.message-container');
            let messageForm = document.getElementById('messageForm');
        
            $('.close').on('click', function() {
                $('#messagesModal').modal('hide');
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
        
                    if (seconds < 1) {
                        return '1 second ago';  // Fix: show "1 second ago" if less than 1 second
                    } else if (seconds < 60) {
                        return `${seconds} second${seconds !== 1 ? 's' : ''} ago`;
                    } else if (minutes < 60) {
                        return `${minutes} minute${minutes !== 1 ? 's' : ''} ago`;
                    } else if (hours < 24) {
                        return `${hours} hour${hours !== 1 ? 's' : ''} ago`;
                    }
                }
        
                return messageDate.toLocaleString('en-US', { 
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', 
                    hour: '2-digit', minute: '2-digit', hour12: true 
                });
            }
        
            function setupMessageButtons() {
                document.querySelectorAll('.view-messages-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const coursesFilesId = this.dataset.id;
                        const declinedReason = this.dataset.declinedReason;
        
                        const relevantMessages = <?php echo json_encode($messages, 15, 512) ?>.filter(
                            msg => msg.courses_files_id == coursesFilesId
                        );
        
                        messageContainer.innerHTML = `
                            ${relevantMessages.map(msg => {
                                const isAdmin = msg.user_login.role === 'admin';
                                const alignment = isAdmin ? 'justify-content-end' : 'justify-content-start';
                                const style = isAdmin ? 'bg-primary text-white' : 'alert alert-secondary';
                                const nameDisplay = !isAdmin ? `<div class="font-weight-bold small mb-1">${msg.user_login.first_name} ${msg.user_login.surname}</div>` : '';
        
                                return `
                                    <div class="d-flex ${alignment} mb-2">
                                        <div class="message p-2 rounded ${style}" style="max-width: 70%;">
                                            ${nameDisplay}
                                            <div>${msg.message_body}</div>
                                            <div class="small mt-1">${formatDate(msg.created_at)}</div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        `;
        
                        messageForm.courses_files_id.value = coursesFilesId;
                        const newMessageForm = messageForm.cloneNode(true);
                        messageForm.parentNode.replaceChild(newMessageForm, messageForm);
                        messageForm = newMessageForm;
        
                        setupMessageForm(newMessageForm);
                        $(messagesModal).modal('show');
                    });
                });
            }
        
            function setupMessageForm(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = Object.fromEntries(new FormData(this).entries());
        
                    fetch('<?php echo e(route("send.file.message.admin")); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageContainer.innerHTML += `
                                <div class="d-flex justify-content-end mb-2">
                                    <div class="message p-2 rounded bg-primary text-white" style="max-width: 70%;">
                                        <div>${data.message.message_body}</div>
                                        <div class="small mt-1">${formatDate(data.message.created_at)}</div>
                                    </div>
                                </div>
                            `;
                            this.reset();
                            messageForm.courses_files_id.value = formData.courses_files_id;
                            messageContainer.scrollTop = messageContainer.scrollHeight;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to send message');
                    });
                });
            }
        
            setupMessageButtons();
        
            $('#messagesModal').on('hidden.bs.modal', function () {
                setupMessageButtons();
            });
        });

        </script>
        <?php echo $__env->make('partials.tables-footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/admin/accomplishment/view-accomplishment.blade.php ENDPATH**/ ?>