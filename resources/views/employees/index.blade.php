@extends('layouts.app')

@section('title', 'Empleados')

@section('content')
    <h1>Plantilla de empleados</h1>

    <form method="GET" action="{{ route('employees.index') }}">
        <label>Filtrar por rol
            <select name="role">
                <option value="">Todos</option>
                @foreach (\App\Data\Staff::ROLES as $option)
                    <option value="{{ $option }}" @selected($role === $option)>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>Ordenar por
            <select name="sort">
                <option value="name" @selected($sort === 'name')>Nombre</option>
                <option value="hours" @selected($sort === 'hours')>Horas de contrato</option>
            </select>
        </label>

        <button type="submit">Aplicar</button>
    </form>

    <ul>
        @foreach ($employees as $employee)
            <li>
                <a href="{{ route('employees.show', $employee->id) }}">
                    {{ $employee->name }}
                </a>
                — {{ $employee->role }}
                — {{ $employee->contractHours }} horas
            </li>
        @endforeach
    </ul>
@endsection
