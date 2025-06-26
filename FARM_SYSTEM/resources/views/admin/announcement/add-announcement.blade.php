<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    @include('partials.admin-header')
    <link rel="stylesheet" href="../../../../asset/vendor/select2/css/select2.css">
    <link rel="stylesheet" href="../../../../asset/vendor/summernote/css/summernote-bs4.css">
    <title>Announcement</title>

</head>

<body>
    @include('partials.admin-sidebar')
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
                                            <a href="{{ route('admin.announcement.admin-announcement') }}"
                                                class="breadcrumb-link">Announcement</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('admin.announcement.add-announcement') }}"
                                                class="breadcrumb-link " style="color: #3d405c;">Add Announcement</a>
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
                            <form method="POST" action="{{ route('admin.announcement.save-announcement') }}"
                                id="announcementForm">
                                @csrf
                                <div class="email-compose-fields">
                                    <div class="to">
                                        <div class="form-group row pt-0">
                                            <label class="col-md-1 control-label">To:</label>
                                            <div class="col-md-11">
                                                <select id="recipientEmails" class="js-example-basic-multiple"
                                                    name="recipient_emails[]" multiple="multiple" required>
                                                    <option value="all-faculty">All Faculty</option>

                                                    <optgroup label="Departments">
                                                        @foreach ($departments as $department)
                                                            <option value="department-{{ $department->department_id }}">
                                                                {{ $department->name }}</option>
                                                        @endforeach
                                                    </optgroup>

                                                    <optgroup label="Faculty Emails">
                                                        @foreach ($facultyUsers as $user)
                                                            <option value="{{ $user->user_login_id }}">
                                                                {{ $user->email }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="subject">
                                        <div class="form-group row pt-2">
                                            <label class="col-md-1 control-label">Subject</label>
                                            <div class="col-md-11">
                                                <input class="form-control" type="text" id="announcement_subject"
                                                    placeholder="Enter subject" name="announcement_subject" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="email editor">
                                    <div class="col-md-12 pl-4 px-4">
                                        <div class="form-group">
                                            <label class="control-label sr-only" for="summernote">Descriptions </label>
                                            <textarea class="form-control" id="summernote" id="announcement-editor" name="announcement_message" rows="6"
                                                placeholder="Write Descriptions" required></textarea>
                                        </div>
                                    </div>
                                    <div class="email action-send">
                                        <div class="col-md-12 pl-4">
                                            <div class="form-group">
                                                <button class="btn btn-primary btn-space" type="submit"><i
                                                        class="icon s7-mail"></i> Send</button>
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
                                <div class="toast-body" data-message="{{ $errors->first() }}"
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
       
//         $(document).ready(function() {
//     const selectElement = $('#recipientEmails');
    
//     selectElement.select2({
//         closeOnSelect: false,
//         tags: true,
//         tokenSeparators: [',', ' '],
//         templateResult: formatOption,
//         templateSelection: formatOptionSelection,
//         width: '100%'  
//     });

//     const originalOptions = {
//         allFaculty: $('option[value="all-faculty"]').clone(),
//         departments: $('optgroup[label="Departments"]').clone(),
//         facultyEmails: $('optgroup[label="Faculty Emails"]').clone()
//     };

//     function formatOption(option) {
//         if (!option.id) return option.text;
//         return $('<span>' + option.text + '</span>');
//     }

//     function formatOptionSelection(option) {
//         if (!option.id) return option.text;
//         return $('<span class="select2-selection-option">' + option.text + '</span>');
//     }

//     selectElement.on('change', function(e) {
//         const selectedValues = $(this).val() || [];
        
//         selectElement.find('option, optgroup').remove();
        
//         if (selectedValues.includes('all-faculty')) {
//             selectElement.append(originalOptions.allFaculty.clone());
//             selectElement.val(['all-faculty']);
//         }
//         else if (selectedValues.some(value => value.startsWith('department-'))) {
//             selectElement.append(originalOptions.departments.clone());
//             selectElement.val(selectedValues.filter(value => value.startsWith('department-'))); 
//         }
//         else if (selectedValues.some(value => !value.startsWith('department-') && value !== 'all-faculty')) {
//             selectElement.append(originalOptions.facultyEmails.clone());
//             selectElement.val(selectedValues.filter(value => !value.startsWith('department-') && value !== 'all-faculty'));
//         }
//         else {
//             selectElement.append(originalOptions.allFaculty.clone())
//                         .append(originalOptions.departments.clone())
//                         .append(originalOptions.facultyEmails.clone());
//         }

//         selectElement.trigger('change.select2');
//     });

//     $(document).on('mousedown', '.select2-selection__choice__remove', function(e) {
//         e.preventDefault();
//         e.stopPropagation();
        
//         const optionValue = $(this).parent().attr('title');
//         const currentSelections = selectElement.val() || [];
        
//         const newSelections = currentSelections.filter(value => {
//             const optionText = selectElement.find('option[value="' + value + '"]').text();
//             return optionText.trim() !== optionValue.trim();
//         });
        
//         selectElement.val(newSelections).trigger('change');
        
//         setTimeout(() => {
//             selectElement.select2('open');
//         }, 0);

//         return false;
//     });

//     selectElement.on('select2:unselect', function(e) {
//         setTimeout(() => {
//             selectElement.select2('open');
//         }, 0);
//     });

//     $('<style>')
//         .text(`
//             .select2-selection__choice {
//                 position: relative;
//                 padding-right: 20px !important;
//                 margin: 4px !important;
//             }
//             .select2-selection__choice__remove {
//                 position: absolute !important;
//                 right: 2px !important;
//                 top: 50% !important;
//                 transform: translateY(-50%) !important;
//                 cursor: pointer !important;
//                 padding: 0 4px !important;
//             }
//             .select2-container--default .select2-selection--multiple {
//                 min-height: 38px;
//             }
//             .select2-container--default .select2-selection--multiple .select2-selection__rendered {
//                 padding: 0 8px;
//             }
//         `)
//         .appendTo('head');
// });

    </script>
</body>

</html>
