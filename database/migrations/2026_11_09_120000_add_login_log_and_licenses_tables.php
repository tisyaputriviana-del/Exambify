<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom log login admin
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (! Schema::hasColumn('admins', 'last_login_at')) {
                    $table->dateTime('last_login_at')->nullable()->after('remember_token');
                }
                if (! Schema::hasColumn('admins', 'last_login_ip')) {
                    $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
                }
            });
        }

        // 2. Tambah kolom manajemen sewa / lisensi permanen pada sekolah
        if (Schema::hasTable('schools')) {
            Schema::table('schools', function (Blueprint $table) {
                if (! Schema::hasColumn('schools', 'package_type')) {
                    $table->string('package_type', 30)->default('beli_permanen')->after('address');
                }
                if (! Schema::hasColumn('schools', 'subscription_start')) {
                    $table->date('subscription_start')->nullable()->after('package_type');
                }
                if (! Schema::hasColumn('schools', 'subscription_end')) {
                    $table->date('subscription_end')->nullable()->after('subscription_start');
                }
                if (! Schema::hasColumn('schools', 'price')) {
                    $table->unsignedBigInteger('price')->default(0)->after('subscription_end');
                }
                if (! Schema::hasColumn('schools', 'billing_status')) {
                    $table->string('billing_status', 30)->default('aktif')->after('price');
                }
                if (! Schema::hasColumn('schools', 'pic_name')) {
                    $table->string('pic_name')->nullable()->after('billing_status');
                }
                if (! Schema::hasColumn('schools', 'pic_phone', 30)) {
                    $table->string('pic_phone', 30)->nullable()->after('pic_name');
                }
                if (! Schema::hasColumn('schools', 'notes')) {
                    $table->text('notes')->nullable()->after('pic_phone');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (Schema::hasColumn('admins', 'last_login_ip')) {
                    $table->dropColumn('last_login_ip');
                }
                if (Schema::hasColumn('admins', 'last_login_at')) {
                    $table->dropColumn('last_login_at');
                }
            });
        }

        if (Schema::hasTable('schools')) {
            Schema::table('schools', function (Blueprint $table) {
                $cols = ['package_type', 'subscription_start', 'subscription_end', 'price', 'billing_status', 'pic_name', 'pic_phone', 'notes'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('schools', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
