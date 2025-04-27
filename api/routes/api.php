<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::post('/tasks/store', [TaskController::class, 'store']);