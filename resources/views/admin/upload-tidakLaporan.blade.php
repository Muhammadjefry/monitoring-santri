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
    <!-- <h5 class="mb-2">Laporan Kegiatan/Rekap Laporan</h5> -->
    <h5 class="mb-2">
      Laporan Kegiatan
      @if($type === 'uploaded') - Sudah Diupload
      @elseif($type === 'not_uploaded') - Belum/Tidak Diupload
      @endif
    </h5>


  </div>
</div>

<div class="row text-center mb-5">
  <div class="col-md-12">
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary m-0 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
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
                  <th>Nama Santri</th>
                  <th>Nama Orang Tua</th>
                  <th>No HP Ortu</th>
                  <th>Total Jadwal</th>
                  <th>Sudah Lapor</th>
                  <th>Belum Lapor</th>
                  <th>Tidak Lapor</th>
                  <th>Persentase</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rekap as $i => $data)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $data->nama }}</td>
                  <td class="text-center">{{ $data->nama_ortu ?? '-' }}</td>
                  <td class="text-center">{{ $data->no_hp_ortu ?? '-' }}</td>
                  <td>{{ $data->total_jadwal }}</td>
                  <td>{{ $data->sudah_lapor }}</td>
                  <td>{{ $data->belum_lapor }}</td>
                  <td>{{ $data->tidak_lapor }}</td>
                  <td>{{ $data->persentase }}%</td>
                  <td>
                    <a href="{{ route('admin.detailLaporan', ['santri_id' => $data->santri_id]) }}" class="btn btn-success  btn-sm">
                      <i class="bi bi-search me-2"></i>Detail
                    </a>
                    <form action="{{ route('admin.send.rekap', $data->santri_id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i> Kirim PDF
                      </button>
                    </form>

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