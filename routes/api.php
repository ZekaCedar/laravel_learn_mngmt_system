<?php

use App\Http\Controllers\DataTablesController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Models\StudentLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/student', [DataTablesController::class, 'getStudentData'])->name('student.data');

Route::get('/show', [DataTablesController::class, 'getSchedule'])->name('schedule.data');

Route::post('/generate-schedule', [DataTablesController::class, 'generateSchedule'])->name('generate-schedule');

Route::apiResource('schedule', ScheduleController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
