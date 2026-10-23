@extends('layouts.app')

@section('title', 'Empleado no encontrado')

@section('content')
    <h1>404 — Empleado no encontrado</h1>
    <p>El empleado solicitado no existe.</p>
    <a href="{{ route('employees.index') }}">Volver a empleados</a>
@endsection