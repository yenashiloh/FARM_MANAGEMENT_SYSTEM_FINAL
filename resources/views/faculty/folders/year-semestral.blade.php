<!DOCTYPE html>
<html lang="en">
<title>Accomplishments</title>
@include('partials.faculty-header')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 col-lg-10 mx-auto">
            <div class="header-container">
                <h5 class="academic">
                    {{ $folderName }} (an academic document that communicates information about a specific course and
                    explains the rules, responsibilities and expectations associated with it.)
                </h5>
                <a href="{{route ('faculty.faculty-accomplishment')}}" class="btn btn-danger"><i
                        class="fas fa-arrow-left"></i> Back to previous page</a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8 col-lg-10 mx-auto">
            <div class="card mt-3">
                <div class="card-body">
                

                    <table id="dataTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Date & Time</th>
                                <th>Folder Name</th>
                                <th>Uploaded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($folders as $folder)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $folder->created_at->locale('en_PH')->format('F j, Y, g:i A') }}</td>
                                    <td>{{ $folder->folder_name }}</td>
                                    <td>{{ $folder->admin->name ?? 'Unknown' }}</td>
                                    <td>
                                        <a href="{{ route('faculty.accomplishments.folders.view-folders', $folder->year_semestral_id) }}"
                                            class="btn btn-info btn-sm w-45 mb-1">View Folder</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('partials.faculty-footer')
   