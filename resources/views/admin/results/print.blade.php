<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Ujian - CBT Examify</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111; padding: 24px; }
        h1 { font-size: 18px; margin-bottom: 4px; text-transform: uppercase; }
        .meta-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 11px;
            color: #334155;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }
        .meta-item { display: flex; }
        .meta-label { font-weight: bold; width: 160px; color: #64748b; }
        .meta-val { font-weight: 600; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; font-weight: bold; color: #1e293b; }
        tr:nth-child(even) { background: #f8fafc; }
        .badge-date { font-weight: bold; color: #0369a1; }
        .badge-type { font-weight: bold; padding: 2px 6px; border-radius: 4px; font-size: 10px; background: #e0e7ff; color: #3730a3; }
        .no-print { margin-bottom: 20px; }
        .no-print button {
            background: #2563eb; color: #fff; border: none; padding: 10px 18px;
            border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer;
        }
        .no-print button:hover { background: #1d4ed8; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Cetak / Simpan sebagai PDF</button>
        <p style="font-size:11px; color:#777; margin-top:8px;">
            Klik tombol di atas, lalu pada dialog cetak pilih tujuan <strong>"Save as PDF"</strong> / "Microsoft Print to PDF" untuk mengunduh dokumen PDF.
        </p>
    </div>

    <h1>Rekapitulasi Hasil Ujian — CBT Examify</h1>
    
    <div class="meta-box">
        <div class="meta-item">
            <span class="meta-label">Kategori Jurusan:</span>
            <span class="meta-val">{{ $departmentFilter === 'ALL' ? 'Semua Jurusan' : $departmentFilter }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Jenis Ujian:</span>
            <span class="meta-val">{{ $examType === 'ALL' ? 'Semua Jenis (UTS & UAS)' : $examType }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Tanggal / Periode:</span>
            <span class="meta-val badge-date">{{ $dateRangeLabel }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Waktu Cetak:</span>
            <span class="meta-val">{{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Total Peserta:</span>
            <span class="meta-val">{{ $attempts->count() }} Siswa</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th>Nama Siswa</th>
                <th>NISN</th>
                <th>Sekolah</th>
                <th>Jurusan</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
                <th>Jenis</th>
                <th>Tanggal Ujian</th>
                <th>Waktu Submit</th>
                <th>Status</th>
                <th>Nilai</th>
                <th>Status Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attempts as $i => $a)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td><strong>{{ $a->student->name }}</strong></td>
                    <td>{{ $a->student->nisn }}</td>
                    <td>{{ $a->student->school->name ?? '-' }}</td>
                    <td>{{ $a->student->department->code ?? '-' }}</td>
                    <td>{{ $a->student->class_str }}</td>
                    <td>{{ $a->subject->name }}</td>
                    <td><span class="badge-type">{{ $a->display_exam_type }}</span></td>
                    <td class="badge-date">{{ $a->formatted_exam_date }}</td>
                    <td>{{ optional($a->submitted_at)->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>{{ ucfirst($a->status) }}</td>
                    <td style="font-weight: bold;">{{ $a->score ?? '-' }}</td>
                    <td>{{ $a->score_released ? 'Dirilis' : 'Disembunyikan' }}</td>
                </tr>
            @empty
                <tr><td colspan="13" style="text-align:center; padding:16px;">Tidak ada data hasil ujian yang sesuai dengan filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
