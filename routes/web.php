<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CoursesFileController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\RoleAuthenticate;


Route::get('/', [RoleController::class, 'showLoginForm'])->name('login');
Route::post('/login', [RoleController::class, 'login'])->name('login.post');
Route::get('/', [RoleController::class, 'showLoginForm'])->name('login.form')->middleware(\App\Http\Middleware\PreventBackHistory::class);
Route::post('/login', [RoleController::class, 'login'])->name('login.post')->middleware(\App\Http\Middleware\PreventBackHistory::class);

Route::middleware(['auth'])->group(function () {
    
    /****************************************FACULTY**************************************/
    Route::get('/faculty-accomplishment', [FacultyController::class, 'accomplishmentPage'])->name('faculty.faculty-accomplishment'); //login page form
    Route::post('/logout', [FacultyController::class, 'facultyLogout'])->name('logout'); //logout
    Route::get('/accomplishment/uploaded-files/{folder_name_id}', [FacultyController::class, 'showUploadedFiles'])->name('faculty.accomplishment.uploaded-files');
    Route::get('/faculty-info', [FacultyController::class, 'getFacultyInfo']);
    Route::post('/accomplishment/uploaded-files', [CoursesFileController::class, 'store'])->name('files.store');
    Route::get('/files/semester/{semester}', [CoursesFileController::class, 'getFilesBySemester']);
    Route::put('/files/update', [CoursesFileController::class, 'update'])->name('files.update');
    


    /*****************************************ADMIN***************************************/
   
    Route::get('/admin-accomplishment', [AdminController::class, 'accomplishmentPage'])->name('admin.admin-accomplishment');
    Route::get('/accomplishment/admin-uploaded-files/{folder_name_id}', [AdminController::class, 'showAdminUploadedFiles'])->name('admin.accomplishment.admin-uploaded-files');
    Route::get('/accomplishment/view-accomplishment/{user_login_id}/{folder_name_id}', [AdminController::class, 'viewAccomplishmentFaculty'])->name('admin.accomplishment.view-accomplishment');
    Route::get('/file/approve/{courses_files_id}', [FileController::class, 'approve'])->name('approveFile');
    Route::post('/file/decline/{courses_files_id}', [FileController::class, 'decline'])->name('declineFile');

    //Maintenance
    Route::get('/maintenance/create-folder', [MaintenanceController::class, 'folderMaintenancePage'])->name('admin.maintenance.create-folder');
    Route::post('/maintenance/store-folder', [MaintenanceController::class, 'storeFolder'])->name('admin.maintenance.store-folder');
    Route::put('/maintenance/create-folder/update-folder/{folder_name_id}', [MaintenanceController::class, 'updateFolder'])->name('admin.maintenance.update-folder');
    Route::delete('/maintenance/create-folder/delete-folder/{folder_name_id}', [MaintenanceController::class, 'deleteFolder'])->name('admin.maintenance.delete-folder');

    Route::get('/admin-dashboard', [DashboardController::class, 'adminDashboardPage'])->name('admin.admin-dashboard');

    //Announcement
     //Announcement
     Route::get('/announcement/admin-announcement', [AnnouncementController::class, 'showAnnouncementPage'])->name('admin.announcement.admin-announcement');
     Route::get('/announcement/add-announcement', [AnnouncementController::class, 'showAddAnnouncementPage'])->name('admin.announcement.add-announcement');
     Route::post('/announcement/add-announcement', [AnnouncementController::class, 'saveAnnouncement'])->name('admin.announcement.save-announcement');
     // Display the edit form
     Route::get('admin/announcement/edit/{id_announcement}', [AnnouncementController::class, 'editAnnouncement'])->name('admin.announcement.edit-announcement');
     // Update the announcement
     Route::post('admin/announcement/update/{id_announcement}', [AnnouncementController::class, 'updateAnnouncement'])->name('admin.announcement.update-announcement');
     // Delete the announcement
     Route::delete('admin/announcement/delete/{id_announcement}', [AnnouncementController::class, 'deleteAnnouncement'])->name('admin.announcement.delete-announcement');
 
     Route::get('admin/announcement/publish/{id_announcement}', [AnnouncementController::class, 'publishAnnouncement'])->name('admin.announcement.publish-announcement');
     Route::get('admin/announcement/unpublish/{id_announcement}', [AnnouncementController::class, 'unpublishAnnouncement'])->name('admin.announcement.unpublish-announcement');
});
