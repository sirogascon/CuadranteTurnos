@extends('layouts.app')

@section('title', 'Resumen semanal')

@section('content')
    <h1>Resumen semanal</h1>

    <h2>Horas por empleado</h2>

    <table>
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Horas asignadas</th>
                <th>Horas de contrato</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hoursByEmployee as $row)
                <tr>
                    <td>{{ $row['employee']->name }}</td>
                    <td>{{ $row['hours'] }}</td>
                    <td>{{ $row['employee']->contractHours }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Personas asignadas por turno</h2>

    <ul>
        @foreach ($countsByShift as $shift => $count)
            <li>{{ $shift }}: {{ $count }}</li>
        @endforeach
    </ul>

    <h2>Avisos de horas extra</h2>

    @forelse ($overtimeEmployees as $row)
        <p>
            Aviso: {{ $row['employee']->name }} tiene
            {{ $row['hours'] }} horas asignadas, por encima de sus
            {{ $row['employee']->contractHours }} horas de contrato.
        </p>
    @empty
        <p>No hay empleados con exceso de horas.</p>
    @endforelse

    <h2>Avisos de turnos vacíos</h2>

    @forelse ($emptyShifts as $empty)
        <p>Aviso: {{ $empty['day'] }} — {{ $empty['shift'] }} no tiene empleado asignado.</p>
    @empty
        <p>Todos los turnos están cubiertos.</p>
    @endforelse

    <h2>Días sin responsable</h2>

    @forelse ($daysWithoutResponsible as $day)
        <p>Aviso: {{ $day }} no tiene ningún responsable asignado.</p>
    @empty
        <p>Todos los días tienen al menos un responsable.</p>
    @endforelse
@endsection
