
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cuadrante de turnos')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <nav>
            <a href="{{ route('roster.index') }}">Cuadrante</a>
            <a href="{{ route('employees.index') }}">Empleados</a>
            <a href="{{ route('roster.summary') }}">Resumen</a>
        </nav>
    </header>

    <main>
        @if (session('success'))
            <p class="mensaje">{{ session('status') }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>
