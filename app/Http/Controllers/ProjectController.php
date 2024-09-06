<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\ProjectService;
use App\Services\TaskService;
use GuzzleHttp\Promise\TaskQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public $projectService;
    public $taskService;

    //Se inyecta el servicio ProjectService a traves de la inyección de dependencias
    public function __construct(ProjectService $projectService, TaskService $taskService)
    {
        //Se le inyecta en la variable local $this->projectService
        $this->projectService = $projectService;
        $this->taskService = $taskService;
    }

    public function index()
    {
        //Si no esta autenticado se redirecciona al login
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        //Se obtiene la lista de Proyectos por usuario desde el servicio "projectService->listProjectUser" 
        $projectList = $this->projectService->listProjectUser();
        return view('projects.index', compact('projectList'));
    }
    //Retorna la vista para crear un proyecto
    public function create()
    {
        if (!Auth::check())
            return redirect()->route('login');
        return view('projects.create-project');
    }

    //Retorna la vista para ver el detalle de un proyecto y sus tareas asociadas si tiene
    public function showProject(Request $request, $project_id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        //Se obtiene el proyecto desde la BD y la lista de tareas
        $project = Project::find($project_id);

        if ($project === null)
            return redirect()->route('webprojects.index');

        $taskList = $project->tasks;

        return view('projects.show', [
            'project' => $project, //retorna el proyecto            
            'taskList' => $taskList //retorna las tareas asociadas al proyecto
        ]);
    }

    //Muestra la vista para la edicion de un proyecto
    public function edit(Project $project)
    {
        if (!Auth::check())
            return redirect()->route('login');
        return view('projects.edit-project', compact('project'));
    }

    //Crear el proyecto y asociarlo al usuario logueado
    public function store(Request $request)
    {
        if (Auth::check()) {
            // El usuario aun está autenticado, se continua con el flujo de creacion de un proyecto

            $request->validate([
                'name' => [
                    //Regla de validacion para que el "name" del proyecto sea unico en cada usuario autenticado
                    //Se hace necesario consultar la tabla pivote project_user por el "name" de proyecto y el "user_id"
                    'required',
                    'min:3',
                    'max:100',
                    Rule::unique('project_user', 'name')->where(function ($query) {
                        return $query->where('user_id', auth()->id());
                    })
                ],
                'description' => 'min:3|max:255',
                'due_date' => 'required|date|after_or_equal:today'
            ]);

            //Crear el proyecto en el servicio "projectService->store" y obtener la referencia para asignarle a la vista
            $project = $this->projectService->store($request);

            //retornar la vista lista de proyectos
            return redirect()->route('webprojects.index', $project);
        }
    }

    //Actualizar el proyecto y devolver a la vista de detalles
    public function update(Request $request, Project $project)
    {
        if (!Auth::check())
            return redirect()->route('login');

        $this->updateProject($request, $project->id);
        //Retornar la vista detalles del proyecto
        return redirect()->route('webprojects.show', $project);
    }

    //Eliminar un proyecto
    public function destroy(Project $project)
    {
        if (!Auth::check())
            return redirect()->route('login');

        $project->delete();
        return redirect()->route('webprojects.index');
    }

    /**********************************EndPoint de la APi****************************/

    public function listProjects(Request $request)
    {
        //return "listado de proyectos por usuario";
        $projectList = $this->projectService->listProjectUser($request);

        return response()->json([
            'success' => true,
            'projectList' => $projectList
        ], 200);
    }

    public function createProject(Request $request)
    {
        if (Auth::check()) {
            // El usuario está autenticado, se continua con el flujo de creacion de un proyecto  
            $validator = Validator::make($request->all(), [
                'name' => [
                    //Regla de validacion para que el "name" del proyecto sea unico en cada usuario autenticado
                    //Se hace necesario consultar la tabla pivote project_user por el "name" de proyecto y el "user_id"
                    'required',
                    'min:3',
                    'max:100',
                    Rule::unique('project_user', 'name')->where(function ($query) {
                        return $query->where('user_id', auth()->id());
                    })
                ],
                'description' => 'min:3|max:255',
                'due_date' => 'required|date|after_or_equal:today'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422); // 422 Unprocessable Entity
            }

            $projectList = $this->projectService->store($request);
            return response()->json([
                'success' => true,
                'project' => $projectList
            ], 200);
        }
        return response()->json([
            'message' => 'Usuario no autorizado',
            'success' => false
        ], 401);
    }

    public function updateProject(Request $request, $project_id)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Usuario no autorizado',
                'success' => false
            ], 401);
        }

        // Delegar la lógica al servicio
        return $this->projectService->updateProject($request, $project_id);
    }

    public function deleteProject($project_id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Usuario no autorizado', 'success' => false], 401);
        }

        return $this->projectService->deleteProject($project_id);
    }

    public function listTaskProject($project_id)
    {
        if (Auth::check()) {
            $taskList = $this->projectService->listarTaskProject($project_id);
            return response()->json(['success' => true, 'taskList' => $taskList], 200);
        }
        return response()->json(['message' => 'Usuario no autorizado', 'success' => false], 401);
    }

    /*Endpoint de proyectos relacionados con las tareas*/
    public function createTaskProject(Request $request, $project_id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Usuario no autorizado', 'success' => false], 401);
        }

        // Delegar la creación de la tarea al servicio
        return $this->taskService->createTaskForProject($request, $project_id);
    }
}
