<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductShowcaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

// APP_MODE: 'all' (satu hosting), 'student' (hosting siswa saja), atau 'admin' (hosting admin saja)
$mode = config('examify.mode', 'all');

if (in_array($mode, ['all', 'student'], true)) {
    // Halaman publik
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/tentang', [PageController::class, 'about'])->name('about');
    Route::get('/bantuan', [PageController::class, 'contact'])->name('contact');
    Route::post('/bantuan', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::post('/bantuan/{feedback}/baca', [FeedbackController::class, 'markRead'])->name('feedback.mark-read')->middleware('auth');
    Route::get('/notifikasi/unread-count', [FeedbackController::class, 'unreadCount'])->name('feedback.unread-count')->middleware('auth');
    Route::get('/jadwal', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/katalog-website', [ProductShowcaseController::class, 'index'])->name('website.catalog');

    // Auth (guest only)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    // Halaman siswa yang wajib login
    Route::middleware('auth')->group(function () {
        Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
        Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/ujian/jurusan', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/ujian/jurusan/{department}/kelas', [DepartmentController::class, 'classes'])->name('departments.classes');
        Route::get('/ujian/jurusan/{department}/kelas/{classLevel}', [SubjectController::class, 'index'])
            ->whereIn('classLevel', ['10', '11', '12'])
            ->name('subjects.index');

        Route::post('/ujian/{subject}/mulai', [ExamController::class, 'start'])->name('exam.start');
        Route::get('/ujian/sesi/{attempt}', [ExamController::class, 'show'])->name('exam.show');
        Route::post('/ujian/sesi/{attempt}/autosave', [ExamController::class, 'autosave'])->name('exam.autosave');
        Route::post('/ujian/sesi/{attempt}/submit', [ExamController::class, 'submit'])->name('exam.submit');
        Route::get('/ujian/sesi/{attempt}/selesai', [ExamController::class, 'success'])->name('exam.success');
        Route::post('/ujian/sesi/{attempt}/pelanggaran', [ExamController::class, 'violation'])->name('exam.violation');
        Route::get('/akun/status', [ExamController::class, 'frozenStatus'])->name('exam.frozen');
        Route::post('/akun/peringatan/{violation}/baca', [ExamController::class, 'acknowledgeWarning'])->name('exam.acknowledge-warning');
        Route::post('/ujian/minta-unfreeze', [ExamController::class, 'requestUnfreeze'])->name('exam.request-unfreeze');
    });
}

/*
|--------------------------------------------------------------------------
| Panel Admin / Guru / Proktor
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\AdminClassroomController;
use App\Http\Controllers\Admin\AdminDepartmentController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CheatViolationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExamResultController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\LicenseManagementController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\SchoolRegistrationController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Admin\SubjectQuestionController;
use App\Http\Controllers\Admin\TenantPaymentController;
use App\Http\Controllers\Owner\OwnerLicenseController;
use App\Http\Controllers\Owner\OwnerPaymentController;
use App\Http\Controllers\Owner\OwnerPricingController;
use App\Http\Controllers\Owner\OwnerProductController;

if (in_array($mode, ['all', 'admin'], true)) {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AdminAuthController::class, 'login'])->name('login.store');

            // Pendaftaran sekolah baru
            Route::get('/daftar', [SchoolRegistrationController::class, 'create'])->name('register');
            Route::post('/daftar', [SchoolRegistrationController::class, 'store'])
                ->middleware('throttle:5,1')
                ->name('register.store');
        });

        Route::middleware('auth:admin')->group(function () {
            Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

            Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Profil Admin & Edit Sandi
            Route::get('/profil', [AdminProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profil', [AdminProfileController::class, 'update'])->name('profile.update');

            // Kelola Admin & Monitoring Siapa Saja Admin yang Masuk (Login Log / Sesi)
            Route::get('/kelola-admin', [AdminManagementController::class, 'index'])->name('admins.index');
            Route::post('/kelola-admin', [AdminManagementController::class, 'store'])->name('admins.store');
            Route::put('/kelola-admin/{admin}', [AdminManagementController::class, 'update'])->name('admins.update');
            Route::delete('/kelola-admin/{admin}', [AdminManagementController::class, 'destroy'])->name('admins.destroy');

            // Portal Pembayaran Sewa & Beli Lisensi bagi Sekolah (QRIS, E-Wallet, M-Banking)
            Route::get('/pembayaran-lisensi', [TenantPaymentController::class, 'show'])->name('subscription.pay');
            Route::post('/pembayaran-lisensi', [TenantPaymentController::class, 'store'])->name('subscription.store');

            // Dashboard Khusus Penjual / Pengelola Lisensi (Sewa & Beli Permanen)
            Route::get('/lisensi', [OwnerLicenseController::class, 'index'])->name('licenses.index');
            Route::post('/lisensi', [OwnerLicenseController::class, 'store'])->name('licenses.store');
            Route::put('/lisensi/{school}', [OwnerLicenseController::class, 'update'])->name('licenses.update');
            Route::patch('/lisensi/{school}/toggle', [OwnerLicenseController::class, 'toggleStatus'])->name('licenses.toggle');
            Route::patch('/lisensi/{school}/tandai-lunas', [OwnerLicenseController::class, 'markAsPaid'])->name('licenses.mark-paid');
            Route::delete('/lisensi/{school}', [OwnerLicenseController::class, 'destroy'])->name('licenses.destroy');

            Route::get('/data-peserta', [AdminStudentController::class, 'index'])->name('students.index');
            Route::patch('/data-peserta/{student}/toggle-freeze', [AdminStudentController::class, 'toggleFreeze'])->name('students.toggle-freeze');

            Route::get('/hasil-ujian', [ExamResultController::class, 'index'])->name('results.index');
            Route::patch('/hasil-ujian/{attempt}/rilis', [ExamResultController::class, 'release'])->name('results.release');
            Route::patch('/hasil-ujian/{attempt}/selesaikan', [ExamResultController::class, 'finish'])->name('results.finish');
            Route::post('/hasil-ujian/selesaikan-massal', [ExamResultController::class, 'bulkFinish'])->name('results.bulk-finish');
            Route::patch('/hasil-ujian/{attempt}/tanggal-ujian', [ExamResultController::class, 'updateExamDate'])->name('results.update-exam-date');
            Route::post('/hasil-ujian/atur-tanggal-massal', [ExamResultController::class, 'bulkSetExamDate'])->name('results.bulk-set-exam-date');
            Route::get('/hasil-ujian/export/excel', [ExamResultController::class, 'exportExcel'])->name('results.export-excel');
            Route::get('/hasil-ujian/export/pdf', [ExamResultController::class, 'printPdf'])->name('results.print-pdf');

            Route::get('/kecurangan', [CheatViolationController::class, 'index'])->name('cheating.index');
            Route::post('/kecurangan/kirim-peringatan', [CheatViolationController::class, 'store'])->name('cheating.store');
            Route::patch('/kecurangan/siswa/{student}/unfreeze', [CheatViolationController::class, 'unfreeze'])->name('cheating.unfreeze');
            Route::delete('/kecurangan/siswa/{student}/reset', [CheatViolationController::class, 'resetViolations'])->name('cheating.reset');

            Route::get('/pesan-masuk', [AdminFeedbackController::class, 'index'])->name('feedback.index');
            Route::get('/pesan-masuk/unread-count', [AdminFeedbackController::class, 'unreadCount'])->name('feedback.unread-count');
            Route::post('/pesan-masuk/{feedback}/balas', [AdminFeedbackController::class, 'reply'])->name('feedback.reply');
            Route::post('/pesan-masuk/{feedback}/unfreeze', [AdminFeedbackController::class, 'unfreeze'])->name('feedback.unfreeze');
            Route::delete('/pesan-masuk/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedback.destroy');

            Route::get('/jadwal', [AdminScheduleController::class, 'index'])->name('schedule.index');
            Route::post('/jadwal', [AdminScheduleController::class, 'store'])->name('schedule.store');
            Route::patch('/jadwal/{schedule}', [AdminScheduleController::class, 'update'])->name('schedule.update');
            Route::delete('/jadwal/{schedule}', [AdminScheduleController::class, 'destroy'])->name('schedule.destroy');

            Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
            Route::post('/pengumuman', [AnnouncementController::class, 'store'])->name('announcements.store');
            Route::delete('/pengumuman/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

            Route::get('/bank-soal', [AdminSubjectController::class, 'index'])->name('subjects.index');
            Route::delete('/bank-soal/{subject}', [AdminSubjectController::class, 'destroy'])->name('subjects.destroy');
            Route::patch('/bank-soal/{subject}/kode-akses', [AdminSubjectController::class, 'updateAccessCode'])->name('subjects.access-code');
            Route::get('/bank-soal/{subject}/soal', [SubjectQuestionController::class, 'index'])->name('subjects.questions');
            Route::get('/bank-soal/{subject}/soal/{question}/edit', [SubjectQuestionController::class, 'edit'])->name('subjects.questions.edit');
            Route::put('/bank-soal/{subject}/soal/{question}', [SubjectQuestionController::class, 'update'])->name('subjects.questions.update');
            Route::delete('/bank-soal/{subject}/soal/{question}', [SubjectQuestionController::class, 'destroy'])->name('subjects.questions.destroy');

            Route::get('/tambah-soal', [AdminQuestionController::class, 'create'])->name('questions.create');
            Route::post('/tambah-soal', [AdminQuestionController::class, 'store'])->name('questions.store');

            Route::get('/pengaturan', [AdminAccountController::class, 'index'])->name('settings.index');
            Route::post('/pengaturan/admin', [AdminAccountController::class, 'store'])->name('settings.admins.store');
            Route::delete('/pengaturan/admin/{admin}', [AdminAccountController::class, 'destroy'])->name('settings.admins.destroy');
            Route::post('/pengaturan/template-peringatan', [AdminAccountController::class, 'updateWarningTemplate'])->name('settings.warning-template');

            // Jurusan (tambah & hapus)
            Route::post('/pengaturan/jurusan', [AdminDepartmentController::class, 'store'])->name('settings.departments.store');
            Route::delete('/pengaturan/jurusan/{id}', [AdminDepartmentController::class, 'destroy'])->name('settings.departments.destroy');

            // Kelas (tambah & hapus)
            Route::post('/pengaturan/kelas', [AdminClassroomController::class, 'store'])->name('settings.classrooms.store');
            Route::delete('/pengaturan/kelas/{id}', [AdminClassroomController::class, 'destroy'])->name('settings.classrooms.destroy');
        });
    });

    // Panel Khusus Pengelola / Pemilik Platform Website SaaS (Folder Terpisah)
    Route::prefix('pengelola')->name('pengelola.')->middleware('auth:admin')->group(function () {
        Route::get('/', fn () => redirect()->route('pengelola.licenses.index'))->name('dashboard');

        // Lisensi & Sewa Sekolah
        Route::get('/lisensi', [OwnerLicenseController::class, 'index'])->name('licenses.index');
        Route::post('/lisensi', [OwnerLicenseController::class, 'store'])->name('licenses.store');
        Route::put('/lisensi/{school}', [OwnerLicenseController::class, 'update'])->name('licenses.update');
        Route::patch('/lisensi/{school}/toggle', [OwnerLicenseController::class, 'toggleStatus'])->name('licenses.toggle');
        Route::patch('/lisensi/{school}/tandai-lunas', [OwnerLicenseController::class, 'markAsPaid'])->name('licenses.mark-paid');
        Route::delete('/lisensi/{school}', [OwnerLicenseController::class, 'destroy'])->name('licenses.destroy');

        // Verifikasi Pembayaran Masuk (QRIS, E-Wallet, M-Banking)
        Route::get('/pembayaran', [OwnerPaymentController::class, 'index'])->name('payments.index');
        Route::patch('/pembayaran/{payment}/verifikasi', [OwnerPaymentController::class, 'verify'])->name('payments.verify');
        Route::patch('/pembayaran/{payment}/tolak', [OwnerPaymentController::class, 'reject'])->name('payments.reject');
        Route::delete('/pembayaran/{payment}', [OwnerPaymentController::class, 'destroy'])->name('payments.destroy');

        // Katalog & Tambah Item Website Lain
        Route::get('/produk', [OwnerProductController::class, 'index'])->name('products.index');
        Route::post('/produk', [OwnerProductController::class, 'store'])->name('products.store');
        Route::put('/produk/{product}', [OwnerProductController::class, 'update'])->name('products.update');
        Route::patch('/produk/{product}/toggle', [OwnerProductController::class, 'toggleAvailability'])->name('products.toggle');
        Route::delete('/produk/{product}', [OwnerProductController::class, 'destroy'])->name('products.destroy');

        // Ubah Harga Paket & Info Rekening Pembayaran
        Route::get('/harga', [OwnerPricingController::class, 'index'])->name('pricing.index');
        Route::put('/harga', [OwnerPricingController::class, 'update'])->name('pricing.update');
    });

    if ($mode === 'admin') {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
    }
}
