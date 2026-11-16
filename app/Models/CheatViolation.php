<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheatViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'exam_attempt_id', 'source', 'activity', 'reason',
        'warning_number', 'page', 'resulted_freeze', 'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'resulted_freeze' => 'boolean',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }
}
