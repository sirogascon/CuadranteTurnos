<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use App\Data\Staff;
use App\Rules\MinimumRest;
use App\Rules\NoOverlap;
use Illuminate\Foundation\Http\FormRequest;

final class AssignShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(RosterStore $store): array
    {
        $day = (string) $this->input('day', '');
        $shift = (string) $this->input('shift', '');

        $employeeIds = array_map(
            fn ($employee) => $employee->id,
            Staff::all()
        );

        return [
            'day' => [
                'required',
                'in:' . implode(',', Shifts::DAYS),
            ],
            'shift' => [
                'required',
                'in:' . implode(',', Shifts::names()),
            ],
            'employee_id' => [
                'required',
                'integer',
                'in:' . implode(',', $employeeIds),
                new NoOverlap($store, $day, $shift),
                new MinimumRest($store, $day, $shift),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'day.required' => 'Debes seleccionar un día.',
            'day.in' => 'El día seleccionado no es válido.',
            'shift.required' => 'Debes seleccionar un turno.',
            'shift.in' => 'El turno seleccionado no es válido.',
            'employee_id.required' => 'Debes seleccionar un empleado.',
            'employee_id.integer' => 'El empleado debe ser un número válido.',
            'employee_id.in' => 'El empleado seleccionado no existe.',
        ];
    }
}
