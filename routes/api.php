<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\ApiAuthUserProjectMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // return $request->user();

     /*Endpoint para proyectos*/
     Route::get('/projects', [ProjectController::class, 'listProjects']);/**/
     Route::post('projects', [ProjectController::class, 'createProject']);/***/
     Route::put('/projects/{id}', [ProjectController::class, 'updateProject']);/** */
     Route::delete('projects/{id}', [ProjectController::class, 'deleteProject']);/** */
 
     /*Endpoint para tareas*/
     Route::get('projects/{id}/tasks', [ProjectController::class, 'listTaskProject']);/** */
     Route::post('projects/{id}/tasks', [ProjectController::class, 'createTaskProject']);/** */
     Route::put('tasks/{id}', [TaskController::class, 'updateTask']);
     Route::delete('tasks/{id}', [TaskController::class, 'deleteTask']);

});

Route::post('login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('API Token')->plainTextToken;

    return response()->json(['token' => $token]);
});