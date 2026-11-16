<?php

return [
    /*
    | Mode aplikasi:
    |  - all     : portal siswa + panel admin dalam satu hosting (default)
    |  - student : hanya portal siswa (untuk hosting siswa)
    |  - admin   : hanya panel admin (untuk hosting admin)
    */
    'mode' => env('APP_MODE', 'all'),

    // Dipakai panel admin untuk link "Buka Portal Ujian Siswa" saat hosting dipisah.
    'student_url' => env('STUDENT_URL'),
];
