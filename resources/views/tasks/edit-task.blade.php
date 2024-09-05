@extends('layouts.app') <!-- Esto asume que tienes una plantilla llamada app.blade.php -->

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

        <h1>Actualizar Tarea</h1>
        <form action="{{ route('tasks.updateTask', [$project_id, $task]) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Campo Name -->
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control"
                    placeholder="Ingresa el titulo de la tarea" value="{{ old('title', $task->title) }}">
            </div>

            <!-- Campo Description -->
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" class="form-control" placeholder="Describe la tarea" rows="2">{{ old('description', $task->description) }}
                </textarea>
            </div>

            <!-- Campo Due Date -->
            <div class="form-group">
                <label for="completed">Completada</label>
                <select name="completed" id="completed" class="form-control"
                    value="{{ old('completed', $task->completed) }}">
                    <option value="0" {{ old('completed') == '0' ? 'selected' : '' }}>No Completado</option>
                    <option value="1" {{ old('completed') == '1' ? 'selected' : '' }}>Completado</option>
                </select>
            </div>
            <!-- Botón Submit -->
            <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
        </form>
    </div>
@endsection
