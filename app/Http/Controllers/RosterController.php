<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use App\Data\Staff;
use App\Http\Requests\AssignShiftRequest;
use App\Services\EligibleEmployees;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RosterController extends Controller
{
    public function index(RosterStore $store): View
    {
        $roster = $store->all();
        $employeesById = [];

        foreach (Staff::all() as $employee) {
            $employeesById[$employee->id] = $employee;
        }

        foreach ($roster as $day => $shifts) {
            foreach ($shifts as $shift => $employeeId) {
                $roster[$day][$shift] = $employeeId === null
                    ? null
                    : ($employeesById[$employeeId] ?? null);
            }
        }

        return view('roster.index', [
            'roster' => $roster,
            'title' => $store->title(),
        ]);
    }

    public function assign(
        AssignShiftRequest $request,
        RosterStore $store
    ): RedirectResponse {
        $store->assign(
            $request->validated('day'),
            $request->validated('shift'),
            (int) $request->validated('employee_id')
        );

        return redirect()
            ->route('roster.index')
            ->with('status', 'Turno asignado correctamente.');
    }

    public function remove(
        Request $request,
        RosterStore $store
    ): RedirectResponse {
        $validated = $request->validate([
            'day' => ['required', 'in:' . implode(',', Shifts::DAYS)],
            'shift' => ['required', 'in:' . implode(',', Shifts::names())],
        ]);

        $store->remove(
            $validated['day'],
            $validated['shift']
        );

        return redirect()
            ->route('roster.index')
            ->with('status', 'Turno eliminado correctamente.');
    }

    public function saveTitle(
        Request $request,
        RosterStore $store
    ): RedirectResponse {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
        ], [
            'title.required' => 'Debes introducir un título.',
            'title.max' => 'El título no puede superar los 100 caracteres.',
        ]);

        $store->setTitle(trim($validated['title']));

        return redirect()
            ->route('roster.index')
            ->with('status', 'Título del cuadrante actualizado correctamente.');
    }

    public function eligible(
        Request $request,
        RosterStore $store,
        EligibleEmployees $service
    ): View {
        $validated = $request->validate([
            'day' => ['required', 'in:' . implode(',', Shifts::DAYS)],
            'shift' => ['required', 'in:' . implode(',', Shifts::names())],
        ]);

        $day = $validated['day'];
        $shift = $validated['shift'];

        $occupied = ($store->all()[$day][$shift] ?? null) !== null;

        return view('roster.eligible', [
            'day' => $day,
            'shift' => $shift,
            'occupied' => $occupied,
            'employees' => $service->forShift($day, $shift),
        ]);
    }

    public function autofill(
        Request $request,
        RosterStore $store,
        EligibleEmployees $service
    ): RedirectResponse {
        $validated = $request->validate([
            'day' => ['required', 'in:' . implode(',', Shifts::DAYS)],
            'shift' => ['required', 'in:' . implode(',', Shifts::names())],
        ]);

        $day = $validated['day'];
        $shift = $validated['shift'];

        if (($store->all()[$day][$shift] ?? null) !== null) {
            return redirect()
                ->route('roster.index')
                ->with('status', 'El turno ya está ocupado.');
        }

        $eligible = $service->forShift($day, $shift);

        if (count($eligible) === 0) {
            return redirect()
                ->route('roster.index')
                ->with('status', 'No hay empleados disponibles para este turno.');
        }

        $employee = $eligible[0];

        $store->assign($day, $shift, $employee->id);

        return redirect()
            ->route('roster.index')
            ->with(
                'status',
                'Turno rellenado automáticamente con ' . $employee->name . '.'
            );
    }

    public function summary(RosterStore $store): View
    {
        $roster = $store->all();
        $hoursByEmployee = [];
        $countsByShift = [];
        $emptyShifts = [];
        $daysWithoutResponsible = [];

        foreach (Staff::all() as $employee) {
            $hoursByEmployee[$employee->id] = [
                'employee' => $employee,
                'hours' => 0,
            ];
        }

        foreach (Shifts::names() as $shift) {
            $countsByShift[$shift] = 0;
        }

        foreach (Shifts::DAYS as $day) {
            $hasResponsible = false;

            foreach (Shifts::names() as $shift) {
                $employeeId = $roster[$day][$shift] ?? null;

                if ($employeeId === null) {
                    $emptyShifts[] = [
                        'day' => $day,
                        'shift' => $shift,
                    ];
                    continue;
                }

                $countsByShift[$shift]++;

                if (isset($hoursByEmployee[$employeeId])) {
                    $hoursByEmployee[$employeeId]['hours'] += Shifts::hours($shift);

                    if (
                        $hoursByEmployee[$employeeId]['employee']->role
                        === 'responsable'
                    ) {
                        $hasResponsible = true;
                    }
                }
            }

            if (!$hasResponsible) {
                $daysWithoutResponsible[] = $day;
            }
        }

        $overtimeEmployees = [];

        foreach ($hoursByEmployee as $row) {
            if ($row['hours'] > $row['employee']->contractHours) {
                $overtimeEmployees[] = $row;
            }
        }

        return view('roster.summary', [
            'hoursByEmployee' => $hoursByEmployee,
            'countsByShift' => $countsByShift,
            'daysWithoutResponsible' => $daysWithoutResponsible,
            'emptyShifts' => $emptyShifts,
            'overtimeEmployees' => $overtimeEmployees,
        ]);
    }
}