<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $folderName }}</title>
    @include('partials.faculty-header')
</head>
<body>
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
                        <table id="dataTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Programs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programs as $index => $program)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $program['name'] }}</td>
                                        <td>
                                            {{-- <a href="{{ route('faculty.folders.program-details', ['program' => $index]) }}" class="btn btn-info">View</a> --}}
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
    @include('partials.faculty-footer')
</body>
</html>