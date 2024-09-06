<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=7">

    <title>@yield('title', 'Welcome Page')</title>
    <!--<script src="https://cdn.tailwindcss.com"></script>-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/logout-list-projects.css') }}">
    @stack('css')
</head>

<body>
    <header>
    </header>
    <div>
        <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary">
            Cerrar Sesión
        </a>
        
        @if (Auth::check())
            <a href="{{ route('webprojects.index') }}" class="btn btn-primary">Listado de Proyectos</a>
        @endif

        <div>
            @if (Auth::check())
                <span>Bienvenido, {{ Auth::user()->name }}</span>
                <!-- Formulario oculto -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <span>Bienvenido</span>
            @endif

        </div>
    </div>


    <div>
        @yield('content')
    </div>


    <footer> </footer>
</body>
