<?php

namespace App\Http\Middleware;

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
        if ($request->route('projects')) {
            $project = Project::find($request->route('projects'));

            // Verificar si el usuario es el creador del proyecto
            if ($project && $project->user_id !== $user->id) {
                return redirect()->route('projects.index')->with('error', 'No tienes permiso para modificar este proyecto.');
            }
        }

        // Verificar si la ruta es para una tarea
        if ($request->route('tasks')) {
            $task = Task::find($request->route('tasks'));

            if ($task) {
                $project = $task->project;

                // Verificar si el usuario es el creador del proyecto o un usuario asignado al proyecto
                if ($project->user_id !== $user->id && !$project->users->contains($user->id)) {
                    return redirect()->route('projects.index')->with('error', 'No tienes permiso para modificar esta tarea.');
                }
            }
        }

        return $next($request);
    }
}
