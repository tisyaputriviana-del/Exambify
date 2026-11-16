<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'nisn',
        'password',
        'department_id',
        'class_level',
        'class_section',
        'is_frozen',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_frozen' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function cheatViolations(): HasMany
    {
        return $this->hasMany(CheatViolation::class);
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $initials = collect($parts)->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');

        return mb_strtoupper($initials ?: 'S');
    }

    // Kelas ditampilkan dalam format romawi + nomor kelas, mis: "XII - RPL 2"
    public function getClassStrAttribute(): string
    {
        $roman = ['10' => 'X', '11' => 'XI', '12' => 'XII'][$this->class_level] ?? $this->class_level;
        $deptCode = $this->department->code ?? '-';
        $section = $this->class_section ? " {$this->class_section}" : '';

        return "{$roman} - {$deptCode}{$section}";
    }

    // Grup untuk pengelompokan tampilan admin, mis: "RPL - Kelas X-2"
    public function getClassGroupLabelAttribute(): string
    {
        $roman = ['10' => 'X', '11' => 'XI', '12' => 'XII'][$this->class_level] ?? $this->class_level;
        $deptCode = $this->department->code ?? 'Tanpa Jurusan';
        $section = $this->class_section ? "-{$this->class_section}" : '';

        return "{$deptCode} - Kelas {$roman}{$section}";
    }
}
