<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class ProjectService
{
    public function listProjectUser()
    {
        //Se obtiene la referencia del user autenticado para atarlos a los proyectos 
        $user = Auth::user();
        return $user->projects;
    }

    public function getProjectById($project_id)
    {
        $project = Project::find($project_id);
        if (empty($project))
            return "No existe un proyecto con el id $project_id";

        return $project;
    }

    public function store(Request $request)
    {
        //Eliminar despues
        if (!Auth::check()) {
            // El usuario no está autenticado, se envia un mensaje de error 
            $error = [
                'message' => 'Usuario no autorizado',
                'success' => false
            ];
            return $error;
        }

        //comprobó que sigue autenticado
        //Se obtiene la referencia del user autenticado para atarlos a los proyectos
        $user = Auth::user();

        //Crear el projecto y obtener la referencia para asignarle el usuario logueado
        //El create deberia hacerse en una clase repository que herede de una interface para inyectarsela al servicio
        //para poder escalar el proyecto en futuras iteraciones
        $project = Project::create($request->all());

        // Asignar el proyecto al usuario logueado
        $project->users()->attach($user->id, ['name' => $request->name]);

        return $project;
    }

    public function update(Request $request)
    {
         //Eliminar despues
        if (!Auth::check()) {
            // El usuario no está autenticado, se envia un mensaje de error 
            $error = [
                'message' => 'Usuario no autorizado',
                'success' => false
            ];
            return $error;
        }

        //comprobó que sigue autenticado
        //Se obtiene la referencia del user autenticado para atarlos a los proyectos
        $user = Auth::user();

        //Crear el projecto y obtener la referencia para asignarle el usuario logueado
        //El create deberia hacerse en una clase repository que herede de una interface para inyectarsela al servicio
        //y hacer uso del repository, para poder escalar el proyecto en futuras iteraciones
        $project = Project::create($request->all());

        // Asignar el proyecto al usuario logueado
        $project->users()->attach($user->id, ['name' => $request->name]);

        return $project;
    }

    public function listarTaskProject($project_id)
    {
        $project = $this->getProjectById($project_id);
        if (!($project instanceof Project))
            return "No se pudieron obtener las tareas";

        return $project->tasks;
    }

    public function checkIfUserCanBeAction($project)
    {
        // Verificar si el usuario autenticado pertenece a los usuarios del proyecto
        $exist = $project->users()->where('user_id', auth()->id())->exists();
        return $exist;
    }  


    public function updateProject(Request $request, $project_id, $isApi)
    {
        // Validación de los datos del request si es desde la api
        if ($isApi) {
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'min:3',
                    'max:100',
                    Rule::unique('project_user', 'name')
                        ->where(function ($query) {
                            return $query->where('user_id', auth()->id());
                        })
                        ->ignore($project_id, 'project_id') // Ignorar el nombre del proyecto actual
                ],
                'description' => 'min:3|max:255',
                'due_date' => "required|date|after_or_equal:today,{$project_id}"
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
        }

        // Obtener el proyecto
        $project = $this->getProjectById($project_id);

        if (!($project instanceof Project)) {
            return response()->json(['success' => false, 'message' => $project], 404);
        }

        // Verificar si el usuario tiene permisos para actualizar el proyecto
        $exist = $project->users()->where('user_id', auth()->id())->exists();

        if (!$exist) {
            return response()->json([
                'message' => 'Este proyecto no le corresponde. Usted no tiene permisos para actualizarlo',
                'success' => false
            ], 401);
        }

        // Actualizar el proyecto con los datos validados
        $project->update($request->only('name', 'description', 'due_date'));

        // Actualizar la relación en la tabla pivote
        $project->users()->updateExistingPivot(auth()->id(), ['name' => $request->name]);

        return response()->json([
            'success' => true,
            'project' => $project
        ], 200);
    }

    public function deleteProject($project_id)
    {
        //Se obtiene el proyecto desde el servicio
        $project = $this->getProjectById($project_id);
        //Se verifica si la respuesta es una instancia de un objeto, si no lo es se crea una respuesta
        if (!($project instanceof Project)) {
            return response()->json([
                'success' => false,
                'response' => $project
            ], 404);
        }

        $project = $this->getProjectById($project_id);

        if (!($project instanceof Project)) {
            return response()->json(['success' => false, 'message' => 'Proyecto no encontrado']);
        }

        // Verificar si el usuario autenticado pertenece a los usuarios del proyecto
        $exist = $project->users()->where('user_id', auth()->id())->exists();

        if (!$exist) {
            return response()->json([
                'message' => 'Este proyecto no le corresponde. Usted no tiene permisos para eliminarlo',
                'success' => false
            ], 401);
        }
        $project->delete();

        return response()->json([
            'message' => 'El proyecto fue eliminado satisfactoriamente',
            'success' => true,
            'project' => $project
        ], 200);
    }
}
