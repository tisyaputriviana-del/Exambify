<?php

namespace App\Models\Concerns;

use App\Models\School;
use App\Support\CurrentSchool;
use Illuminate\Database\Eloquent\Builder;

/**
 * Relasi ke sekolah + isi school_id otomatis saat data dibuat.
 * TANPA global scope, dipakai untuk model Admin dan Student
 * (agar proses login tidak berputar-putar memanggil dirinya sendiri).
 */
trait HasSchool
{
    protected static function bootHasSchool(): void
    {
        static::creating(function ($model) {
            if (empty($model->school_id)) {
                $model->school_id = CurrentSchool::id();
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scopeInSchool(Builder $query, ?int $schoolId = null): Builder
    {
        $schoolId ??= CurrentSchool::id();

        return $schoolId
            ? $query->where($this->getTable() . '.school_id', $schoolId)
            : $query;
    }
}
