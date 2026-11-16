<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'exam_date',
        'exam_type',
        'started_at',
        'submitted_at',
        'score',
        'score_released',
        'needs_grading',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score_released' => 'boolean',
            'needs_grading' => 'boolean',
        ];
    }

    /**
     * Mendapatkan tanggal ujian efektif:
     * 1. Kolom exam_date jika sudah diatur oleh admin
     * 2. submitted_at jika ujian sudah diserahkan
     * 3. started_at jika sedang berjalan
     * 4. created_at
     */
    public function getEffectiveExamDateAttribute(): ?Carbon
    {
        if ($this->exam_date) {
            return Carbon::parse($this->exam_date)->startOfDay();
        }

        if ($this->submitted_at) {
            return $this->submitted_at->copy()->startOfDay();
        }

        if ($this->started_at) {
            return $this->started_at->copy()->startOfDay();
        }

        if ($this->created_at) {
            return $this->created_at->copy()->startOfDay();
        }

        return null;
    }

    /**
     * Format tanggal pelaksanaan ujian: DD-MM-YYYY (contoh: 01-11-2026)
     */
    public function getFormattedExamDateAttribute(): string
    {
        $date = $this->effective_exam_date;

        return $date ? $date->format('d-m-Y') : '-';
    }

    /**
     * Format tanggal pelaksanaan ujian dalam teks Indonesia (contoh: 01 November 2026)
     */
    public function getFormattedExamDateIndoAttribute(): string
    {
        $date = $this->effective_exam_date;

        return $date ? $date->translatedFormat('d F Y') : '-';
    }

    /**
     * Nilai tanggal untuk input type="date" (YYYY-MM-DD)
     */
    public function getExamDateInputValueAttribute(): string
    {
        $date = $this->effective_exam_date;

        return $date ? $date->format('Y-m-d') : '';
    }

    /**
     * Tampilan jenis ujian (UTS / UAS)
     */
    public function getDisplayExamTypeAttribute(): string
    {
        return strtoupper($this->exam_type ?? 'UAS');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }
}
