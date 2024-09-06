<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\AuthAssignedUserProjectMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Route::middleware(['auth', 'project.assigned'])->group(function () {    

    Route::resource('projects', ProjectController::class)
        ->parameters(['projects' => 'project'])
        ->names('webprojects');

    Route::post('/projects/{id}/tasks', [TaskController::class, 'create'])->name('tasks.createTask');
    Route::get('/projects/{project}/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/projects/{id}/tasks/store', [TaskController::class, 'store'])->name('tasks.storeTask');
    Route::get('/projects/{id}/tasks/{task}/show', [TaskController::class, 'show'])->name('tasks.showTask');
    Route::get('/projects/{id}/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.editTask');
    Route::delete('/projects/{id}/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroyTask');
    Route::put('/projects/{id}/tasks/{task}', [TaskController::class, 'update'])->name('tasks.updateTask');

});

//Se generan las rutas de navegacion para el modulo de Autenticacion
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');