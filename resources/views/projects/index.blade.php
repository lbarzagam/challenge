@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/list-projects.css') }}">
@section('content')
    <div class="container">

        <h1>Listado de Proyectos</h1>

        <a href="{{ route('webprojects.create') }}" class="btn btn-primary">Nuevo Proyecto</a>
        {{-- Se verifica que la lista de proyectos no sea vacia para renderizarla en la plantilla --}}
        @if (count($projectList) > 0)
            <ul class="project-list">
                @foreach ($projectList as $project)
                    <li class="project-item">
                        <h3><a href="{{ route('webprojects.show', $project) }}">{{ $project->name }}</a></h3>
                        <p>{{ $project->description }}</p>
                        <span class="due-date">Fecha de entrega: {{ $project->due_date }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
