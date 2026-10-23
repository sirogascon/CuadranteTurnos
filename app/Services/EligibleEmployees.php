<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use App\Data\Staff;
use App\Rules\MinimumRest;
use App\Rules\NoOverlap;
use Illuminate\Support\Facades\Validator;

final class EligibleEmployees
{
    public function __construct(
        private readonly RosterStore $store
    ) {
    }

    public function forShift(string $day, string $shift): array
    {
        if (
            !in_array($day, Shifts::DAYS, true)
            || !in_array($shift, Shifts::names(), true)
        ) {
            return [];
        }

        $roster = $this->store->all();

        if (($roster[$day][$shift] ?? null) !== null) {
            return [];
        }

        $eligible = [];

        foreach (Staff::all() as $employee) {
            $validator = Validator::make(
                ['employee_id' => $employee->id],
                [
                    'employee_id' => [
                        new NoOverlap($this->store, $day, $shift),
                        new MinimumRest($this->store, $day, $shift),
                    ],
                ]
            );

            if ($validator->passes()) {
                $eligible[] = $employee;
            }
        }

        return $eligible;
    }
}