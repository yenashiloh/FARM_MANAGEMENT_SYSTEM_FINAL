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
                            <h2 class="pageheader-title">Add Input Field</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                                style="cursor: default; color: #3d405c;">Maintenance</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.maintenance.create-folder') }}"
                                                class="breadcrumb-link">Create Folder</a></li>
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('admin.maintenance.view-file-input', ['folder_input_id' => $folder->folder_name_id]) }}"
                                                class="breadcrumb-link">
                                                View Input Field
                                            </a>
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
                        <div class="card">
                            <div class="card-body">
                                <button type="button" class="btn btn-success mb-4" data-toggle="modal"
                                    data-target="#addInputModal" data-folder-id="{{ $folder->folder_name_id }}"> <i
                                        class="fas fa-plus"></i>
                                    Add Input Field
                                </button>
                                </button>
                                </a>

                                @if (session('success'))
                                    <div id="success-alert"
                                        class="alert alert-success alert-dismissible fade show text-center"
                                        role="alert" aria-label="Close">
                                        {{ session('success') }}
                                    </div>
                                @endif



                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered first">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Input Label</th>
                                                <th>Input Type</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inputs as $index => $input)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $input->input_label }}</td>
                                                    <td>{{ $input->input_type }}</td>
                                                    <td>
                                                        <!-- Single Edit button per row -->
                                                        <div class="d-inline-block">
                                                            <button type="button" class="btn btn-primary btn-warning btn-sm"
                                                                data-toggle="modal" data-target="#updateModal"
                                                                onclick="openUpdateModal({{ $input->folder_input_id }})">
                                                                Edit
                                                            </button>
                                                        </div>

                                                        <!-- Delete button -->
                                                        <div class="d-inline-block">
                                                            <form id="delete-form-{{ $input->folder_input_id }}"
                                                                action="{{ route('folder-inputs.destroy', $input->folder_input_id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                onclick="confirmDelete({{ $input->folder_input_id }})">
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>

                            <!-- Update Modal -->
                            <div class="modal fade" id="updateModal" tabindex="-1" role="dialog"
                                aria-labelledby="updateModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="updateModalLabel">Update Folder Input</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form id="updateForm">
                                            @csrf
                                            <input type="hidden" name="_method" value="PUT">
                                            <div class="modal-body">
                                                <input type="hidden" id="folderInputId" name="folder_input_id">

                                                <div class="form-group">
                                                    <label for="input_label">Input Label</label>
                                                    <input type="text" class="form-control" id="input_label"
                                                        name="input_label" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="input_type">Input Type</label>
                                                    <select class="form-control" id="input_type" name="input_type"
                                                        required>
                                                        <option value="text">Text</option>
                                                        <option value="file">File</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <!-- Add Input Modal -->
                            <div class="modal fade" id="addInputModal" tabindex="-1" role="dialog"
                                aria-labelledby="addInputModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addInputModalLabel">Add Input</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form id="addInputForm" action="{{ route('folder-inputs.store') }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="folder_name_id" id="folder_name_id">
                                                <div class="form-group">
                                                    <label for="input_label">Input Label</label>
                                                    <input type="text" class="form-control" id="input_label"
                                                        name="input_label" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="input_type">Input Type</label>
                                                    <select class="form-control" id="input_type" name="input_type"
                                                        required>
                                                        <option value="text">Text</option>
                                                        <option value="file">File</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Add Input</button>
                                            </div>
                                        </form>
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
                            //add input field
                            $(document).ready(function() {
                                $('#addInputModal').on('show.bs.modal', function(event) {
                                    var button = $(event.relatedTarget);
                                    var folderId = button.data('folder-id');
                                    console.log('Folder ID:', folderId);
                                    var modal = $(this);
                                    modal.find('input[name="folder_name_id"]').val(folderId);
                                });

                                $('#addInputForm').on('submit', function(e) {
                                    e.preventDefault();
                                    var formData = $(this).serialize();
                                    console.log('Form data:', formData);

                                    $.ajax({
                                        url: $(this).attr('action'),
                                        method: 'POST',
                                        data: formData,
                                        success: function(response) {
                                            console.log('Success:', response);
                                            $('#addInputModal').modal('hide');
                                            window.location.href = window.location.href;
                                        },
                                        error: function(xhr) {
                                            console.error('Error:', xhr.responseText);
                                            alert('Error adding input. Please try again.');
                                        }
                                    });
                                });
                            });

                            //delete 
                            function confirmDelete(id) {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: 'You won\'t be able to revert this!',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, delete it!',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        fetch(document.getElementById('delete-form-' + id).action, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                                        'content')
                                                },
                                                body: JSON.stringify({
                                                    _method: 'DELETE'
                                                })
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                Swal.fire(
                                                    'Deleted!',
                                                    data.success,
                                                    'success'
                                                ).then(() => {
                                                    location.reload();
                                                });
                                            })
                                            .catch(error => {
                                                Swal.fire(
                                                    'Error!',
                                                    'An error occurred while deleting the input field.',
                                                    'error'
                                                );
                                            });
                                    }
                                });
                            }

                            //edit
                            function openUpdateModal(id) {
                                fetch(`/folder-inputs/${id}`, {
                                        method: 'GET',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        document.getElementById('folderInputId').value = data.folder_input_id;
                                        document.getElementById('input_label').value = data.input_label;

                                        const inputTypeSelect = document.getElementById('input_type');
                                        for (let i = 0; i < inputTypeSelect.options.length; i++) {
                                            if (inputTypeSelect.options[i].value === data.input_type) {
                                                inputTypeSelect.selectedIndex = i;
                                                break;
                                            }
                                        }

                                        $('#updateModal').modal('show');
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('Failed to fetch folder input data');
                                    });
                            }

                            //update
                            document.getElementById('updateForm').addEventListener('submit', function(e) {
                                e.preventDefault();

                                const id = document.getElementById('folderInputId').value;
                                const formData = new FormData(this);

                                fetch(`/folder-inputs/${id}`, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                                'content'),
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        if (data.success) {
                                            $('#updateModal').modal('hide');
                                            location.reload(); 
                                        } else {
                                            throw new Error('Update failed');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('Failed to update folder input');
                                    });
                            });
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
