@extends('layouts.app')

@section('title', 'Empleados elegibles')

@section('content')
    <h1>F08 · Empleados elegibles</h1>

    <p>
        Día: {{ $day }} |
        Turno: {{ $shift }}
    </p>

    @if ($occupied)
        <p>Este turno ya está ocupado.</p>
    @else
        <h2>Empleados que pueden cubrirlo</h2>

        @forelse ($employees as $employee)
            <p>
                {{ $employee->name }}
                — {{ $employee->role }}
                — {{ $employee->contractHours }} horas de contrato
            </p>
        @empty
            <p>
                Ningún empleado cumple las reglas de asignación
                para este turno.
            </p>
        @endforelse

        @if (count($employees) > 0)
            <form method="POST" action="{{ route('roster.autofill') }}">
                @csrf

                <input type="hidden" name="day" value="{{ $day }}">
                <input type="hidden" name="shift" value="{{ $shift }}">

                <button type="submit" class="primario">
                    F09 · Rellenar automáticamente
                </button>
            </form>
        @endif
    @endif

    <p>
        <a href="{{ route('roster.index') }}">Volver al cuadrante</a>
    </p>
@endsection