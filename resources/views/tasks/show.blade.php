@extends('layouts.app') <!-- Extiende de la plantilla base -->
<link rel="stylesheet" href="{{ asset('css/detail-projects.css') }}">

@section('content')
    <div class="container">
        <div class="project-detail">

            <h2>Detalles de la Tarea</h2>

            <div class="container-form">
                <div class="form-column">
                    <form action="{{ route('tasks.editTask', [$project_id, $task->id]) }}">
                        
                        <button type="submit" class="btn btn-primary">Editar</button>
                    </form>
                </div>
                
                <div class="form-column">
                    <form action="{{ route('tasks.destroyTask', [$project_id, $task->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
            
            <h3 class="project-title">{{ $task->title }}</h3>

            <div class="project-description">
                <p>Descripción : {{ $task->description }}</p>
            </div>

            <p>Completada : {{ $task->completed == 1 ? 'Completada' : 'No Completada' }}</p>

        </div>        
    </div>
@endsection
