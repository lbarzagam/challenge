@extends('layouts.app')

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const availableItems = document.getElementById('available-items');
        const selectedItems = document.getElementById('selected-items');
        const addItemButton = document.getElementById('add-item');
        const removeItemButton = document.getElementById('remove-item');

        // Mover item de "Items Disponibles" a "Items Seleccionados"
        addItemButton.addEventListener('click', function() {
            moveItems(availableItems, selectedItems);
        });

        // Mover item de "Items Seleccionados" a "Items Disponibles"
        removeItemButton.addEventListener('click', function() {
            moveItems(selectedItems, availableItems);
        });

        function moveItems(sourceList, destinationList) {
            const selectedOptions = Array.from(sourceList.selectedOptions);

            selectedOptions.forEach(option => {
                destinationList.appendChild(option);
            });
        }
    });
</script>

<style>
    .select-box {
        width: 100%;
        height: 250px;
    }
</style>

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
        <form action="{{ route('webprojects.updateUsers', $project->id) }}" method="POST">
            @csrf
            @method('PUT')
        
            <label for="selected-users">Seleccionar Usuarios:</label>
            <select id="selected-users" name="selectedUsers[]" class="form-control" multiple>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ in_array($user->id, $project->users->pluck('id')->toArray()) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>        
            <button type="submit" class="btn btn-primary mt-3">Asignar Usuarios</button>
            <button type="submit" class="btn btn-primary mt-3">Quitar Usuarios</button>
        </form>
@endsection
