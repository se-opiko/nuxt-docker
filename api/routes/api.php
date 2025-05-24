<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\TaskController;

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks/store', [TaskController::class, 'store']);
Route::patch('/tasks/{id}/update', [TaskController::class, 'update']);
Route::delete('/tasks/{id}/destroy', [TaskController::class, 'destroy']);