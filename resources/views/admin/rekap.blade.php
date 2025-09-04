<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px
    }

    .header {
      margin-bottom: 10px
    }

    table {
      width: 100%;
      border-collapse: collapse
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 6px;
      text-align: left
    }
  </style>
</head>

<body>
  <div class="header">
    <h3>Rekap Laporan Santri</h3>
    <p>Nama Santri: {{ $santri->nama_santri ?? '-' }}</p>
    <p>Nama Orangtua: {{ $santri->nama_ortu ?? '-' }} — {{ $santri->no_hp_ortu ?? '-' }}</p>
    <hr>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Waktu</th>
        <th>Kegiatan</th>
        <th>Status</th>
        <th>Uploaded At</th>
      </tr>
    </thead>

    {{-- File: resources/views/admin/rekap.blade.php --}}

    <tbody>
      @php
      date_default_timezone_set('Asia/Jakarta');
      @endphp

      @foreach ($jadwals as $i => $jadwal)
      @php
      $laporan = $laporans[$jadwal->id] ?? null;
      $mulai = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . str_replace('.', ':', $jadwal->waktu_mulai));
      $selesai = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . str_replace('.', ':', $jadwal->waktu_selesai));
      $now = now(); // Gunakan now() agar konsisten
      $uploadTime = $laporan ? \Carbon\Carbon::parse($laporan->updated_at) : null;
      @endphp

      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $mulai->translatedFormat('d F Y') }}</td>
        <td>{{ $mulai->format('H:i') }} - {{ $selesai->format('H:i') }}</td>
        <td>{{ $jadwal->judul_kegiatan }}</td>

        {{-- ## PERBAIKAN UTAMA DI KOLOM STATUS ## --}}
        <td>
          @if ($laporan)
          {{-- Jika ada keterangan (Izin/Sakit), tampilkan keterangan itu --}}
          @if ($laporan->keterangan)
          {{ $laporan->keterangan }}
          {{-- Jika tidak ada keterangan, berarti sudah lapor dengan file --}}
          @else
          Sudah Lapor
          @endif
          @elseif ($now->gt($selesai))
          Tidak Diisi
          @else
          Belum Lapor
          @endif
        </td>

        <td>{{ $uploadTime ? $uploadTime->format('d-m-Y H:i:s') : '-' }}</td>
      </tr>
      @endforeach
    </tbody>

  </table>
</body>

</html>