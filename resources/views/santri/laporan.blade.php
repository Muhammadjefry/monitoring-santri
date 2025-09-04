@extends('layouts.app')

@section('title', 'Laporan Kegiatan')

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
    <h5 class="mb-2">Laporan Kegiatan</h5>
  </div>
</div>

<div class="row text-center mb-5">
  <div class="col-md-12">
    <a href="{{ route('santri.rekapLaporan') }}" class="btn btn-primary m-0">
      Rekap Laporan
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

                  <th style="width: 5%;">#</th>
                  <th style="width: 10%;">Tanggal</th>
                  <th style="width: 15%;">Batas Waktu Laporan</th>
                  <th>Nama Kegiatan</th>
                  <th style="width: 15%;">Status</th>
                  <th style="width: 20%;">Foto/Dokumentasi Laporan</th>
                  <th style="width: 20%;">Opsi Lain</th>


                </tr>
              </thead>
              <tbody>
                @php
                date_default_timezone_set('Asia/Jakarta');
                @endphp

                @foreach ($jadwals as $i => $jadwal)
                @php
                $laporan = $laporans[$jadwal->id] ?? null;
                $mulai = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . str_replace('.', ':', $jadwal->waktu_mulai));
                $deadline = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . str_replace('.', ':', $jadwal->waktu_selesai));
                $now = now()->seconds(0);
                @endphp

                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $jadwal->tanggal }}</td>
                  <td>{{ $mulai->format('H:i') }} - {{ $deadline->format('H:i') }}</td>
                  <td>{{ $jadwal->judul_kegiatan }}</td>

                  {{-- KOLOM STATUS --}}
                  <td>
                    @if ($laporan)
                    @if ($laporan->keterangan)
                    ✅ {{ $laporan->keterangan }}
                    @else
                    ✅ Sudah Lapor
                    @endif
                    @elseif ($now->gt($deadline))
                    ❌ Terlambat
                    @else
                    ⏳ Belum Lapor
                    @endif
                  </td>

                  {{-- Kolom 1: FOTO/DOKUMENTASI LAPORAN --}}
                  <td>
                    @if ($laporan && $laporan->keterangan)
                    <span class="text-muted small">-</span>
                    @elseif ($laporan && !$laporan->keterangan)
                    <a href="{{ asset('storage/' . $laporan->bukti_laporan) }}" target="_blank" class="btn btn-primary btn-sm">Lihat File</a>
                    @if ($now->between($mulai, $deadline))
                    <form action="{{ route('laporan.update') }}" method="POST" enctype="multipart/form-data" class="mt-2">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                      <input type="file" name="bukti_laporan" required>
                      <button class="btn btn-warning btn-sm mt-1" type="submit">Update</button>
                    </form>
                    @endif
                    @elseif ($now->between($mulai, $deadline))
                    <form action="{{ route('laporan.upload') }}" method="POST" enctype="multipart/form-data">
                      @csrf
                      <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                      <input type="file" name="bukti_laporan" required>
                      <button class="btn btn-success btn-sm mt-1" type="submit">Upload Laporan</button>
                    </form>
                    @else
                    <span class="text-muted small">
                      @if ($now->gt($deadline)) Ditutup @else Belum Mulai @endif
                    </span>
                    @endif
                  </td>

                  <td>
                    @if ($laporan && !$laporan->keterangan)
                    <span class="text-muted small">-</span>
                    @elseif ($laporan && $laporan->keterangan)
                    {{-- JIKA SUDAH ADA LAPORAN KETERANGAN --}}
                    @if ($now->between($mulai, $deadline))
                    {{-- Tampilkan form update jika masih dalam waktu --}}
                    <form action="{{ route('laporan.update') }}" method="POST" class="mt-2">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">
                      <select name="keterangan" class="form-control form-control-sm mb-2" required>
                        <option value="Izin" {{ $laporan->keterangan == 'Izin' ? 'selected' : '' }}>Izin</option>
                        <option value="Sakit" {{ $laporan->keterangan == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Haid" {{ $laporan->keterangan == 'Haid' ? 'selected' : '' }}>Haid</option>
                      </select>
                      <button class="btn btn-warning btn-sm mt-1" type="submit">Update</button>
                    </form>
                    @else
                    {{-- Tampilkan badge jika waktu sudah habis --}}
                    <span class="badge ps-5 pe-5 pt-2 pb-2 @if($laporan->keterangan == 'Sakit') bg-danger @elseif($laporan->keterangan == 'Izin') bg-primary @else bg-secondary @endif">
                      {{ $laporan->keterangan }}
                    </span>
                    @endif
                    @elseif ($now->between($mulai, $deadline))
                    {{-- Form upload HANYA untuk Opsi Lain --}}
                    <form action="{{ route('laporan.upload') }}" method="POST">
                      @csrf
                      <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                      <select name="keterangan" class="form-control form-control-sm mb-2" required>
                        <option value="">-- Pilih Opsi --</option>
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Haid">Haid</option>
                      </select>
                      <button class="btn btn-primary btn-sm mt-1" type="submit">Kirim</button>
                    </form>
                    @else
                    {{-- Jika waktu habis dan tidak ada laporan apapun --}}
                    <span class="text-muted small">
                      @if ($now->gt($deadline)) Ditutup @else Belum Mulai @endif
                    </span>
                    @endif
                  </td>
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