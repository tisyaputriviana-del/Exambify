<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id', 'category', 'class_level', 'code', 'name',
        'description', 'duration_minutes', 'teacher_name', 'icon', 'color', 'bg',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function scheduleItems(): HasMany
    {
        return $this->hasMany(ScheduleItem::class);
    }

    public function scopeForDepartmentAndClass($query, ?int $departmentId, string $classLevel)
    {
        return $query->where(function ($q) use ($departmentId) {
            $q->where('category', 'umum')->orWhere('department_id', $departmentId);
        })->where(function ($q) use ($classLevel) {
            $q->whereNull('class_level')->orWhere('class_level', $classLevel);
        });
    }
}
