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
        'description', 'duration_minutes', 'teacher_name', 'access_code', 'icon', 'color', 'bg',
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

    /**
     * Mapel yang relevan untuk siswa jurusan & kelas tertentu:
     * - Mapel "umum" TANPA jurusan (department_id kosong) -> tampil untuk SEMUA jurusan
     *   (dipakai kalau gurunya sama untuk semua jurusan).
     * - Mapel "umum" DENGAN jurusan terisi -> hanya tampil untuk jurusan itu saja
     *   (dipakai kalau gurunya beda-beda tiap jurusan, mis. PKN kelas RPL vs PKN kelas TKJ).
     * - Mapel "produktif" -> selalu terikat & hanya tampil untuk jurusannya sendiri.
     */
    public function scopeForDepartmentAndClass($query, ?int $departmentId, string $classLevel)
    {
        return $query->where(function ($q) use ($departmentId) {
            $q->where(function ($q2) use ($departmentId) {
                $q2->where('category', 'umum')
                    ->where(function ($q3) use ($departmentId) {
                        $q3->whereNull('department_id')->orWhere('department_id', $departmentId);
                    });
            })->orWhere(function ($q2) use ($departmentId) {
                $q2->where('category', 'produktif')->where('department_id', $departmentId);
            });
        })->where(function ($q) use ($classLevel) {
            $q->whereNull('class_level')->orWhere('class_level', $classLevel);
        });
    }
}
