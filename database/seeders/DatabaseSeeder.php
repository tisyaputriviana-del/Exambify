<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Announcement;
use App\Models\CheatViolation;
use App\Models\Department;
use App\Models\ExamAttempt;
use App\Models\Feedback;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ScheduleItem;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $departments = $this->seedDepartments();
        $subjects = $this->seedSubjects($departments);
        $this->seedQuestions($subjects);
        $this->seedSchedule($subjects);
        $students = $this->seedDemoStudents($departments);
        $student = $students['ahmad'] ?? $students[0];
        $admin = $this->seedDemoAdmin();
        $this->seedDemoSuperAdmin();
        $this->seedDemoDepartmentAdmin($departments);
        $this->seedCheatViolations($student);
        $this->seedFeedback($student);
        $this->seedAnnouncement($admin);
        $this->seedDemoExamAttempts($students, $subjects);
        \App\Models\WebsiteProduct::ensureTableExists();
        \App\Models\LicensePayment::ensureTableExists();
    }

    /**
     * @return array<string, Department>
     */
    private function seedDepartments(): array
    {
        $rows = [
            ['code' => 'PPLG', 'name' => 'Pengembangan Perangkat Lunak dan Gim', 'category' => 'Teknologi Informasi', 'description' => 'Fokus pada pemrograman web, mobile, basis data, dan game development.', 'class_count' => 3, 'icon' => 'fa-gamepad', 'color' => 'text-sky-400', 'bg' => 'bg-sky-600/10'],
            ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak', 'category' => 'Teknologi Informasi', 'description' => 'Fokus pada pemrograman web, mobile, basis data, dan algoritma perangkat lunak.', 'class_count' => 3, 'icon' => 'fa-code', 'color' => 'text-blue-400', 'bg' => 'bg-blue-600/10'],
            ['code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan', 'category' => 'Jaringan & Infrastruktur', 'description' => 'Fokus pada administrasi server, routing, jaringan komputer, dan keamanan siber.', 'class_count' => 2, 'icon' => 'fa-network-wired', 'color' => 'text-indigo-400', 'bg' => 'bg-indigo-600/10'],
            ['code' => 'MM', 'name' => 'Multimedia / DKV', 'category' => 'Desain & Kreatif', 'description' => 'Fokus pada desain grafis, animasi 2D/3D, dan produksi video.', 'class_count' => 2, 'icon' => 'fa-palette', 'color' => 'text-pink-400', 'bg' => 'bg-pink-600/10'],
            ['code' => 'AK', 'name' => 'Akuntansi dan Keuangan Lembaga', 'category' => 'Bisnis & Manajemen', 'description' => 'Fokus pada siklus akuntansi perusahaan, perpajakan, dan pembukuan keuangan.', 'class_count' => 2, 'icon' => 'fa-calculator', 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-600/10'],
            ['code' => 'OTKP', 'name' => 'Otomatisasi Tata Kelola Perkantoran', 'category' => 'Administrasi Bisnis', 'description' => 'Fokus pada administrasi perkantoran modern, arsip digital, dan korespondensi bisnis.', 'class_count' => 2, 'icon' => 'fa-briefcase', 'color' => 'text-amber-400', 'bg' => 'bg-amber-600/10'],
        ];

        $departments = [];
        foreach ($rows as $row) {
            $departments[$row['code']] = Department::updateOrCreate(['code' => $row['code']], $row);
        }

        return $departments;
    }

    /**
     * @param  array<string, Department>  $departments
     * @return array<string, Subject>
     */
    private function seedSubjects(array $departments): array
    {
        $subjects = [];

        $umum = [
            ['id' => 'mat', 'name' => 'Matematika Umum', 'code' => 'MAT-U', 'desc' => 'Fungsi kuadrat, barisan bilangan, trigonometri, dan statistika.', 'duration' => 90, 'icon' => 'fa-square-root-variable', 'color' => 'text-blue-400', 'bg' => 'bg-blue-600/10', 'teacher' => 'Dra. Siti Aminah, M.Pd'],
            ['id' => 'bindo', 'name' => 'Bahasa Indonesia', 'code' => 'BIN-U', 'desc' => 'Analisis teks narasi, negosiasi, resensi, dan karya ilmiah.', 'duration' => 90, 'icon' => 'fa-book', 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-600/10', 'teacher' => 'Drs. H. Suwarto, M.Pd'],
            ['id' => 'bing', 'name' => 'Bahasa Inggris', 'code' => 'BIG-U', 'desc' => 'Reading comprehension, grammar, conditional sentences, and essays.', 'duration' => 90, 'icon' => 'fa-globe', 'color' => 'text-indigo-400', 'bg' => 'bg-indigo-600/10', 'teacher' => 'Jessica Wijaya, S.Pd'],
            ['id' => 'pancasila', 'name' => 'Pendidikan Pancasila & Kewarganegaraan', 'code' => 'PPKN', 'desc' => 'UUD 1945, sistem hukum nasional, dan nilai-nilai Pancasila.', 'duration' => 60, 'icon' => 'fa-shield-halved', 'color' => 'text-amber-400', 'bg' => 'bg-amber-600/10', 'teacher' => 'Agus Setiawan, S.H'],
            ['id' => 'sejarah', 'name' => 'Sejarah Indonesia', 'code' => 'SEJ-U', 'desc' => 'Perjuangan kemerdekaan, masa orde lama, orde baru, hingga reformasi.', 'duration' => 60, 'icon' => 'fa-landmark', 'color' => 'text-purple-400', 'bg' => 'bg-purple-600/10', 'teacher' => 'Dra. Muryati, M.Pd'],
        ];

        foreach ($umum as $s) {
            $subjects[$s['id']] = Subject::updateOrCreate(
                ['code' => $s['code']],
                [
                    'department_id' => null,
                    'category' => 'umum',
                    'class_level' => null,
                    'name' => $s['name'],
                    'description' => $s['desc'],
                    'duration_minutes' => $s['duration'],
                    'teacher_name' => $s['teacher'],
                    'icon' => $s['icon'],
                    'color' => $s['color'],
                    'bg' => $s['bg'],
                ]
            );
        }

        $produktif = [
            'RPL' => [
                ['id' => 'pbo', 'name' => 'Pemrograman Berorientasi Objek (PBO)', 'code' => 'PRD-RPL1', 'desc' => 'Konsep OOP, encapsulation, inheritance, polymorphism menggunakan Java/C#.', 'duration' => 120, 'icon' => 'fa-code', 'teacher' => 'Drs. Budi Santoso, M.Kom'],
                ['id' => 'web', 'name' => 'Pemrograman Web & Perangkat Bergerak', 'code' => 'PRD-RPL2', 'desc' => 'HTML5, CSS3, JavaScript framework, REST API, dan Responsive UI.', 'duration' => 120, 'icon' => 'fa-laptop-code', 'teacher' => 'Rian Pratama, S.T'],
                ['id' => 'db', 'name' => 'Basis Data (Database Administration)', 'code' => 'PRD-RPL3', 'desc' => 'Relational database, SQL queries, normalization, dan stored procedures.', 'duration' => 90, 'icon' => 'fa-database', 'teacher' => 'Ir. Hendra Wijaya'],
            ],
            'TKJ' => [
                ['id' => 'jaringan_wan', 'name' => 'Teknologi Jaringan WAN & Routing', 'code' => 'PRD-TKJ1', 'desc' => 'Konfigurasi router Cisco, VLAN, routing statis/dinamis, dan troubleshooting.', 'duration' => 120, 'icon' => 'fa-network-wired', 'teacher' => 'Eko Prasetyo, S.T'],
                ['id' => 'server_admin', 'name' => 'Administrasi Infrastruktur Server', 'code' => 'PRD-TKJ2', 'desc' => 'Linux server administration, DNS, DHCP, Web Server, dan Firewall.', 'duration' => 120, 'icon' => 'fa-server', 'teacher' => 'Fajar Nugroho, S.Kom'],
            ],
            'MM' => [
                ['id' => 'desain_grafis', 'name' => 'Desain Grafis Percetakan & Media', 'code' => 'PRD-MM1', 'desc' => 'Vector illustration, raster manipulation, layout grid, dan typography.', 'duration' => 90, 'icon' => 'fa-pen-nib', 'teacher' => 'Maya Anggraini, M.Ds'],
                ['id' => 'animasi_video', 'name' => 'Teknik Animasi 2D/3D & Video Editing', 'code' => 'PRD-MM2', 'desc' => 'Rigging character, motion graphics, color grading, dan post-production.', 'duration' => 120, 'icon' => 'fa-clapperboard', 'teacher' => 'Dimas Ananda, S.Sn'],
            ],
            'AK' => [
                ['id' => 'ak_perusahaan', 'name' => 'Akuntansi Perusahaan Jasa & Dagang', 'code' => 'PRD-AK1', 'desc' => 'Jurnal umum, buku besar, neraca lajur, dan laporan laba rugi.', 'duration' => 120, 'icon' => 'fa-calculator', 'teacher' => 'Dra. Hj. Nurul Hidayah'],
                ['id' => 'pajak', 'name' => 'Praktikum Akuntansi Pajak & Komputer', 'code' => 'PRD-AK2', 'desc' => 'PPh Badan/Orang Pribadi, PPN, dan pengoperasian software akuntansi.', 'duration' => 90, 'icon' => 'fa-receipt', 'teacher' => 'Tri Wibowo, S.E., M.Ak'],
            ],
            'OTKP' => [
                ['id' => 'adm_umum', 'name' => 'Administrasi Umum & Kepegawaian', 'code' => 'PRD-OTK1', 'desc' => 'Pengelolaan arsip elektronik, agenda surat masuk/keluar, dan tata naskah.', 'duration' => 90, 'icon' => 'fa-folder-open', 'teacher' => 'Dra. Sri Lestari'],
                ['id' => 'humas', 'name' => 'Keprotokolan & Hubungan Masyarakat', 'code' => 'PRD-OTK2', 'desc' => 'Manajemen event instansi, public speaking, dan pelayanan prima (excellent service).', 'duration' => 90, 'icon' => 'fa-handshake', 'teacher' => 'Anita Rahmawati, S.Sos'],
            ],
        ];

        $deptColors = [
            'RPL' => ['text-blue-400', 'bg-blue-600/10'],
            'TKJ' => ['text-indigo-400', 'bg-indigo-600/10'],
            'MM' => ['text-pink-400', 'bg-pink-600/10'],
            'AK' => ['text-emerald-400', 'bg-emerald-600/10'],
            'OTKP' => ['text-amber-400', 'bg-amber-600/10'],
        ];

        foreach ($produktif as $deptCode => $list) {
            [$color, $bg] = $deptColors[$deptCode];
            foreach ($list as $s) {
                $subjects[$s['id']] = Subject::updateOrCreate(
                    ['code' => $s['code']],
                    [
                        'department_id' => $departments[$deptCode]->id,
                        'category' => 'produktif',
                        'class_level' => null,
                        'name' => $s['name'],
                        'description' => $s['desc'],
                        'duration_minutes' => $s['duration'],
                        'teacher_name' => $s['teacher'],
                        'icon' => $s['icon'],
                        'color' => $color,
                        'bg' => $bg,
                    ]
                );
            }
        }

        return $subjects;
    }

    /**
     * @param  array<string, Subject>  $subjects
     */
    private function seedQuestions(array $subjects): void
    {
        // Bank soal khusus untuk PBO (dipakai sebagai contoh detail, sama seperti mock asal).
        $pboQuestions = [
            ['q' => 'Manakah di bawah ini yang merupakan pilar utama dalam konsep Pemrograman Berorientasi Objek (OOP)?', 'options' => ['Encapsulation, Inheritance, Polymorphism, Abstraction', 'Sequential, Loop, Branching, Function', 'HTML, CSS, JavaScript, PHP', 'RAM, ROM, CPU, Harddisk'], 'answer' => 0],
            ['q' => "Apa fungsi utama dari keyword 'extends' dalam bahasa pemrograman Java?", 'options' => ['Membuat variabel konstanta', 'Melakukan pewarisan (inheritance) dari superclass ke subclass', 'Menghapus memori objek otomatis', 'Menghentikan perulangan (loop)'], 'answer' => 1],
            ['q' => 'Dalam basis data relasional, istilah untuk kolom pada sebuah tabel disebut juga sebagai...', 'options' => ['Tuple', 'Row', 'Attribute / Field', 'Primary Key'], 'answer' => 2],
            ['q' => 'Manakah struktur protokol yang berjalan pada lapisan transport (Transport Layer) dalam OSI Model?', 'options' => ['HTTP & FTP', 'TCP & UDP', 'IP & ICMP', 'Ethernet & Wi-Fi'], 'answer' => 1],
            ['q' => 'Perangkat lunak open source yang biasa digunakan untuk manajemen server web berbasis Linux Apache/Nginx adalah...', 'options' => ['Microsoft Excel', 'Adobe Photoshop', 'Ubuntu Server / Debian', 'Corel Draw'], 'answer' => 2],
        ];

        $this->createQuestionsForSubject($subjects['pbo'], $pboQuestions);

        // Bank soal generik untuk mapel lainnya, agar semua mapel bisa langsung diujikan.
        foreach ($subjects as $key => $subject) {
            if ($key === 'pbo') {
                continue;
            }

            $generic = [];
            for ($i = 1; $i <= 5; $i++) {
                $generic[] = [
                    'q' => "Soal latihan #{$i}: Manakah pernyataan yang paling tepat terkait materi \"{$subject->name}\"?",
                    'options' => [
                        "Pernyataan yang sesuai dengan kompetensi dasar {$subject->name}",
                        'Pernyataan tidak relevan pertama',
                        'Pernyataan tidak relevan kedua',
                        'Pernyataan tidak relevan ketiga',
                    ],
                    'answer' => 0,
                ];
            }
            $this->createQuestionsForSubject($subject, $generic);
        }
    }

    private function createQuestionsForSubject(Subject $subject, array $questions): void
    {
        if ($subject->questions()->exists()) {
            return;
        }

        foreach ($questions as $index => $data) {
            $question = Question::create([
                'subject_id' => $subject->id,
                'question_text' => $data['q'],
                'order' => $index,
            ]);

            foreach ($data['options'] as $optIndex => $optionText) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_letter' => chr(65 + $optIndex),
                    'option_text' => $optionText,
                    'is_correct' => $optIndex === $data['answer'],
                ]);
            }
        }
    }

    /**
     * @param  array<string, Subject>  $subjects
     */
    private function seedSchedule(array $subjects): void
    {
        $items = [
            ['subject' => 'mat', 'date' => now()->next('Monday'), 'start' => '07:30', 'end' => '09:00', 'status' => 'tersedia'],
            ['subject' => 'bindo', 'date' => now()->next('Monday'), 'start' => '09:30', 'end' => '11:30', 'status' => 'tersedia'],
            ['subject' => 'pbo', 'date' => now()->next('Tuesday'), 'start' => '07:30', 'end' => '10:00', 'status' => 'mendatang'],
        ];

        foreach ($items as $item) {
            ScheduleItem::updateOrCreate(
                [
                    'subject_id' => $subjects[$item['subject']]->id,
                    'exam_date' => $item['date']->toDateString(),
                ],
                [
                    'start_time' => $item['start'],
                    'end_time' => $item['end'],
                    'status' => $item['status'],
                ]
            );
        }
    }

    /**
     * @param  array<string, Department>  $departments
     * @return array<string, Student>
     */
    private function seedDemoStudents(array $departments): array
    {
        $pplgDept = $departments['PPLG'] ?? $departments['RPL'];
        $tkjDept = $departments['TKJ'] ?? $departments['RPL'];
        $akDept = $departments['AK'] ?? $departments['RPL'];
        $mmDept = $departments['MM'] ?? $departments['RPL'];
        $otkpDept = $departments['OTKP'] ?? $departments['RPL'];
        $rplDept = $departments['RPL'] ?? $departments['PPLG'];

        $list = [];

        // 1. PPLG
        $list['tyza'] = Student::updateOrCreate(
            ['nisn' => '09872637'],
            ['name' => 'Tyza', 'department_id' => $pplgDept->id, 'class_level' => '10', 'class_section' => 3, 'password' => Hash::make('password123')]
        );
        $list['fajar'] = Student::updateOrCreate(
            ['nisn' => '0061234567'],
            ['name' => 'Fajar Pratama', 'department_id' => $pplgDept->id, 'class_level' => '11', 'class_section' => 2, 'password' => Hash::make('password123')]
        );
        $list['ahmad'] = Student::updateOrCreate(
            ['nisn' => '0058291023'],
            ['name' => 'Ahmad Siswa', 'department_id' => $pplgDept->id, 'class_level' => '12', 'class_section' => 1, 'password' => Hash::make('password123')]
        );

        // 2. TKJ
        $list['ucil'] = Student::updateOrCreate(
            ['nisn' => '075757575'],
            ['name' => 'Ucil', 'department_id' => $tkjDept->id, 'class_level' => '10', 'class_section' => 2, 'password' => Hash::make('password123')]
        );
        $list['budi'] = Student::updateOrCreate(
            ['nisn' => '0063344556'],
            ['name' => 'Budi Kurniawan', 'department_id' => $tkjDept->id, 'class_level' => '11', 'class_section' => 1, 'password' => Hash::make('password123')]
        );
        $list['rizky'] = Student::updateOrCreate(
            ['nisn' => '0054455667'],
            ['name' => 'Rizky Ramadhan', 'department_id' => $tkjDept->id, 'class_level' => '12', 'class_section' => 2, 'password' => Hash::make('password123')]
        );

        // 3. AK (Akuntansi dan Keuangan Lembaga)
        $list['siti'] = Student::updateOrCreate(
            ['nisn' => '0071122334'],
            ['name' => 'Siti Nurhaliza', 'department_id' => $akDept->id, 'class_level' => '10', 'class_section' => 1, 'password' => Hash::make('password123')]
        );
        $list['dewi'] = Student::updateOrCreate(
            ['nisn' => '0062233445'],
            ['name' => 'Dewi Sartika', 'department_id' => $akDept->id, 'class_level' => '11', 'class_section' => 1, 'password' => Hash::make('password123')]
        );
        $list['putri'] = Student::updateOrCreate(
            ['nisn' => '0053344556'],
            ['name' => 'Putri Lestari', 'department_id' => $akDept->id, 'class_level' => '12', 'class_section' => 2, 'password' => Hash::make('password123')]
        );

        // 4. MM (Multimedia / DKV)
        $list['bagas'] = Student::updateOrCreate(
            ['nisn' => '0074455667'],
            ['name' => 'Bagas Prasetyo', 'department_id' => $mmDept->id, 'class_level' => '10', 'class_section' => 1, 'password' => Hash::make('password123')]
        );
        $list['dian'] = Student::updateOrCreate(
            ['nisn' => '0065566778'],
            ['name' => 'Dian Safitri', 'department_id' => $mmDept->id, 'class_level' => '11', 'class_section' => 2, 'password' => Hash::make('password123')]
        );
        $list['bayu'] = Student::updateOrCreate(
            ['nisn' => '0056677889'],
            ['name' => 'Bayu Pratama', 'department_id' => $mmDept->id, 'class_level' => '12', 'class_section' => 1, 'password' => Hash::make('password123')]
        );

        // 5. OTKP (Otomatisasi Tata Kelola Perkantoran)
        $list['rina'] = Student::updateOrCreate(
            ['nisn' => '0077788990'],
            ['name' => 'Rina Novita', 'department_id' => $otkpDept->id, 'class_level' => '10', 'class_section' => 1, 'password' => Hash::make('password123')]
        );
        $list['anisa'] = Student::updateOrCreate(
            ['nisn' => '0068899001'],
            ['name' => 'Anisa Rahmawati', 'department_id' => $otkpDept->id, 'class_level' => '11', 'class_section' => 1, 'password' => Hash::make('password123')]
        );
        $list['tari'] = Student::updateOrCreate(
            ['nisn' => '0059900112'],
            ['name' => 'Tari Anggraini', 'department_id' => $otkpDept->id, 'class_level' => '12', 'class_section' => 2, 'password' => Hash::make('password123')]
        );

        // 6. RPL (Rekayasa Perangkat Lunak)
        $list['dimas'] = Student::updateOrCreate(
            ['nisn' => '0070011223'],
            ['name' => 'Dimas Wahyu', 'department_id' => $rplDept->id, 'class_level' => '10', 'class_section' => 1, 'password' => Hash::make('password123')]
        );
        $list['reza'] = Student::updateOrCreate(
            ['nisn' => '0061122334'],
            ['name' => 'Reza Aditya', 'department_id' => $rplDept->id, 'class_level' => '11', 'class_section' => 2, 'password' => Hash::make('password123')]
        );
        $list['gilang'] = Student::updateOrCreate(
            ['nisn' => '0052233445'],
            ['name' => 'Gilang Ramadhan', 'department_id' => $rplDept->id, 'class_level' => '12', 'class_section' => 1, 'password' => Hash::make('password123')]
        );

        return $list;
    }

    /**
     * @param  array<string, Student>  $students
     * @param  array<string, Subject>  $subjects
     */
    private function seedDemoExamAttempts(array $students, array $subjects): void
    {
        if (ExamAttempt::exists()) {
            return;
        }

        $now = now();
        $sampleSubjects = array_values($subjects);
        $studentValues = array_values($students);

        foreach ($studentValues as $index => $student) {
            $subject = $sampleSubjects[$index % count($sampleSubjects)];
            $score = rand(75, 95);
            $type = ($index % 2 === 0) ? 'UTS' : 'UAS';
            $daysAgo = ($index % 7);

            ExamAttempt::create([
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'status' => 'selesai',
                'score' => $score,
                'score_released' => true,
                'exam_type' => $type,
                'exam_date' => $now->copy()->subDays($daysAgo)->toDateString(),
                'started_at' => $now->copy()->subDays($daysAgo)->setTime(8, 0),
                'submitted_at' => $now->copy()->subDays($daysAgo)->setTime(9, 30),
            ]);
        }
    }

    private function seedDemoAdmin(): Admin
    {
        return Admin::updateOrCreate(
            ['email' => 'admin@examify.test'],
            [
                'name' => 'Tisya Putri Viana',
                'password' => Hash::make('admin123'),
                'role' => 'Admin Sekolah & Proktor',
                'is_super_admin' => false,
            ]
        );
    }

    private function seedDemoSuperAdmin(): Admin
    {
        return Admin::updateOrCreate(
            ['email' => 'owner@examify.test'],
            [
                'name' => 'Super Administrator (Owner SaaS)',
                'password' => Hash::make('owner123'),
                'role' => 'Pemilik Platform & Lisensi',
                'is_super_admin' => true,
            ]
        );
    }

    private function seedDemoDepartmentAdmin(array $departments): void
    {
        Admin::updateOrCreate(
            ['email' => 'guru.rpl@examify.test'],
            [
                'name' => 'Rian Pratama',
                'password' => Hash::make('guru123'),
                'role' => 'Guru Jurusan RPL',
                'department_id' => $departments['RPL']->id,
            ]
        );
    }

    private function seedCheatViolations(Student $student): void
    {
        if (CheatViolation::exists()) {
            return;
        }

        foreach ([1, 2, 3] as $n) {
            CheatViolation::create([
                'student_id' => $student->id,
                'activity' => 'Keluar Tab Browser',
                'warning_number' => $n,
                'page' => 'Soal PBO No. '.$n,
                'resulted_freeze' => false,
            ]);
        }
    }

    private function seedFeedback(Student $student): void
    {
        if (Feedback::exists()) {
            return;
        }

        Feedback::create([
            'student_id' => $student->id,
            'name' => $student->name,
            'class_info' => 'XII - RPL',
            'category' => 'Kendala Teknis Soal / Koneksi',
            'message' => 'Koneksi saya sempat terputus saat mengerjakan soal, mohon dicek.',
            'rating' => 5,
            'status' => 'Menunggu',
        ]);
    }

    private function seedAnnouncement(Admin $admin): void
    {
        if (Announcement::exists()) {
            return;
        }

        Announcement::create([
            'admin_id' => $admin->id,
            'title' => 'Pengumuman Ujian Akhir Semester',
            'message' => 'Pastikan kalian mengerjakan ujian dengan jujur dan bertanggung jawab.',
        ]);
    }
}
