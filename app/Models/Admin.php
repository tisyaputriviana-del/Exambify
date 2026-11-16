<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'is_super_admin',
        'name',
        'email',
        'password',
        'role',
        'department_id',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'last_login_at' => 'datetime',
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

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $initials = collect($parts)->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');

        return mb_strtoupper($initials);
    }

    public function getIsOnlineAttribute(): bool
    {
        return $this->last_login_at && $this->last_login_at->greaterThanOrEqualTo(now()->subMinutes(60));
    }

    public function getLastLoginStrAttribute(): string
    {
        if (! $this->last_login_at) {
            return 'Belum pernah login';
        }

        return $this->last_login_at->translatedFormat('d M Y, H:i').' WIB';
    }

    public function canManageSubject(Subject $subject): bool
    {
        if (! $this->department_id) {
            return true;
        }

        return $subject->department_id === null || $subject->department_id === $this->department_id;
    }
}
