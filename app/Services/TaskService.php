<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskService
{
    public function listTaskProject($project)
    {
        $project = Project::find($project->project_id);
        if (!empty($project))
            return;

        if (!($project instanceof Project))
            return "No se pudieron obtener las tareas";

        return $project->tasks;

        /*$tasks = Task::where('project_id', $project->project_id);
        return $tasks;*/
    }

    public function crearTask($task)
    {
        $taskBd = Task::create($task);
        return $taskBd;
    }

    public function update($taskUpdate, $request)
    {
        return $taskUpdate->update($request->all());
    }

    public function updateTask(Request $request, $task_id, $api)
    {
        // Buscar la tarea con el id proporcionado
        $task = Task::find($task_id);

        if (!($task instanceof Task)) {
            return response()->json([
                'message' => "No existe una tarea con id $task_id",
                'success' => false
            ], 404);
        }

        $project_id = $task->project_id;

        if ($api) {
            // Validar los datos del request
            $validator = Validator::make($request->all(), [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    'min:5',
                    Rule::unique('tasks')->where(function ($query) use ($project_id) {
                        return $query->where('project_id', $project_id);
                    })->ignore($task->id),
                ],
                'description' => 'nullable|string',
                'completed' => 'boolean',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422); // 422 Unprocessable Entity
            }
        }



        // Verificar que el usuario autenticado esté autorizado para actualizar la tarea
        if (!$this->userHasPermit($task)) {
            return response()->json([
                'message' => 'Usted no tiene permisos para actualizar esta tarea',
                'success' => false
            ], 403);  // 403 Forbidden
        }

        // Actualizar la tarea solo con los campos seleccionados
        $task->update($request->only(['title', 'description', 'completed']));

        return response()->json([
            'message' => 'Tarea actualizada satisfactoriamente',
            'success' => true
        ], 200);
    }

    public function deleteTask($task_id)
    {
        // Buscar la tarea por su id
        $task = Task::find($task_id);

        if (!($task instanceof Task)) {
            return response()->json([
                'message' => "La tarea con el id $task_id no fue encontrada",
                'success' => false
            ], 404);
        }

        // Verificar si el usuario autenticado tiene permisos
        if (!$this->userHasPermit($task)) {
            return response()->json([
                'message' => 'Usted no tiene permisos para eliminar esta tarea',
                'success' => false
            ], 403);  // Cambiado a 403 Forbidden
        }

        // Eliminar la tarea
        $task->delete();

        return response()->json([
            'message' => 'Tarea eliminada exitosamente',
            'success' => true
        ], 200);
    }

    public function userHasPermit(Task $task)
    {
        // Verificar si el usuario autenticado pertenece a los usuarios del proyecto
        return $task->project->users()->where('user_id', auth()->id())->exists();
    }

    public function createTaskForProject(Request $request, $project_id)
    {
        // Agregar el project_id al request
        $request['project_id'] = $project_id;

        // Validación de los datos del request
        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:5',
                Rule::unique('tasks')->where(function ($query) use ($project_id) {
                    return $query->where('project_id', $project_id);
                }),
            ],
            'description' => 'nullable|string',
            'completed' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Obtener el proyecto
        $project = Project::find($project_id);

        if (!($project instanceof Project)) {
            return response()->json(['success' => false, 'message' => 'Proyecto no encontrado'], 404);
        }

        // Verificar si el usuario autenticado pertenece al proyecto
        $exist = $project->users()->where('user_id', Auth::id())->exists();

        if (!$exist) {
            return response()->json([
                'message' => 'Este proyecto no le corresponde. Usted no tiene permisos para crear una tarea',
                'success' => false
            ], 401);
        }

        // Crear la tarea asociada al proyecto
        $task = $project->tasks()->create($request->only('title', 'description', 'completed', 'project_id'));

        return response()->json(['success' => true, 'task' => $task], 200);
    }
}
