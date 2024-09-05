@extends('layouts.app') <!-- Heredar de la plantilla app.blade.php -->

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

        <h1>Crear Nuevo Proyecto</h1>
        <form action="{{ route('webprojects.store') }}" method="POST">
            @csrf
            <!-- Campo Name -->
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" class="form-control"
                    placeholder="Ingresa el nombre del proyecto" value="{{ old('name') }}">
            </div>

            <!-- Campo Description -->
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" class="form-control" placeholder="Describe el proyecto" rows="2">
                    {{old('description') }}
                </textarea>
            </div>

            <!-- Campo Due Date -->
            <div class="form-group">
                <label for="due_date">Fecha de Entrega</label>
                <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}">
            </div>

            <!-- Botón Submit -->
            <button type="submit" class="btn btn-primary">Crear Proyecto</button>
        </form>
    </div>
@endsection
