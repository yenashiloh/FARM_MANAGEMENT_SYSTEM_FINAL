<!DOCTYPE html>
<html lang="en">
<title> {{ $folderName }} </title>
@include('partials.faculty-header')
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
</style>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 col-lg-10 mx-auto">
            <div class="header-container">
                <h5 class="academic">
                    {{ $folderName }} (an academic document that communicates information about a specific course and
                    explains the rules, responsibilities, and expectations associated with it.)
                </h5>
                <a href="{{ route('faculty.faculty-accomplishment') }}" class="btn btn-danger">
                    <i class="fas fa-arrow-left"></i> Back to previous page
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-10 mx-auto">
            <div class="card mt-3">
                <div class="card-body">
                    <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal"
                        data-bs-target="#addFolderModal">
                        <i class="fas fa-plus"></i> Upload Files
                    </a>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table id="dataTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Date & Time</th>
                                <th>Semester</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedFiles as $semester => $files)
                                @if ($files->contains('user_login_id', auth()->id())) 
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::now('Asia/Manila')->locale('en_PH')->format('F j, Y, g:i A') }}</td>
                                        <td>{{ $semester }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#viewFilesModal" data-files="{{ json_encode($files) }}">
                                                View
                                            </button>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editFilesModal" data-files="{{ json_encode($files) }}"
                                                data-semester="{{ $semester }}">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View Files Modal -->
    <div class="modal fade" id="viewFilesModal" tabindex="-1" aria-labelledby="viewFilesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewFilesModalLabel">Uploaded Files</h5>
                </div>
                <div class="modal-body view-modal">
                    <ul id="fileList" class="list-group">
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Files Modal -->
    <div class="modal fade" id="editFilesModal" tabindex="-1" aria-labelledby="editFilesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFilesModalLabel">Edit Files</h5>
                </div>
                <div class="modal-body body-modal">
                    <form id="editFilesForm" action="{{ route('files.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="folder_name_id" value="{{ $folder->folder_name_id }}">
                        <input type="hidden" name="semester" id="editSemester">
                        <div id="editFileInputs"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
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
                        <h6 class="m-0">
                            <strong>Instructions:</strong>
                            Please upload the files related to your teaching courses. All input fields with the symbol
                            (<span style="color: red;">*</span>) are required.
                        </h6>
                    </div>
                    <div class="mb-3">
                        <div class="">
                            <h6 class="mb-3">Current Semester {{ $semester }}</h6>
                        </div>
                    </div>
                    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="folder_name_id" value="{{ $folder->folder_name_id }}">
                        @foreach ($subjects as $index => $subject)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-group ">
                                        <label for="file{{ $index + 1 }}"
                                            style="display: inline-block; margin-bottom: 0;">
                                            <span>
                                                <strong>Subject Code:</strong> {{ $subject['code'] }}
                                                <br>
                                                <strong>Subject:</strong> {{ $subject['name'] }}
                                                <span style="color: red;">*</span>
                                            </span>
                                        </label>
                                        <p>
                                            <span><strong>Year:</strong>
                                                {{ $subject['year_programs'][0]['year'] }}</span>
                                            <br>
                                            <span><strong>Program:</strong>
                                                {{ $subject['year_programs'][0]['program'] }}</span>
                                        </p>

                                        <input type="file" class="form-control-file bordered-file-input"
                                            id="file{{ $index + 1 }}" name="files[]"
                                            accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Upload Files</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertSuccess = document.querySelector('.alert-success');
            const alertDanger = document.querySelector('.alert-danger');

            setTimeout(function() {
                if (alertSuccess) {
                    alertSuccess.classList.add('d-none');
                }
                if (alertDanger) {
                    alertDanger.classList.add('d-none');
                }
            }, 3000);
        });

        //view modal
        document.addEventListener('DOMContentLoaded', function() {
            const viewFilesModal = document.getElementById('viewFilesModal');
            viewFilesModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const files = JSON.parse(button.getAttribute('data-files'));

                console.log(files);
                const fileList = document.getElementById('fileList');
                fileList.innerHTML = '';

                files.forEach(file => {
                    const year = file.year || 'Not specified';
                    const program = file.program || 'Not specified';
                    const code = file.code || 'Not specified';
                    const status = file.status || 'Not specified';
                    const declinedReason = file.declined_reason || '';

                    let badgeClass;
                    let declinedReasonHtml = '';
                    switch (status) {
                        case 'Approved':
                            badgeClass = 'bg-success';
                            break;
                        case 'Declined':
                            badgeClass = 'bg-danger';
                            if (declinedReason) {
                                declinedReasonHtml =
                                    `<br><span><strong>Declined Reason:</strong> ${declinedReason}</span>`;
                            }
                            break;
                        case 'To Review':
                            badgeClass = 'bg-primary';
                            break;
                        default:
                            badgeClass = 'bg-secondary';
                            break;
                    }

                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item';
                    listItem.innerHTML = `
                <span><strong>Status:</strong> <span class="badge ${badgeClass}" style="color: white;">${status}</span>${declinedReasonHtml}</span> 
                <br>
                <span><strong>Subject Code:</strong> ${code}</span>
                <br>
                <span><strong>Subject:</strong> ${file.subject}</span>
                <br>
                <span><strong>Year:</strong> ${year}</span>
                <br>
                <span><strong>Program:</strong> ${program}</span>
                <br>
                <a href="{{ asset('storage') }}/${file.files}" target="_blank">
                    <strong style="color:black;">File:</strong> ${file.original_file_name}
                </a>
            `;
                    fileList.appendChild(listItem);
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
    const editFilesModal = document.getElementById('editFilesModal');
    editFilesModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const files = JSON.parse(button.getAttribute('data-files'));
        const semester = button.getAttribute('data-semester');

        document.getElementById('editSemester').value = semester;

        const fileInputs = document.getElementById('editFileInputs');
        fileInputs.innerHTML = '';

        files.forEach((file, index) => {
            const fileCard = document.createElement('div');
            fileCard.className = 'card mb-3';

            const isDisabled = file.status === 'Approved';
            const newStatus = file.status === 'Declined' ? 'To Review' : file.status;

            fileCard.innerHTML = `
                <div class="card-body">
                    <p class="card-text">
                        <strong>Subject:</strong> ${file.subject}<br>
                        <strong>Code:</strong> ${file.code}<br>
                        <strong>Year:</strong> ${file.year}<br>
                        <strong>Program:</strong> ${file.program}<br>
                    </p>
                    <input type="file" class="form-control-file bordered-file-input mb-2" id="editFile${index + 1}" name="files[${index}]" accept=".pdf,.doc,.docx,.xls,.xlsx" ${isDisabled ? 'disabled' : ''}>
                    <input type="hidden" name="existingFiles[${index}]" value="${file.files}|${escapeHtml(file.original_file_name)}">
                    <input type="hidden" name="subject[${index}]" value="${escapeHtml(file.subject)}">
                    <input type="hidden" name="code[${index}]" value="${escapeHtml(file.code)}">
                    <input type="hidden" name="year[${index}]" value="${escapeHtml(file.year)}">
                    <input type="hidden" name="program[${index}]" value="${escapeHtml(file.program)}">
                    <input type="hidden" name="originalStatus[${index}]" value="${file.status}">
                    <input type="hidden" name="newStatus[${index}]" value="${newStatus}">
                    <small class="form-text text-muted">Current file: ${file.original_file_name}</small>
                </div>
            `;
            fileInputs.appendChild(fileCard);
        });
    });
        });

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }
    </script>

    @include('partials.faculty-footer')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
