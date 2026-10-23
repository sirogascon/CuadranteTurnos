<?php

declare(strict_types=1);

namespace App\Rules;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use App\Data\Staff;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class MinimumRest implements ValidationRule
{
    public const MINIMUM_MINUTES = 12 * 60;

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

        $dayIndex = array_search($this->day, Shifts::DAYS, true);

        if ($dayIndex === false) {
            return;
        }

        $newStart = $dayIndex * 1440 + Shifts::startsAt($this->shift);
        $newEnd = $dayIndex * 1440 + Shifts::endsAt($this->shift);

        foreach ($roster as $day => $shifts) {
            $otherDayIndex = array_search($day, Shifts::DAYS, true);

            if ($otherDayIndex === false) {
                continue;
            }

            foreach ($shifts as $otherShift => $assignedEmployeeId) {
                if ($assignedEmployeeId !== $employeeId) {
                    continue;
                }

                $otherStart = $otherDayIndex * 1440
                    + Shifts::startsAt($otherShift);

                $otherEnd = $otherDayIndex * 1440
                    + Shifts::endsAt($otherShift);

                if ($otherStart <= $newStart) {
                    $rest = $newStart - $otherEnd;
                } else {
                    $rest = $otherStart - $newEnd;
                }

                if ($rest < self::MINIMUM_MINUTES) {
                    $fail('El empleado necesita al menos 12 horas de descanso entre turnos.');
                    return;
                }
            }
        }
    }
}
