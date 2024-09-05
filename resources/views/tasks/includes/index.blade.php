<div class="container">
    <div class="project-detail">

         {{-- Se verifica que la lista de tareas no sea vacia para renderizarla en la plantilla --}}
        @if (count($taskList) === 0)
            <h3 class="tarea-list">No se ha creado ninguna tarea</h3>
            <a href="{{ route('tasks.create', $project) }}" class="btn btn-primary" >Crear Tarea</a>
            
        @else
            <h3 class="tarea-list">Listado de Tareas</h3>
            <a href="{{ route('tasks.create', $project) }}" class="btn btn-primary" >Crear Tarea</a>

            <ul class="project-list">
                @foreach ($taskList as $task)
                    <li class="project-item">
                        <h3><a href="{{ route('tasks.showTask',[$project->id, $task]) }}">{{ $task->title }}</a></h3>
                        {{--<p>Descripción : {{ $task->description }}</p>
                        <span class="due-date">Completed: {{ $task->completed }}</span>--}}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
