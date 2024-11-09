<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.admin-header')
    <title>Upload Schedule</title>
    <style>
        .toggle-dropdown {
            position: absolute;
            margin-left: 20px;
            top: 100%;
            transform: translateX(-100%);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: white;
            border-radius: 4px;
            z-index: 1000;
        }
    </style>
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
                            <h2 class="pageheader-title">Manage Upload Schedule</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#!" class="breadcrumb-link"
                                               >Maintenance</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('admin.maintenance.upload-schedule') }}"
                                                class="breadcrumb-link" style="cursor: default; color: #3d405c;">Upload Schedule</a></li>
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
                        <!-- ============================================================== -->
                        <!-- basic form -->
                        <!-- ============================================================== -->
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="card">
                                <h5 class="card-header"> Upload Schedule</h5>
                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    @if (session('success'))
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                localStorage.setItem('updateButtonDisabled', 'true');

                                                const updateButton = document.getElementById('updateScheduleBtn');
                                                if (updateButton) {
                                                    updateButton.disabled = true;
                                                    updateButton.classList.add('disabled');
                                                }
                                            });
                                        </script>
                                    @endif

                                    <form action="{{ route('upload-schedule.store') }}" id="uploadScheduleForm"
                                        method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="inputStartDate">Start Date</label>
                                                    <input id="inputStartDate" type="date" name="start_date"
                                                        value="{{ old('start_date', $uploadSchedule['start_date'] ?? '') }}"
                                                        required class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputStartTime">Start Time</label>
                                                    <input id="inputStartTime" type="time" name="start_time"
                                                        value="{{ old('start_time', $uploadSchedule['start_time'] ?? '') }}"
                                                        required class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="inputEndDate">End Date</label>
                                                    <input id="inputEndDate" type="date" name="end_date"
                                                        value="{{ old('end_date', $uploadSchedule['end_date'] ?? '') }}"
                                                        required class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="inputStopTime">End Time</label>
                                                    <input id="inputStopTime" type="time" name="stop_time"
                                                        value="{{ old('stop_time', $uploadSchedule['stop_time'] ?? '') }}"
                                                        required class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 mt-3">
                                                <button type="submit" id="updateScheduleBtn"
                                                    class="btn btn-space btn-primary">Update Schedule</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end main wrapper  -->
        <!-- ============================================================== -->


        @include('partials.admin-footer')
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('uploadScheduleForm');
                const updateButton = document.getElementById('updateScheduleBtn');

                function checkScheduleStatus() {
                    const endDate = document.getElementById('inputEndDate').value;
                    const endTime = document.getElementById('inputStopTime').value;

                    if (endDate && endTime) {
                        const scheduleEnd = new Date(endDate + 'T' + endTime);
                        const now = new Date();

                        const isButtonDisabled = localStorage.getItem('updateButtonDisabled');

                        if (isButtonDisabled === 'true') {
                            if (now > scheduleEnd) {
                                updateButton.disabled = false;
                                updateButton.classList.remove('disabled');
                                localStorage.removeItem('updateButtonDisabled');
                            } else {
                                updateButton.disabled = true;
                                updateButton.classList.add('disabled');
                            }
                        }
                    }
                }

                checkScheduleStatus();

                setInterval(checkScheduleStatus, 60000);

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const startTime = document.getElementById('inputStartTime');
                    const stopTime = document.getElementById('inputStopTime');
                    const endDate = document.getElementById('inputEndDate').value;
                    const endTime = document.getElementById('inputStopTime').value;

                    localStorage.setItem('updateButtonDisabled', 'true');

                    updateButton.disabled = true;
                    updateButton.classList.add('disabled');

                    if (startTime.value === startTime.defaultValue) {
                        startTime.disabled = true;
                    }
                    if (stopTime.value === stopTime.defaultValue) {
                        stopTime.disabled = true;
                    }

                    this.submit();
                });

                const isButtonDisabled = localStorage.getItem('updateButtonDisabled');
                if (isButtonDisabled === 'true') {
                    updateButton.disabled = true;
                    updateButton.classList.add('disabled');
                }
            });
        </script>


</body>

</html>
