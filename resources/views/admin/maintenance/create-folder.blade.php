{{-- <!DOCTYPE html>
<html lang="en">
<title>Maintenance</title>
@include('partials.admin-header')


<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 col-lg-10 mx-auto">
            <div class="header-container">
                <h5 class="academic">
                    Maintenance folder for the accomplishments 
                </h5>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-10 mx-auto">
            <div class="card mt-3">
                <div class="card-body">
                    <button type="button" class="btn btn-success mb-3" data-toggle="modal"
                        data-target="#addFolderModal">
                        <i class="fas fa-plus"></i> Add Folder
                    </button>
                    </a>

                    @if (session('success'))
                        <div id="success-alert" class="alert alert-success alert-dismissible fade show text-center"
                            role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="modal fade" id="addFolderModal" tabindex="-1" role="dialog"
                        aria-labelledby="addFolderModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addFolderModalLabel">Add Sub Folder</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                            <select class="form-control" id="mainFolderName" name="main_folder_name"
                                                required>
                                                <option value="Classroom Management">Classroom Management</option>
                                                <option value="Test Administration">Test Administration</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Folder</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Edit Folder Modal -->
                    <div class="modal fade" id="editFolderModal" tabindex="-1" role="dialog"
                        aria-labelledby="editFolderModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editFolderModalLabel">Edit Folder</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                            <input type="text" class="form-control" id="editFolderName"
                                                name="folder_name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="editMainFolderName">Main Folder Name</label>
                                            <select class="form-control" id="editMainFolderName" name="main_folder_name"
                                                required>
                                                <option value="Classroom Management">Classroom Management</option>
                                                <option value="Test Administration">Test Administration</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



                    <table id="dataTable" class="table table-striped">
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
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editFolderModal" 
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
            </div>
        </div>
    </div>

    @include('partials.admin-footer')

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
    </script> --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    @include('partials.admin-header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <title>Create Folder</title>
</head>

<body>
    @include('partials.admin-sidebar')

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
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link" style="cursor: default; color: #3d405c;">Maintenance</a></li>
                                        <li class="breadcrumb-item"><a href="{{route ('admin.maintenance.create-folder')}}" class="breadcrumb-link">Create Folder</a></li>
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
                    <div class="col-md-8 col-lg-12 mx-auto">
                        <div class="card mt-3">
                            <div class="card-body">
                                <button type="button" class="btn btn-success mb-3" data-toggle="modal"
                                    data-target="#addFolderModal">
                                    <i class="fas fa-plus"></i> Add Folder
                                </button>
                                </a>

                                @if (session('success'))
                                    <div id="success-alert"
                                        class="alert alert-success alert-dismissible fade show text-center"
                                        role="alert">
                                        {{ session('success') }}
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
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Folder</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
                                                        <input type="text" class="form-control" id="editFolderName"
                                                            name="folder_name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="editMainFolderName">Main Folder Name</label>
                                                        <select class="form-control" id="editMainFolderName"
                                                            name="main_folder_name" required>
                                                            <option value="Classroom Management">Classroom Management
                                                            </option>
                                                            <option value="Test Administration">Test Administration
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

                                <table id="dataTable" class="table table-striped">
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
                   
        @include('partials.admin-footer')
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
</body>



</html>
