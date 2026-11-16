<?php

namespace App\Models\Concerns;

use App\Support\CurrentSchool;
use Illuminate\Database\Eloquent\Builder;

/**
 * Data otomatis hanya tampil milik sekolah yang sedang aktif.
 * Pakai di: Department, Subject, ScheduleItem, Announcement, Feedback, AppSetting, Classroom.
 * Jangan dipakai di Admin dan Student (pakai HasSchool).
 */
trait BelongsToSchool
{
    use HasSchool;

    protected static function bootBelongsToSchool(): void
    {
        static::addGlobalScope('school', function (Builder $query) {
            $schoolId = CurrentSchool::id();

            if ($schoolId) {
                $query->where($query->getModel()->getTable() . '.school_id', $schoolId);
            }
        });
    }
}
