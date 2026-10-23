@extends('layouts.app')

@section('title', 'Ficha de empleado')

@section('content')
    <h1>{{ $employee->name }}</h1>

    <p>Rol: {{ $employee->role }}</p>
    <p>Horas de contrato: {{ $employee->contractHours }}</p>

    <h2>Turnos asignados</h2>

    @forelse ($shifts as $shift)
        <p>{{ $shift['day'] }} — {{ $shift['shift'] }}</p>
    @empty
        <p>Este empleado no tiene turnos asignados.</p>
    @endforelse

    <a href="{{ route('employees.index') }}">Volver a empleados</a>
@endsection
