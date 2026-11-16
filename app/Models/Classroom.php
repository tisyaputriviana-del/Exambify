<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use BelongsToSchool;

    protected $fillable = ['school_id', 'department_id', 'name', 'level'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
