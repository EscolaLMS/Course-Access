<?php

use EscolaLms\CourseAccess\Http\Controllers\CourseAccessAPIController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api'], 'prefix' => 'api/admin'], function () {
    Route::get('courses/{id}/access', [CourseAccessAPIController::class, 'list']);
    Route::post('courses/{id}/access/add', [CourseAccessAPIController::class, 'add']);
    Route::post('courses/{id}/access/remove', [CourseAccessAPIController::class, 'remove']);
    Route::post('courses/{id}/access/set', [CourseAccessAPIController::class, 'set']);
});
