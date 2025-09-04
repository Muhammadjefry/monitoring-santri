@extends('layouts.app')

@section('title', 'Rekap Laporan Kegiatan')


@section('style')
<style>
  /* Alert Style */
  .alert-succees {
    position: relative;
    opacity: 0;
    transform: translateY(-100%);
    transition: all 0.5s ease-in-out;

    background-color: rgb(163, 207, 186.6);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 15px;
    color: #ffffffff;
    border-radius: 8px;
    margin-bottom: 20px;
  }

  .alert-succees.slide-down.show {
    opacity: 1;
    transform: translateY(0);
  }

  .alert-danger {
    position: relative;
    opacity: 0;
    transform: translateY(-100%);
    transition: all 0.5s ease-in-out;

    background-color: rgb(241, 174.2, 180.6);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 15px;
    color: #92400e;
    border-radius: 8px;
    margin-bottom: 20px;
  }

  .alert-danger.slide-down.show {
    opacity: 1;
    transform: translateY(0);
  }
</style>
@endsection


@section('content')
<div class="app-content-header">
  <div class="container-fluid">
    <h5 class="mb-2">Laporan Kegiatan/Rekap Laporan/Detail</h5>

  </div>
</div>

<div class="row text-center mb-5">
  <div class="col-md-12">
    <a href="{{ route('admin.rekapLaporan') }}" class="btn btn-secondary m-0 {{ request()->routeIs('admin.rekapLaporan') ? 'active' : '' }}">
      <i class="bi bi-arrow-counterclockwise me-3"></i>Kembali
    </a>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-md-12">

        @if (session('success'))
        <div class="d-flex justify-content-between alertt alert-succees slide-down" id="alert-succees">
          <div>
            <h5><i class="bi bi-check"></i> Sukses!</h5>
            {{ session('success') }}
          </div>
          <div>
            <button class="close d-block btn" onclick="close_alert()">×</button>
          </div>
        </div>
        @endif

        @if (session('error'))
        <div class="d-flex justify-content-between alertt alert-danger slide-down" id="alert-danger">
          <div>
            <h5><i class="bi bi-ban"></i> Gagal!</h5>
            {{ session('error') }}
          </div>
          <div>
            <button class="close d-block btn" onclick="close_alert()">×</button>
          </div>
        </div>
        @endif


        <div class="card mb-4">
          <div class="card-body">
            <table id="myTable" class="table table-bordered">
              <thead>
                <tr>
                  <th>#</th>
                  <th style="width:13%">Tanggal</th>
                  <th>Batas Waktu Laporan</th>
                  <th>Nama Kegiatan</th>
                  <th>Status</th>
                  {{-- DUA KOLOM BARU UNTUK KETERANGAN DETAIL --}}
                  <th style="width:17%">Foto/Dokumentasi</th>
                  <th style="width:15%">Opsi Lain</th>
                  <th style="width:13%">Waktu Laporan</th>
                </tr>
              </thead>

              <tbody>
                @php
                date_default_timezone_set('Asia/Jakarta');
                @endphp

                @foreach ($jadwals as $i => $jadwal)
                @php
                $laporan = $laporans[$jadwal->id] ?? null;

                // Kalkulasi Waktu
                $mulai = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . str_replace('.', ':', $jadwal->waktu_mulai));
                $selesai = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . str_replace('.', ':', $jadwal->waktu_selesai));
                $now = now();
                $uploadTime = $laporan ? \Carbon\Carbon::parse($laporan->updated_at) : null;
                @endphp

                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $mulai->translatedFormat('d F Y') }}</td>
                  <td>{{ $mulai->format('H:i') }} - {{ $selesai->format('H:i') }}</td>
                  <td>{{ $jadwal->judul_kegiatan }}</td>

                  {{-- ## PERBAIKAN KOLOM STATUS ## --}}
                  <td>
                    @if ($laporan)
                    @if ($laporan->keterangan)
                    ✅ {{ $laporan->keterangan }}
                    @else
                    ✅ Sudah Lapor
                    @endif
                    @elseif ($now->gt($selesai))
                    ❌ Tidak Diisi
                    @else
                    ⏳ Belum Lapor
                    @endif
                  </td>

                  {{-- ## KOLOM FOTO/DOKUMENTASI ## --}}
                  {{-- Hanya menampilkan bukti file jika ada --}}
                  <td>
                    @if ($laporan && $laporan->bukti_laporan)
                    @php
                    $filePath = asset('storage/' . $laporan->bukti_laporan);
                    $ext = strtolower(pathinfo($laporan->bukti_laporan, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp

                    @if ($isImage)
                    <img src="{{ $filePath }}" alt="Bukti" style="max-width: 100px; cursor: pointer; border-radius: 5px;" onclick="previewModal('{{ $filePath }}')">
                    @else
                    <a href="{{ $filePath }}" target="_blank" class="btn btn-sm btn-outline-dark">Lihat Dokumen</a>
                    @endif
                    @else
                    <span class="text-muted small">-</span>
                    @endif
                  </td>

                  <td>
                    @if ($laporan && $laporan->keterangan)
                    <span class="badge  pt-2 pb-2 ps-5 pe-5 text-white
                                @if($laporan->keterangan == 'Sakit') bg-danger 
                                @elseif($laporan->keterangan == 'Izin') bg-primary 
                                @else bg-secondary @endif">
                      {{ $laporan->keterangan }}
                    </span>
                    @else
                    <span class="text-muted small">-</span>
                    @endif
                  </td>

                  <td>{{ $uploadTime ? $uploadTime->format('d-m-Y H:i:s') : '-' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>


      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <img id="modalImage" src="" class="img-fluid" alt="Preview">
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  let table = new DataTable('#myTable');

  function close_alert() {
    $('.alertt').remove();
  }

  function previewModal(src) {
    document.getElementById('modalImage').src = src;
    $('#modalPreview').modal('show');
  }

  window.addEventListener('DOMContentLoaded', () => {
    const alert1 = document.getElementById('alert-succees');
    const alert2 = document.getElementById('alert-danger');

    if (alert1) {
      setTimeout(() => {
        alert1.classList.add('show');
      }, 100);

      setTimeout(() => {
        alert1.classList.remove('show');

        setTimeout(() => {
          alert1.remove();
        }, 500);
      }, 4000);
    } else if (alert2) {
      setTimeout(() => {
        alert2.classList.add('show');
      }, 100);

      setTimeout(() => {
        alert2.classList.remove('show');

        setTimeout(() => {
          alert2.remove();
        }, 500);
      }, 4000);
    }
  });
</script>
@endsection