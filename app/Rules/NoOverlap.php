<?php

declare(strict_types=1);

namespace App\Rules;

use App\Contracts\RosterStore;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class NoOverlap implements ValidationRule
{
    public function __construct(
        private readonly RosterStore $store,
        private readonly string $day,
        private readonly string $shift,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value)) {
            return;
        }

        $employeeId = (int) $value;
        $roster = $this->store->all();

        foreach ($roster[$this->day] ?? [] as $assignedEmployeeId) {
            if ($assignedEmployeeId === $employeeId) {
                $fail('Este empleado ya tiene un turno asignado ese día.');
                return;
            }
        }
    }
}
