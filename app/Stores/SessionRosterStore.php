<?php

declare(strict_types=1);

namespace App\Stores;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use Illuminate\Support\Facades\Session;

/**
 * F06/F07: almacena el cuadrante semanal en la sesión de Laravel.
 *
 * Utiliza la clave de sesión `roster` para guardar los turnos y
 * `roster_title` para guardar el título libre de la semana.
 * Si la sesión no contiene un cuadrante, devuelve los siete días con
 * sus tres turnos vacíos (null).
 */
final class SessionRosterStore implements RosterStore
{
    private const ROSTER_KEY = 'roster';
    private const TITLE_KEY = 'roster_title';

    /**
     * @return array<string, array<string, int|null>>
     */
    public function all(): array
    {
        $roster = Session::get(self::ROSTER_KEY);

        if (is_array($roster)) {
            return $roster;
        }

        $week = [];

        foreach (Shifts::DAYS as $day) {
            foreach (Shifts::names() as $shift) {
                $week[$day][$shift] = null;
            }
        }

        return $week;
    }

    public function assign(string $day, string $shift, int $employeeId): void
    {
        $roster = $this->all();
        $roster[$day][$shift] = $employeeId;

        Session::put(self::ROSTER_KEY, $roster);
    }

    public function remove(string $day, string $shift): void
    {
        $roster = $this->all();
        $roster[$day][$shift] = null;

        Session::put(self::ROSTER_KEY, $roster);
    }

    /**
     * @return list<array{day: string, shift: string}>
     */
    public function forEmployee(int $employeeId): array
    {
        $result = [];

        foreach ($this->all() as $day => $dayShifts) {
            foreach ($dayShifts as $shift => $id) {
                if ($id === $employeeId) {
                    $result[] = [
                        'day' => $day,
                        'shift' => $shift,
                    ];
                }
            }
        }

        return $result;
    }

    public function title(): string
    {
        return (string) Session::get(self::TITLE_KEY, '');
    }

    public function setTitle(string $title): void
    {
        Session::put(self::TITLE_KEY, $title);
    }
}
