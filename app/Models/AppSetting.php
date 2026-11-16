<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public const DEFAULT_WARNING_TEMPLATE = 'PERINGATAN [JUMLAH]/5 dari [GURU] ([JURUSAN]): Anda terdeteksi keluar dari halaman ujian [MAPEL]. Sisa [SISA] kali pelanggaran lagi sebelum akun dibekukan otomatis.';

    public const DEFAULT_FREEZE_MESSAGE = 'Akun Anda telah DIBEKUKAN oleh sistem karena 5 kali pelanggaran keluar dari halaman ujian [MAPEL] yang diawasi oleh [GURU] ([JURUSAN]).';
}
