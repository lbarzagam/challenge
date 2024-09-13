@extends('layouts.app') <!-- Extiende de la plantilla base -->
<link rel="stylesheet" href="{{ asset('css/detail-projects.css') }}">

@section('content')
    <div class="container">
        <div class="project-detail">

            <h2>Detalles del Proyecto</h2>

            <div class="container-form">
                <div class="form-column">
                    <form action="{{ route('webprojects.edit', $project->id) }}">
                        <button type="submit" class="btn btn-primary">Editar</button>
                    </form>
                </div>
                <div class="form-column">
                    <form action="{{ route('webprojects.destroy', $project->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
                <div class="form-column">
                    <form action="{{ route('webprojects.showAssignUser', $project->id) }}">                    
                        <button type="submit" class="btn btn-success">Asignar Usuarios</button>
                    </form>
                </div>
            </div>
           
            
            <h3 class="project-title">{{ $project->name }}</h3>

            <div class="project-description">
                <p>Descripción : {{ $project->description }}</p>
            </div>

            <div class="project-due-date">
                <p>Fecha de Entrega: {{ \Carbon\Carbon::parse($project->due_date)->format('d/m/Y') }}</p>
            </div>

        </div>        
        @if ($project->tasks)
            @include('tasks.includes.index', $project)
        @endif
    </div>
@endsection
