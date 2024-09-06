<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function createTask(Request $request)
    {
        return view('tasks.create-task', compact('project_id'));
    }

    public function create(Project $project)
    {
        //return $project;
        return view('tasks.create-task', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        //Se crea la validacion en el controlador y no en un FormRequest porque son pocos campos a validar, de lo contrario
        //debería crearse el FormRequest y modificar el metodo rules de este con las reglas de validación        
        $request->validate([
            'title' => 'required|string|max:255|min:5|unique:tasks,title,id',
            'description' => 'nullable|string',
            'completed' => 'boolean',
        ]);

        $this->taskService->crearTask($request->all());

        //Redireccionar a la ruta donde se muestra la vista del proyecto en el que creamos la tarea
        $projectDetail = Project::find($request->project_id);
        return redirect()->route('webprojects.show', [
            'project' => $projectDetail
        ]);
    }

    public function showTask($project_id, Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit($project_id, Task $task)
    {
        return view('tasks.edit-task', compact(['project_id', 'task']));
    }

    public function update($project_id, Task $task, Request $request)
    {
        $this->taskService->updateTask($request, $task->id);   

        $projectDetail = Project::find($project_id);
        return redirect()->route('webprojects.show', [
            'project' => $projectDetail
        ]);
    }

    public function destroy($project_id, Task $task)
    {
        $this->taskService->deleteTask($task->id);

        $projectDetail = Project::find($project_id);

        return redirect()->route('webprojects.show', [
            'project' => $projectDetail
        ]);
    }

    /**********************************EndPoint de la APi****************************/

    public function updateTask(Request $request, $task_id)
    {

        if (!Auth::check()) {
            return response()->json([
                'message' => 'Usuario no autorizado',
                'success' => false
            ], 401);
        }

        // Llamar al servicio para actualizar la tarea
        return $this->taskService->updateTask($request, $task_id);       
    }

    public function deleteTask($task_id)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Usuario no autorizado',
                'success' => false
            ], 401);
        }

        // Delegar la eliminación de la tarea al servicio
        return $this->taskService->deleteTask($task_id);
    }
}