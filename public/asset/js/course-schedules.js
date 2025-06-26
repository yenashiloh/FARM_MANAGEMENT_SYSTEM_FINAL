document.addEventListener('DOMContentLoaded', function() {
    function fetchCourseSchedules() {
        fetch('/get-course-schedules')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modalBody = document.querySelector('#addFolderModal .modal-body form');
                    if (!modalBody) {
                        console.error('Modal body form not found.');
                        return;
                    }

                    const scheduleContainer = document.createElement('div');
                    scheduleContainer.id = 'scheduleContainer';

                    data.schedules.forEach((schedule, index) => {
                        const card = `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="file${index + 1}" style="display: inline-block; margin-bottom: 0;">
                                            <strong>Subject:</strong> ${schedule.course_subjects}<br>
                                            <strong>Subject Code:</strong> ${schedule.course_code}<br>
                                            <strong>Schedule:</strong> ${schedule.schedule}
                                        </label>
                                        <p>
                                            <strong>Year & Section:</strong> ${schedule.year_section}<br>
                                            <strong>Program:</strong> ${schedule.program}
                                        </p>
                                        <input type="file" 
                                            class="form-control mb-2 w-100"
                                            id="fileInput${schedule.course_schedule_id}"
                                            name="files[${schedule.course_schedule_id}][]"
                                            multiple
                                            accept=".pdf, .doc, .docx, .xls, .xlsx, image/*"
                                            capture
                                            required>
                                        <div id="preview${schedule.course_schedule_id}" class="preview-container"></div>
                                    </div>
                                </div>
                            </div>
                        `;
                        scheduleContainer.insertAdjacentHTML('beforeend', card);
                    });

                    // Find the existing schedule container and replace it
                    const existingContainer = document.querySelector('#scheduleContainer');
                    if (existingContainer) {
                        existingContainer.replaceWith(scheduleContainer);
                    } else {
                        // Insert before the progress bar div
                        const progressBar = document.querySelector('#uploadProgress');
                        if (progressBar) {
                            modalBody.insertBefore(scheduleContainer, progressBar);
                        } else {
                            console.error('Progress bar element not found.');
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching course schedules:', error);
            });
    }

    // Fetch schedules when modal is opened
    const addFolderModal = document.getElementById('addFolderModal');
    if (addFolderModal) {
        addFolderModal.addEventListener('show.bs.modal', fetchCourseSchedules);
    } else {
        console.error('Add Folder Modal not found.');
    }
});
