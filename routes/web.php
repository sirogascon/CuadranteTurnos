<?php

declare(strict_types=1);

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RosterController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/cuadrante')->name('home');

Route::get('/empleados', [EmployeeController::class, 'index'])
    ->name('employees.index');

Route::get('/empleados/{id}', [EmployeeController::class, 'show'])
    ->whereNumber('id')
    ->name('employees.show');

Route::get('/cuadrante', [RosterController::class, 'index'])
    ->name('roster.index');

Route::post('/cuadrante/asignar', [RosterController::class, 'assign'])
    ->name('roster.assign');

Route::post('/cuadrante/quitar', [RosterController::class, 'remove'])
    ->name('roster.remove');

Route::post('/cuadrante/titulo', [RosterController::class, 'saveTitle'])
    ->name('roster.title');

Route::get('/cuadrante/elegibles', [RosterController::class, 'eligible'])
    ->name('roster.eligible');

Route::post('/cuadrante/autorrellenar', [RosterController::class, 'autofill'])
    ->name('roster.autofill');

Route::get('/resumen', [RosterController::class, 'summary'])
    ->name('roster.summary');