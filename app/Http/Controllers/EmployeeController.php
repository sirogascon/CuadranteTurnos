<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\Staff;
use App\Exceptions\EmployeeNotFoundException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->input('role');
        $sort = $request->input('sort', 'name');

        if (! in_array($role, Staff::ROLES, true)) {
            $role = null;
        }

        $employees = Staff::byRole($role);

        if ($sort === 'hours') {
            usort(
                $employees,
                static fn ($a, $b) => $a->contractHours <=> $b->contractHours
            );
        } else {
            usort(
                $employees,
                static fn ($a, $b) => strcmp($a->name, $b->name)
            );
        }

        return view('employees.index', [
            'employees' => $employees,
            'role' => $role,
            'sort' => $sort,
        ]);
    }

    public function show(int $id): View
    {
        $employee = Staff::find($id);

        if ($employee === null) {
            throw new EmployeeNotFoundException($id);
        }

        return view('employees.show', [
            'employee' => $employee,
            'shifts' => app(\App\Contracts\RosterStore::class)->forEmployee($id),
        ]);
    }
}
