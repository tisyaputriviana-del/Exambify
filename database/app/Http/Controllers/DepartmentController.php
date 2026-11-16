<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('departments.index', compact('departments'));
    }

    public function classes(Department $department): View
    {
        return view('departments.classes', compact('department'));
    }
}
