<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Main Requirements</title>
    <link rel="icon" href="{{ asset('assets/images/pup-logo.png') }}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../../asset/vendor/bootstrap/css/bootstrap.min.css">
    <link href="../../../../asset/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../../asset/libs/css/style.css">
    <link rel="stylesheet" href="../../../../asset/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/buttons.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/select.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="../../../../asset/vendor/datatables/css/fixedHeader.bootstrap4.css">
</head>

<body>
    @include('partials.admin-sidebar')
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
                            <h2 class="pageheader-title">Create Folder</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!"
                                                class="breadcrumb-link">Maintenance</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.maintenance.create-folder') }}"
                                                class="breadcrumb-link" style="color: #3d405c;">Create Folder</a></li>
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
                            <div class="card-body">
                                <button type="button" class="btn btn-primary mb-3" data-toggle="modal"
                                    data-target="#addFolderModal">
                                    <i class="fas fa-plus"></i> Add Folder
                                </button>
                                </a>
                                @if (session('success'))
                                    <div id="success-alert"
                                        class="alert alert-success alert-dismissible fade show text-center"
                                        role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show text-center"
                                        role="alert">
                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <div class="modal fade" id="addFolderModal" tabindex="-1" role="dialog"
                                    aria-labelledby="addFolderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addFolderModalLabel">Add Sub Folder</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('admin.maintenance.store-folder') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="folderName">Folder Name</label>
                                                        <input type="text" class="form-control" id="folderName"
                                                            name="folder_name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="mainFolderName">Main Folder Name</label>
                                                        <select class="form-control" id="mainFolderName"
                                                            name="main_folder_name" required>
                                                            <option value="Classroom Management">Classroom Management
                                                            </option>
                                                            <option value="Test Administration">Test Administration
                                                            </option>
                                                            <option value="Syllabus Preparation ">Syllabus Preparation
                                                            </option>
                                                        </select>
                                                        <i class="fas fa-chevron-down position-absolute"
                                            style="right: 25px; top: 75%; transform: translateY(-50%); pointer-events: none;"></i>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save
                                                        Folder</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered first">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Folder Name</th>
                                                <th>Main Folder Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($folders as $index => $folder)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $folder->folder_name }}</td>
                                                    <td>{{ $folder->main_folder_name }}</td>
                                                    <td>
                                                        {{-- <a href="{{ route('admin.maintenance.view-file-input', ['folder_input_id' => $folder->folder_name_id]) }}"
                                                            class="btn btn-info btn-sm">
                                                            View
                                                        </a> --}}
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            data-toggle="modal" data-target="#editFolderModal"
                                                            data-id="{{ $folder->folder_name_id }}"
                                                            data-name="{{ $folder->folder_name }}"
                                                            data-main-folder-name="{{ $folder->main_folder_name }}">
                                                            Edit
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $folder->folder_name_id }})">Delete</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Edit Folder Modal -->
                                <div class="modal fade" id="editFolderModal" tabindex="-1" role="dialog"
                                    aria-labelledby="editFolderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editFolderModalLabel">Edit Folder</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="POST" action="">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <input type="hidden" id="editFolderId" name="folder_name_id">
                                                    <div class="form-group">
                                                        <label for="editFolderName">Folder Name</label>
                                                        <input type="text" class="form-control"
                                                            id="editFolderName" name="folder_name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="editMainFolderName">Main Folder Name</label>
                                                        <select class="form-control" id="editMainFolderName"
                                                            name="main_folder_name" required>
                                                            <option value="Classroom Management">Classroom Management
                                                            </option>
                                                            <option value="Test Administration">Test Administration
                                                            </option>
                                                            <option value="Syllabus Preparation">Syllabus Preparation
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save
                                                        changes</button>
                                                </div>
                                            </form>
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                $('#editFolderModal').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var folderId = button.data('id');
                    var folderName = button.data('name');
                    var mainFolderName = button.data('main-folder-name');

                    var modal = $(this);
                    modal.find('#editFolderId').val(folderId);
                    modal.find('#editFolderName').val(folderName);
                    modal.find('#editMainFolderName').val(mainFolderName);

                    var form = modal.find('form');
                    form.attr('action', '/maintenance/create-folder/update-folder/' + folderId);
                });
            });

            function confirmDelete(folderId) {
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
                        deleteFolder(folderId);
                    }
                });
            }

            function deleteFolder(folderId) {
                $.ajax({
                    url: '/maintenance/create-folder/delete-folder/' + folderId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            xhr.responseJSON.message || 'An error occurred while deleting the folder.',
                            'error'
                        );
                    }
                });
            }
        </script>

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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>



</html>
