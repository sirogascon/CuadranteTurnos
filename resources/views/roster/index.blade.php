@extends('layouts.app')

@section('title', 'Cuadrante semanal')

@section('content')
    <h1>{{ $title !== '' ? $title : 'Semana sin título' }}</h1>

    <form method="POST" action="{{ route('roster.title') }}" class="asignar">
        @csrf

        <label for="title">Título del cuadrante</label>

        <input
            id="title"
            type="text"
            name="title"
            value="{{ old('title', $title) }}"
            maxlength="100"
            required
        >

        <button class="primario" type="submit">Guardar título</button>
    </form>

    @if ($errors->any())
        <div class="errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rejilla">
        <div></div>

        @foreach (\App\Data\Shifts::DAYS as $day)
            <div class="cab">{{ $day }}</div>
        @endforeach

        @foreach (\App\Data\Shifts::names() as $shift)
            <div class="turno">{{ $shift }}</div>

            @foreach (\App\Data\Shifts::DAYS as $day)
                <x-shift-cell
                    :day="$day"
                    :shift="$shift"
                    :employee="$roster[$day][$shift] ?? null"
                />
            @endforeach
        @endforeach
    </div>

    <h2>Asignar empleado</h2>

    <form method="POST" action="{{ route('roster.assign') }}" class="asignar">
        @csrf

        <label>
            Día
            <select name="day">
                @foreach (\App\Data\Shifts::DAYS as $day)
                    <option value="{{ $day }}" @selected(old('day') === $day)>
                        {{ $day }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Turno
            <select name="shift">
                @foreach (\App\Data\Shifts::names() as $shift)
                    <option value="{{ $shift }}" @selected(old('shift') === $shift)>
                        {{ $shift }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Empleado
            <select name="employee_id">
                @foreach (\App\Data\Staff::all() as $employee)
                    <option
                        value="{{ $employee->id }}"
                        @selected((int) old('employee_id') === $employee->id)
                    >
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </label>

        <button class="primario" type="submit">Asignar</button>
    </form>

    <h2>F08 · ¿Quién puede cubrir un turno?</h2>

    <form method="GET" action="{{ route('roster.eligible') }}" class="asignar">
        <label>
            Día
            <select name="day">
                @foreach (\App\Data\Shifts::DAYS as $day)
                    <option value="{{ $day }}">{{ $day }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Turno
            <select name="shift">
                @foreach (\App\Data\Shifts::names() as $shift)
                    <option value="{{ $shift }}">{{ $shift }}</option>
                @endforeach
            </select>
        </label>

        <button class="primario" type="submit">
            Ver empleados elegibles
        </button>
    </form>
@endsection