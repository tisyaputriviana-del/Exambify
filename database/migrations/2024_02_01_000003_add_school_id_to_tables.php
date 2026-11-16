<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tabel yang datanya milik masing-masing sekolah. */
    private array $tables = [
        'departments',
        'admins',
        'students',
        'subjects',
        'schedule_items',
        'announcements',
        'feedbacks',
        'app_settings',
    ];

    public function up(): void
    {
        // Sekolah bawaan untuk menampung data yang sudah ada.
        $defaultId = DB::table('schools')->orderBy('id')->value('id');
        if (! $defaultId) {
            $defaultId = DB::table('schools')->insertGetId([
                'name' => 'Sekolah Utama',
                'slug' => 'sekolah-utama',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($this->tables as $name) {
            if (! Schema::hasTable($name) || Schema::hasColumn($name, 'school_id')) {
                continue;
            }

            Schema::table($name, function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->constrained('schools')->restrictOnDelete();
            });

            DB::table($name)->whereNull('school_id')->update(['school_id' => $defaultId]);
        }

        // Penanda super admin (bisa menambah sekolah baru).
        if (Schema::hasTable('admins') && ! Schema::hasColumn('admins', 'is_super_admin')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false);
            });

            // Admin pertama otomatis menjadi super admin.
            $firstAdmin = DB::table('admins')->orderBy('id')->value('id');
            if ($firstAdmin) {
                DB::table('admins')->where('id', $firstAdmin)->update(['is_super_admin' => true]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'is_super_admin')) {
            Schema::table('admins', fn (Blueprint $t) => $t->dropColumn('is_super_admin'));
        }

        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && Schema::hasColumn($name, 'school_id')) {
                Schema::table($name, fn (Blueprint $t) => $t->dropConstrainedForeignId('school_id'));
            }
        }
    }
};
