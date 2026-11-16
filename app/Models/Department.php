<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'category', 'description', 'class_count', 'icon', 'color', 'bg',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public static function ensureDefaultDepartments(): void
    {
        $defaultRows = [
            ['code' => 'AK', 'name' => 'Akuntansi dan Keuangan Lembaga', 'category' => 'Bisnis & Manajemen', 'description' => 'Fokus pada siklus akuntansi perusahaan, perpajakan, dan pembukuan keuangan.', 'class_count' => 2, 'icon' => 'fa-calculator', 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-600/10'],
            ['code' => 'MM', 'name' => 'Multimedia / DKV', 'category' => 'Desain & Kreatif', 'description' => 'Fokus pada desain grafis, animasi 2D/3D, dan produksi video.', 'class_count' => 2, 'icon' => 'fa-palette', 'color' => 'text-pink-400', 'bg' => 'bg-pink-600/10'],
            ['code' => 'OTKP', 'name' => 'Otomatisasi Tata Kelola Perkantoran', 'category' => 'Administrasi Bisnis', 'description' => 'Fokus pada administrasi perkantoran modern, arsip digital, dan korespondensi bisnis.', 'class_count' => 2, 'icon' => 'fa-briefcase', 'color' => 'text-amber-400', 'bg' => 'bg-amber-600/10'],
            ['code' => 'PPLG', 'name' => 'Pengembangan Perangkat Lunak dan Gim', 'category' => 'Teknologi Informasi', 'description' => 'Fokus pada pemrograman web, mobile, basis data, dan game development.', 'class_count' => 3, 'icon' => 'fa-gamepad', 'color' => 'text-sky-400', 'bg' => 'bg-sky-600/10'],
            ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak', 'category' => 'Teknologi Informasi', 'description' => 'Fokus pada pemrograman web, mobile, basis data, dan algoritma perangkat lunak.', 'class_count' => 3, 'icon' => 'fa-code', 'color' => 'text-blue-400', 'bg' => 'bg-blue-600/10'],
            ['code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan', 'category' => 'Jaringan & Infrastruktur', 'description' => 'Fokus pada administrasi server, routing, jaringan komputer, dan keamanan siber.', 'class_count' => 2, 'icon' => 'fa-network-wired', 'color' => 'text-indigo-400', 'bg' => 'bg-indigo-600/10'],
        ];

        try {
            foreach ($defaultRows as $row) {
                self::updateOrCreate(['code' => $row['code']], $row);
            }
        } catch (\Throwable $e) {
            // Silently ignore if database is temporarily unreachable
        }
    }
}
