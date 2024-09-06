<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ProjectController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Task;

class AuthAssignedUserProjectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes estar autenticado para acceder a esta ruta.');
        }
        $user = Auth::user();

        // Verificar si la ruta es para un proyecto
        if ($request->is('projects*')) {
            // Obtener el proyecto desde la ruta
            $project = Project::find($request->route('project'));

            // Verificar si el proyecto existe y si el usuario está relacionado con el proyecto
            if (($project != null) && !($project->users()->where('user_id', $user->id)->exists())) {
                return redirect()->route('webprojects.index'); //->with('error', 'No tienes permiso para modificar este proyecto.');
            }

            $task = Task::find($request->route('task'));
            if ($task != null) {
                $project = $task->project;

                if ($project != null) {
                    // Verificar si el usuario es el creador del proyecto o un colaborador asignado
                    if(!($project->users()->where('user_id', $user->id)->exists())){
                    //if (!$project->users->contains($user->id)) {
                        return redirect()->route('webprojects.index')->with('error', 'No tienes permiso para modificar esta tarea.');
                    }
                }
            }
        }
        return $next($request);
    }
}
