<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CoursesFileController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminEditDetailsController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\MarkAsReadController;
use App\Http\Controllers\FolderInputController;
use App\Http\Controllers\UploadScheduleController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AccomplishmentController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RoleAuthenticate;
use App\Http\Middleware\DirectorAuthenticate;


Route::get('/', [RoleController::class, 'showLandingPage'])->name('welcome');
Route::get('login', [RoleController::class, 'showLoginForm'])->name('login');
Route::post('/login', [RoleController::class, 'login'])->name('login.post');
Route::get('/callback', [RoleController::class, 'handleProviderCallback'])->name('callback');
Route::get('/oauth/redirect', [RoleController::class, 'redirectToProvider'])->name('oauth.redirect');
Route::get('/login/hris', [RoleController::class, 'redirectToProvider'])->name('login.hris');
Route::post('/login', [RoleController::class, 'login'])->name('login.post')->middleware(PreventBackHistory::class);

Route::middleware(['auth', 'role:faculty,faculty-coordinator', 'prevent-back-history'])->group(function () {

    Route::get('/faculty-dashboard', [DashboardController::class, 'facultyDashboardPage'])
                    ->name('faculty.faculty-dashboard');
    Route::post('/faculty-logout', [FacultyController::class, 'facultyLogout'])->name('faculty-logout'); 
    
    Route::get('/accomplishment/classroom-management', [FacultyController::class, 'showUploadedFiles'])->name('faculty.accomplishment.uploaded-files');
    Route::get('/accomplishment/test-administration', [FacultyController::class, 'showTestAdministration'])->name('faculty.accomplishment.test-administration');
    Route::get('/accomplishment/syllabus-preparation', [FacultyController::class, 'showSyllabusPreparation'])->name('faculty.accomplishment.syllabus-preparation');
    
    Route::get('/accomplishment/approved-files', [FacultyController::class, 'showApprovedFiles'])->name('faculty.accomplishment.approved-files');
    Route::get('/accomplishment/declined-files', [FacultyController::class, 'showDeclinedFiles'])->name('faculty.accomplishment.declined-files');
    Route::get('/accomplishment/pending-files', [FacultyController::class, 'showPendingFiles'])->name('faculty.accomplishment.pending-files');
    
    Route::get('/faculty-info', [FacultyController::class, 'getFacultyInfo']);
    Route::post('/accomplishment/uploaded-files', [CoursesFileController::class, 'store'])->name('files.store');
    Route::post('/accomplishment/uploaded-test-administration-files', [CoursesFileController::class, 'storeTestAdministration'])->name('files.store.test-administration');
    Route::post('/accomplishment/uploaded-syllabus', [CoursesFileController::class, 'storeSyllabus'])->name('files.store.syllabus');
    Route::get('/files/semester/{semester}', [CoursesFileController::class, 'getFilesBySemester']);
                
    
    Route::post('/update-files', [CoursesFileController::class, 'updateFiles'])->name('update-files');

    Route::get('/notifications/count', [NotificationController::class, 'getNotificationCount'])->name('notifications.count');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markNotificationsAsRead'])->name('notifications.mark-read');
                Route::get('/notifications/list', [NotificationController::class, 'getNotificationList'])->name('notifications.list');
                Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
                Route::get('/announcement', [FacultyController::class, 'announcementPage'])->name('faculty.announcement'); 
                Route::get('/view-uploaded-files/{user_login_id}/{folder_name_id}/{semester?}', [FacultyController::class, 'viewUploadedFiles'])->name('faculty.accomplishment.view-uploaded-files');
                
                Route::post('/archive-by-date', [CoursesFileController::class, 'archiveByDate'])->name('archive.by.date');
                Route::post('/archive-by-test-administration', [CoursesFileController::class, 'archiveByTestAdministration'])->name('archive.by.test-administration');
                Route::post('/archive-by-syllabus', [CoursesFileController::class, 'archiveBySyllabus'])->name('archive.by.syllabus');
                Route::get('/view-archive', [CoursesFileController::class, 'showArchive'])->name('faculty.view-archive');
                Route::post('/files/unarchive/{courses_files_id}', [CoursesFileController::class, 'unarchive'])->name('files.unarchive');

                Route::post('/files/archiveAll', [CoursesFileController::class, 'archiveAll'])->name('files.archiveAll');
                Route::post('/files/archive-by-date-range', [CoursesFileController::class, 'archiveByDateRange'])->name('files.archiveByDateRange');
                Route::post('/files/bulk-unarchive', [CoursesFileController::class, 'bulkUnarchive'])->name('files.bulkUnarchive');
                Route::post('/log-logout', [AuditController::class, 'logLogout'])->name('log-logout');

                Route::post('/request-upload-access', [FacultyController::class, 'requestUploadAccess'])->name('request.upload.access');

                Route::get('/faculty/announcement/search', [AnnouncementController::class, 'searchFacultyAnnouncements'])->name('faculty.announcement.search');

                Route::post('/remove-file', [CoursesFileController::class, 'removeFile'])->name('remove.file');
                Route::delete('/courses-files/{id}', [CoursesFileController::class, 'destroyFiles'])->name('courses-files.destroy');
                
                Route::post('/add-new-file', [CoursesFileController::class, 'addNewFile']);
                Route::delete('/courses-file/{fileId}', [CoursesFileController::class, 'destroyFacultyFile']);
                
                Route::get('/get-course-schedules', [FacultyController::class, 'getCourseSchedules'])->name('get.course.schedules');
    
    Route::get('/request-upload-access-faculty', [FacultyController::class, 'showRequestUploadFaculty'])->name('faculty.request-upload-access');
    Route::post('/files/archive/{id}', [CoursesFileController::class, 'archiveOneFile'])->name('files.archive');
    
    Route::delete('/faculty/files/{fileId}/delete-single/{fileIndex}', [CoursesFileController::class, 'deleteSingleFile'])
    ->name('faculty.files.delete-single');
    
    Route::post('/send-file-message', [FacultyController::class, 'sendMessageClassroomManagement'])->name('send.file.message');
    
    Route::get('/get-available-folders', [FacultyController::class, 'getAvailableFolders'])->name('get-available-folders');
     
    Route::get('/get-all-folders', 'FacultyController@getAllFolders');
    Route::get('/get-uploaded-folders', 'FacultyController@getUploadedFolders');
    });
    
    /************************************ADMIN***************************************/
Route::group(['middleware' => ['auth', 'role:admin', 'prevent-back-history']], function () {

    //Accomplishment
    Route::get('/admin-accomplishment', [AdminController::class, 'accomplishmentPage'])->name('admin.admin-accomplishment');
    Route::get('/accomplishment/admin-uploaded-files/{folder_name_id}', [AdminController::class, 'showAdminUploadedFiles'])->name('admin.accomplishment.admin-uploaded-files');
    
    Route::get('/accomplishment/department/{folder_name_id}', [AccomplishmentController::class, 'showDepartmentPage'])->name('admin.accomplishment.department');
    Route::get('accomplishment/faculty/{department}/{folder_name_id?}', [AccomplishmentController::class, 'showAccomplishmentDepartment'])->name('viewAccomplishmentDepartment');

    //File Crud
    Route::get('/file/approve/{courses_files_id}', [FileController::class, 'approve'])->name('approveFile');
    Route::post('/file/decline/{courses_files_id}', [FileController::class, 'decline'])->name('declineFile');
    Route::get('/export/{folder_name_id}', [FileController::class, 'export'])->name('report.export');
    Route::get('/report/export/not-passed/{folder_name_id}', [FileController::class, 'exportNotPassed'])->name('report.export.not_passed');
    Route::delete('/files/{courses_files_id}', [FileController::class, 'destroy'])->name('deleteFile');
    Route::get('/undo-approval/{courses_files_id}', [FileController::class, 'undoApproval'])->name('undoApproval');
    Route::get('/file/undo-declined/{courses_files_id}', [FileController::class, 'undoDeclined'])->name('undoDeclined');



    //Generate Report
    Route::get('/generate-all-reports/{semester}', [FileController::class, 'generateAllReports'])->name('generate.all.reports');

    //Maintenance
    Route::get('/maintenance/create-folder', [MaintenanceController::class, 'folderMaintenancePage'])->name('admin.maintenance.create-folder');
    Route::post('/maintenance/store-folder', [MaintenanceController::class, 'storeFolder'])->name('admin.maintenance.store-folder');
    Route::put('/maintenance/create-folder/update-folder/{folder_name_id}', [MaintenanceController::class, 'updateFolder'])->name('admin.maintenance.update-folder');
    Route::delete('/maintenance/create-folder/delete-folder/{folder_name_id}', [MaintenanceController::class, 'deleteFolder'])->name('admin.maintenance.delete-folder');

    //Admin Dashboard
    Route::get('/admin-dashboard', [DashboardController::class, 'adminDashboardPage'])->name('admin.admin-dashboard');

    //Announcement
    Route::get('/announcement/admin-announcement', [AnnouncementController::class, 'showAnnouncementPage'])->name('admin.announcement.admin-announcement');
    Route::get('/announcement/add-announcement', [AnnouncementController::class, 'showAddAnnouncementPage'])->name('admin.announcement.add-announcement');
    Route::post('/announcement/add-announcement', [AnnouncementController::class, 'saveAnnouncement'])->name('admin.announcement.save-announcement');

    //Announcement
    Route::get('admin/announcement/edit/{id_announcement}', [AnnouncementController::class, 'editAnnouncement'])->name('admin.announcement.edit-announcement');
    Route::post('admin/announcement/update/{id_announcement}', [AnnouncementController::class, 'updateAnnouncement'])->name('admin.announcement.update-announcement');
    Route::delete('admin/announcement/delete/{id_announcement}', [AnnouncementController::class, 'deleteAnnouncement'])->name('admin.announcement.delete-announcement');
    Route::get('admin/announcement/publish/{id_announcement}', [AnnouncementController::class, 'publishAnnouncement'])->name('admin.announcement.publish-announcement');
    Route::get('admin/announcement/unpublish/{id_announcement}', [AnnouncementController::class, 'unpublishAnnouncement'])->name('admin.announcement.unpublish-announcement');
     
     //Notification
    Route::get('/admin/notifications', [NotificationController::class, 'getAdminNotifications'])->name('admin.notifications.get');
    Route::get('/admin/notifications/count', [NotificationController::class, 'getAdminNotificationCount'])->name('admin.notifications.count');
    Route::post('/admin/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.markAsRead');
    Route::post('/admin/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.markAllAsRead');
    Route::post('/admin/notifications/log-click', [NotificationController::class, 'logClick'])->name('admin.notifications.logClick');
     
     //View Admin Account
    Route::get('/admin-account', [AdminController::class, 'adminAccountPage'])->name('admin.admin-account');
    Route::post('/update-account', [AdminController::class, 'updateAccount'])->name('updateAccount');
    Route::post('/admin-logout', [AdminController::class, 'adminLogout'])->name('admin-logout');

    //Add Input Field
    Route::post('/folder-inputs', [FolderInputController::class, 'store'])->name('folder-inputs.store');
    Route::delete('/folder-inputs/{id}', [FolderInputController::class, 'destroy'])->name('folder-inputs.destroy');
    Route::get('/maintenance/view-file-input/{folder_input_id}', [FolderInputController::class, 'showInputs'])->name('admin.maintenance.view-file-input');
    Route::put('/folder-inputs/{id}', [FolderInputController::class, 'update'])->name('folder-inputs.update');
    Route::get('/folder-inputs/{id}', [FolderInputController::class, 'show'])->name('folder-inputs.show');

    //Upload Schedule
    Route::get('/maintenance/upload-schedules', [UploadScheduleController::class, 'showUploadSchedule'])
    ->name('admin.maintenance.upload-schedule');
    Route::post('/upload-schedule', [UploadScheduleController::class, 'store'])->name('upload-schedule.store');
    Route::get('/maintenance/upload-schedule/{id}/edit', [UploadScheduleController::class, 'edit'])->name('upload-schedule.edit');
    Route::put('/maintenance/upload-schedules/{uploadSchedule}', [UploadScheduleController::class, 'update'])->name('upload_schedule.update');

    Route::get('/admin/accomplishment/{folder_name_id}', [AdminController::class, 'showAdminUploadedFiles'])->name('admin.accomplishment.show');

    Route::get('/audit-trail', [AdminController::class, 'showAuditTrail'])->name('admin.maintenance.audit-trail');
    Route::get('/request-upload-access', [AdminController::class, 'showRequestUploadAccess'])->name('admin.request-upload-access');
    Route::post('/upload/request', [AdminController::class, 'requestAccess'])->name('upload.request');
    Route::get('/admin/check-new-requests', [AdminController::class, 'checkNewRequests'])->name('check.new.requests');
    Route::get('/admin/real-time-upload-access', [AdminController::class, 'realTimeUploadAccess'])->name('real.time.access');
    Route::post('/admin/mark-requests-as-read', [AdminController::class, 'markRequestsAsRead']);

    //Accomplishment
    Route::get('/all-accomplishment', [AccomplishmentController::class, 'showAccomplishmentPage'])->name('admin.accomplishment.accomplishment');
  
    Route::get('/accomplishment/faculty-accomplishments/{user_login_id}', [AccomplishmentController::class, 'viewFacultyAccomplishments'])->name('faculty.accomplishments');
    Route::get('/accomplishment/view-folder-names/{user_login_id}/{main_folder_name}', [AccomplishmentController::class, 'viewFolderNames'])->name('admin.accomplishment.viewFolderNames');
    Route::get('/accomplishment/view-academic-year/{user_login_id}/{folder_name_id}', [AccomplishmentController::class, 'viewAcademicYear'])->name('admin.accomplishment.viewAcademicYear');
    Route::get('/view-accomplishment/{user_login_id}/{folder_name_id}/{semester?}', [AdminController::class, 'viewAccomplishmentFaculty'])->name('admin.accomplishment.view-accomplishment');
    Route::get('/admin/get-upload-requests', [AdminController::class, 'getUploadRequests'])->name('admin.get-upload-requests');

    Route::get('/admin/announcement/search', [AnnouncementController::class, 'searchAnnouncements'])->name('admin.announcement.search');
    
    Route::get('/submission-tracker', [AdminController::class, 'showSubmissionTracker'])->name('admin.submission-tracker');
    Route::get('/submission-tracker/{user_login_id}', [AdminController::class, 'viewFolder'])->name('admin.view-folder');
    Route::get('/submission-tracker/{user_login_id}/{folder_name}', [AdminController::class, 'viewSubfolder'])
    ->name('admin.view-subfolder');
    Route::get('/admin/send-reminder/{user_login_id}', [AdminController::class, 'sendReminder'])
    ->name('admin.send-reminder');

    Route::post('/approve-upload-request/{id}', [AdminController::class, 'approveUploadRequest'])->name('admin.approve-upload-request');
    
    Route::post('/send-file-message-admin', [AdminController::class, 'sendMessageFaculty'])->name('send.file.message.admin');
    
    //Totals page
    Route::get('/dashboard/users', [AdminController::class, 'showTotalUsers'])->name('admin.dashboard-totals.users');
    Route::get('/dashboard/completed-reviews', [AdminController::class, 'showTotalCompletedReviews'])->name('admin.dashboard-totals.completed-reviews');
    Route::get('/dashboard/pending-review', [AdminController::class, 'showPendingReviewFiles'])->name('admin.dashboard-totals.pending-review');
    Route::get('/dashboard/files-submitted', [AdminController::class, 'showTotalFilesSubmitted'])->name('admin.dashboard-totals.files-submitted');
    });

    /************************************DIRECTOR***************************************/
    Route::group(['middleware' => ['auth', 'role:director', 'prevent-back-history']], function () {

    Route::get('/accomplishment/director-uploaded-files/{folder_name_id}', [DirectorController::class, 'showDirectorUploadedFiles'])->name('director.accomplishment.director-uploaded-files');
    // Route::get('/accomplishment/view-faculty-accomplishment/{user_login_id}/{folder_name_id}', [DirectorController::class, 'viewFacultyAccomplishment'])->name('director.accomplishment.view-accomplishment');

    Route::get('/director/accomplishment/view-accomplishment/{user_login_id}/{folder_name_id}/{semester?}', [DirectorController::class, 'viewFacultyAccomplishment'])
    ->name('director.accomplishment.view-faculty-accomplishment');

    Route::get('/generate-all-reports-director/{semester}', [DirectorController::class, 'generateAllReportsDirector'])->name('generate.all.reports.director');

    Route::get('/director-dashboard', [DirectorController::class, 'directorDashboardPage'])->name('director.director-dashboard');

    Route::get('/director-account', [DirectorController::class, 'directorAccountPage'])->name('director.director-account');
    Route::post('/update-director-account', [DirectorController::class, 'updateDirectorAccount'])->name('updateDirectorAccount');

    Route::post('/logout-director', [DirectorController::class, 'directorLogout'])->name('logout-director');

    Route::get('/director/accomplishment/{folder_name_id}', [DirectorController::class, 'showDirectorUploadedFiles'])->name('director.accomplishment.show');

    Route::get('/director/department/{folder_name_id}', [DirectorController::class, 'showDirectorDepartmentPage'])->name('director.department');

    Route::get('/director/accomplishment/faculty/{department}/{folder_name_id?}', [DirectorController::class, 'showDirectorAccomplishmentDepartment'])->name('view.accomplishment.department');
    
    Route::get('/undo-approval-director/{courses_files_id}', [DirectorController::class, 'undoApprovalDirector'])->name('undoApprovalDirector');
    Route::get('/file/undo-declined-director/{courses_files_id}', [DirectorController::class, 'undoDeclinedDirector'])->name('undoDeclinedDirector');
    
    Route::get('/file/approve-director/{courses_files_id}', [DirectorController::class, 'approveDirector'])->name('approveFileDirector');
    Route::post('/file/decline-director/{courses_files_id}', [DirectorController::class, 'declineDirector'])->name('declineFileDirector');
    
    Route::post('/send-file-message-director', [DirectorController::class, 'sendMessageDirector'])->name('send.file.message.director');
    
    
});


