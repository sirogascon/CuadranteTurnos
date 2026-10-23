<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\RosterStore;
use App\Data\Staff;
use Tests\TestCase;

class F08_F09_OptionalFeaturesTest extends TestCase
{
    public function test_f08_muestra_empleados_elegibles(): void
    {
        $response = $this->get(route('roster.eligible', [
            'day' => 'lunes',
            'shift' => 'manana',
        ]));

        $response->assertOk();
        $response->assertSee('Empleados elegibles');
        $response->assertSee(Staff::all()[0]->name);
    }

    public function test_f08_excluye_empleado_que_ya_trabaja_ese_dia(): void
    {
        $employee = Staff::all()[0];

        $this->app->make(RosterStore::class)
            ->assign('lunes', 'manana', $employee->id);

        $response = $this->get(route('roster.eligible', [
            'day' => 'lunes',
            'shift' => 'tarde',
        ]));

        $response->assertOk();
        $response->assertDontSee($employee->name);
    }

    public function test_f08_excluye_empleado_sin_descanso_suficiente(): void
    {
        $employee = Staff::all()[0];

        $this->app->make(RosterStore::class)
            ->assign('lunes', 'noche', $employee->id);

        $response = $this->get(route('roster.eligible', [
            'day' => 'martes',
            'shift' => 'manana',
        ]));

        $response->assertOk();
        $response->assertDontSee($employee->name);
    }

    public function test_f09_rellena_un_turno_vacio(): void
    {
        $response = $this->post(route('roster.autofill'), [
            'day' => 'lunes',
            'shift' => 'manana',
        ]);

        $response->assertRedirect(route('roster.index'));

        $roster = $this->app->make(RosterStore::class)->all();

        $this->assertNotNull($roster['lunes']['manana']);
    }

    public function test_f09_no_sobrescribe_turno_ocupado(): void
    {
        $employee = Staff::all()[0];

        $this->app->make(RosterStore::class)
            ->assign('lunes', 'manana', $employee->id);

        $response = $this->post(route('roster.autofill'), [
            'day' => 'lunes',
            'shift' => 'manana',
        ]);

        $response->assertRedirect(route('roster.index'));

        $roster = $this->app->make(RosterStore::class)->all();

        $this->assertSame($employee->id, $roster['lunes']['manana']);
    }
}
