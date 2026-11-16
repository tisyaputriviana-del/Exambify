<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name', 'nisn', 'password', 'department_id', 'class_level',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    // Kelas ditampilkan dalam format romawi, mis: "XII - RPL"
    public function getClassStrAttribute(): string
    {
        $roman = ['10' => 'X', '11' => 'XI', '12' => 'XII'][$this->class_level] ?? $this->class_level;
        $deptCode = $this->department->code ?? '-';

        return "{$roman} - {$deptCode}";
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $initials = collect($parts)->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');

        return mb_strtoupper($initials);
    }
}
