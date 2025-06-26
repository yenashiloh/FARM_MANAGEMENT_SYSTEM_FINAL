<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CoursesFileController;
use App\Http\Controllers\RoleController;

Route::get('/callback', [RoleController::class, 'handleProviderCallback'])->name('callback');
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');


Route::middleware(['auth:sanctum'])->group(function () {

    /****************************************FACULTY**************************************/
    Route::get('/faculty-accomplishment', [FacultyController::class, 'accomplishmentPage'])->name('faculty.faculty-accomplishment');
    Route::post('/logout', [FacultyController::class, 'facultyLogout'])->name('logout');
    Route::get('/accomplishment/uploaded-files/{folder_name_id}', [FacultyController::class, 'showUploadedFiles'])->name('faculty.accomplishment.uploaded-files');
    Route::get('/faculty-info', [FacultyController::class, 'getFacultyInfo']);
    Route::post('/accomplishment/uploaded-files', [CoursesFileController::class, 'store'])->name('files.store');
    Route::get('/files/semester/{semester}', [CoursesFileController::class, 'getFilesBySemester']);
    Route::put('/files/update', [CoursesFileController::class, 'update'])->name('files.update');

    /*****************************************ADMIN***************************************/
    Route::get('/admin-accomplishment', [AdminController::class, 'accomplishmentPage'])->name('admin.admin-accomplishment');

    // Maintenance
    Route::get('/maintenance/create-folder', [MaintenanceController::class, 'folderMaintenancePage'])->name('admin.maintenance.create-folder');
    Route::post('/maintenance/store-folder', [MaintenanceController::class, 'storeFolder'])->name('admin.maintenance.store-folder');
    Route::put('/maintenance/create-folder/update-folder/{folder_name_id}', [MaintenanceController::class, 'updateFolder'])->name('admin.maintenance.update-folder');
    Route::delete('/maintenance/create-folder/delete-folder/{folder_name_id}', [MaintenanceController::class, 'deleteFolder'])->name('admin.maintenance.delete-folder');
});
