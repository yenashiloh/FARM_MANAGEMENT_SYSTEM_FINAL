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
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RoleAuthenticate;
use App\Http\Middleware\DirectorAuthenticate;


Route::get('/', [RoleController::class, 'showLandingPage'])->name('welcome');
Route::get('login', [RoleController::class, 'showLoginForm'])->name('login');
Route::post('/login', [RoleController::class, 'login'])->name('login.post');
// Route::get('/', [RoleController::class, 'showLoginForm'])->name('login.form')->middleware(\App\Http\Middleware\PreventBackHistory::class);
Route::post('/login', [RoleController::class, 'login'])->name('login.post')->middleware(\App\Http\Middleware\PreventBackHistory::class);

Route::middleware(['auth', 'role:faculty', 'prevent-back-history'])->group(function () {
    
    /****************************************FACULTY**************************************/
    Route::get('/faculty-accomplishment', [FacultyController::class, 'accomplishmentPage'])->name('faculty.faculty-accomplishment'); 
    Route::post('/logout', [FacultyController::class, 'facultyLogout'])->name('logout'); 
    Route::get('/accomplishment/uploaded-files/{folder_name_id}', [FacultyController::class, 'showUploadedFiles'])->name('faculty.accomplishment.uploaded-files');
    Route::get('/faculty-info', [FacultyController::class, 'getFacultyInfo']);
    Route::post('/accomplishment/uploaded-files', [CoursesFileController::class, 'store'])->name('files.store');
    Route::get('/files/semester/{semester}', [CoursesFileController::class, 'getFilesBySemester']);
    Route::put('/files/update', [CoursesFileController::class, 'update'])->name('files.update');
    Route::get('/faculty-dashboard', [DashboardController::class, 'facultyDashboardPage'])->name('faculty.faculty-dashboard');
    Route::get('/notifications/count', [NotificationController::class, 'getNotificationCount'])->name('notifications.count');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markNotificationsAsRead'])->name('notifications.mark-read');
    Route::get('/notifications/list', [NotificationController::class, 'getNotificationList'])->name('notifications.list');
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::get('/announcement', [FacultyController::class, 'announcementPage'])->name('faculty.announcement'); 
});

    /************************************ADMIN***************************************/
Route::group(['middleware' => ['auth', 'role:admin', 'prevent-back-history']], function () {

    Route::get('/admin-accomplishment', [AdminController::class, 'accomplishmentPage'])->name('admin.admin-accomplishment');
    Route::get('/accomplishment/admin-uploaded-files/{folder_name_id}', [AdminController::class, 'showAdminUploadedFiles'])->name('admin.accomplishment.admin-uploaded-files');
    Route::get('/accomplishment/view-accomplishment/{user_login_id}/{folder_name_id}', [AdminController::class, 'viewAccomplishmentFaculty'])->name('admin.accomplishment.view-accomplishment');
    Route::get('/file/approve/{courses_files_id}', [FileController::class, 'approve'])->name('approveFile');
    Route::post('/file/decline/{courses_files_id}', [FileController::class, 'decline'])->name('declineFile');
    Route::get('/export/{folder_name_id}', [FileController::class, 'export'])->name('report.export');
    Route::get('/report/export/not-passed/{folder_name_id}', [FileController::class, 'exportNotPassed'])->name('report.export.not_passed');
    Route::delete('/files/{courses_files_id}', [FileController::class, 'destroy'])->name('deleteFile');
    Route::get('/generate-all-reports/{semester}', [FileController::class, 'generateAllReports'])->name('generate.all.reports');

    Route::post('/delete-selected-files', [FileController::class, 'deleteSelectedFiles']);
    Route::post('/delete-all-files', [FileController::class, 'deleteAllFiles']);

    //Maintenance
    Route::get('/maintenance/create-folder', [MaintenanceController::class, 'folderMaintenancePage'])->name('admin.maintenance.create-folder');
    Route::post('/maintenance/store-folder', [MaintenanceController::class, 'storeFolder'])->name('admin.maintenance.store-folder');
    Route::put('/maintenance/create-folder/update-folder/{folder_name_id}', [MaintenanceController::class, 'updateFolder'])->name('admin.maintenance.update-folder');
    Route::delete('/maintenance/create-folder/delete-folder/{folder_name_id}', [MaintenanceController::class, 'deleteFolder'])->name('admin.maintenance.delete-folder');

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
     
     Route::get('/admin/notifications', [NotificationController::class, 'getAdminNotifications'])->name('admin.notifications.get');
     Route::get('/admin/notifications/count', [NotificationController::class, 'getAdminNotificationCount'])->name('admin.notifications.count');
     Route::post('/admin/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.markAsRead');

     Route::post('/admin/notifications/log-click', [NotificationController::class, 'logClick'])->name('admin.notifications.logClick');
     
     //View Admin Account
     Route::get('/admin-account', [AdminController::class, 'adminAccountPage'])->name('admin.admin-account');
     Route::post('/update-account', [AdminController::class, 'updateAccount'])->name('updateAccount');
     Route::post('/logout', [AdminController::class, 'adminLogout'])->name('logout');

    });

    /************************************DIRECTOR***************************************/
Route::group(['middleware' => ['auth', 'role:director', 'prevent-back-history']], function () {

    Route::get('/accomplishment/director-uploaded-files/{folder_name_id}', [DirectorController::class, 'showDirectorUploadedFiles'])->name('director.accomplishment.director-uploaded-files');
    Route::get('/accomplishment/view-faculty-accomplishment/{user_login_id}/{folder_name_id}', [DirectorController::class, 'viewFacultyAccomplishment'])->name('director.accomplishment.view-accomplishment');

    Route::get('/director-dashboard', [DirectorController::class, 'directorDashboardPage'])->name('director.director-dashboard');

    Route::get('/director-account', [DirectorController::class, 'directorAccountPage'])->name('director.director-account');
    Route::post('/update-director-account', [DirectorController::class, 'updateDirectorAccount'])->name('updateDirectorAccount');

    Route::post('/logout-director', [DirectorController::class, 'directorLogout'])->name('logout-director');
});


