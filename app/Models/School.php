<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'npsn',
        'logo',
        'address',
        'is_active',
        'package_type',
        'subscription_start',
        'subscription_end',
        'price',
        'billing_status',
        'pic_name',
        'pic_phone',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_start' => 'date',
        'subscription_end' => 'date',
        'price' => 'integer',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class)->withoutGlobalScopes();
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class)->withoutGlobalScopes();
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function payments()
    {
        return $this->hasMany(LicensePayment::class)->latest();
    }

    public function getPackageLabelAttribute(): string
    {
        return match ($this->package_type) {
            'sewa_bulanan' => 'Sewa Bulanan',
            'sewa_tahunan' => 'Sewa Tahunan',
            'beli_permanen' => 'Beli Permanen (Lifetime)',
            default => 'Beli Permanen (Lifetime)',
        };
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price ?? 0, 0, ',', '.');
    }

    public function getRemainingDaysAttribute(): ?int
    {
        if ($this->package_type === 'beli_permanen' || ! $this->subscription_end) {
            return null;
        }

        $now = Carbon::today();
        if ($now->greaterThan($this->subscription_end)) {
            return 0;
        }

        return (int) $now->diffInDays($this->subscription_end);
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->package_type === 'beli_permanen' || ! $this->subscription_end) {
            return false;
        }

        return Carbon::today()->greaterThan($this->subscription_end);
    }
}
