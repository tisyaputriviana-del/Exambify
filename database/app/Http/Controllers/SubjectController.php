<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request, Department $department, string $classLevel): View
    {
        $filter = $request->query('filter', 'all'); // all | umum | produktif

        $query = Subject::with('department')->forDepartmentAndClass($department->id, $classLevel);

        if (in_array($filter, ['umum', 'produktif'], true)) {
            $query->where('category', $filter);
        }

        $subjects = $query->orderBy('category')->orderBy('name')->get();

        return view('subjects.index', [
            'department' => $department,
            'classLevel' => $classLevel,
            'filter' => $filter,
            'subjects' => $subjects,
        ]);
    }
}
