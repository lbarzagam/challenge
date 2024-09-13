@extends('layouts.app')

@section('content')
<div class="container">

    @if ($errors->any())
    <div>
        <h2>Errores:</h2>
        <ul>
            @foreach ($errors->all() as $error)
                <li>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    <h1>Actualizar Proyecto</h1>
    <form action="{{ route('webprojects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Campo Name -->
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ingresa el nombre de la tarea" 
            required value="{{old('name', $project->name )}}" >
        </div>

        <!-- Campo Description -->
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control" placeholder="Describe la tarea" rows="2">
                {{old('description', $project->description)}}
            </textarea>
        </div>

        <!-- Campo Due Date -->
        <div class="form-group">
            <label for="due_date">Fecha de Entrega</label>
            <input type="date" name="due_date" id="due_date" class="form-control" required 
            value="{{old('due_date', $project->due_date)}}" >
        </div>

        <!-- Botón Submit -->
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection