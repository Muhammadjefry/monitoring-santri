@extends('layouts.app')

@section('title', 'Jadwal Kegiatan')

@section('style')
<style>
  .password-wrapper {
    position: relative;
  }

  .password-wrapper input {
    width: 100%;
    padding-right: 40px;
  }

  .toggle-password {
    position: absolute;
    top: 70%;
    right: 12px;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 1.2rem;
  }

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
    <h5 class="mb-3">Jadwal Kegiatan Santri</h5>
    @php
    $role = session('role');

    if ($role === 'ADMINISTRATOR'){

    @endphp

    <button onclick="modal_add()" class="btn btn-primary m-0">Tambah Jadwal</button>

    @php } @endphp
  </div>
</div>




<div class="app-content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-md-12">

        @php
        $role = session('role');
        if ($role === 'ADMINISTRATOR'){

        @endphp

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

        @php } @endphp

        <div class="card mb-4">

          <div class="card-body">
            <table id="myTable" class="table table-bordered">
              <thead>
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Tanggal</th>
                  <th>Nama Kegiatan</th>
                  <th>Waktu Mulai</th>
                  <th>Waktu Selesai</th>

                  @php
                  $role = session('role');
                  if ($role === 'ADMINISTRATOR'){

                  @endphp

                  <th>Aksi</th>
                  @php
                  }
                  @endphp

                </tr>
              </thead>
              <tbody>

                @foreach ($jadwals as $i => $jadwal)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $jadwal->tanggal }}</td>
                  <td>{{ $jadwal->judul_kegiatan }}</td>
                  <td>{{ $jadwal->waktu_mulai }}</td>
                  <td>{{ $jadwal->waktu_selesai }}</td>

                  @php
                  $role = session('role');
                  if ($role === 'ADMINISTRATOR'){

                  @endphp
                  <td class="d-flex justify-content-center gap-2">
                    <button
                      class="btn btn-warning btn-sm"
                      title="Edit Data"
                      data-bs-toggle="modal"
                      data-bs-target="#modal-edit"
                      onclick="modal_edit('{{ $jadwal->id }}', '{{ $jadwal->tanggal }}', '{{ $jadwal->judul_kegiatan }}', '{{ $jadwal->waktu_mulai }}', '{{ $jadwal->waktu_selesai }}')">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button onclick="modal_hapus('{{ $jadwal->id }}')" class="btn btn-danger btn-sm" title="Delete Data">
                      <i class="bi bi-trash"></i>
                    </button>

                  </td>
                  @php
                  }
                  @endphp
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


<!-- // Modal Tambah Santri -->
<div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between">
        <h5 class="modal-title">Tambah jadwal kegiatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('jadwal.store') }}" method="POST">
        @csrf
        <div class="modal-body">

          <div class="mb-3">
            <label class="mb-1">Tanggal :</label>
            <input type="date" name="tanggal" class="form-control" placeholder="Ketik di sini" required>
          </div>

          <div class="mb-3">
            <label class="mb-1">Nama Kegiatan :</label>
            <select name="judul_kegiatan" class="form-select" required>
              <option value="">-- Pilih Kegiatan --</option>
              <option value="Tadarus">Tadarus</option>
              <option value="Tarawih">Tarawih</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="mb-1">Waktu Mulai :</label>
            <input type="time" name="waktu_mulai" class="form-control" placeholder="Ketik di sini" required>
          </div>

          <div class="mb-3">
            <label class="mb-1">Waktu Selesai :</label>
            <input type="time" name="waktu_selesai" class="form-control" placeholder="Ketik di sini" required>
          </div>


        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modal-edit" tabindex="-1">
  <div class="modal-dialog">


    <form id="form-edit" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Pengguna</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit-id" name="id">

          <div class="mb-2">
            <label class="mb-1">Tanggal :</label>
            <input type="date" id="edit-tanggal" name="tanggal" class="form-control mb-2" placeholder="Username">
          </div>

          <div class="mb-3">
            <label class="mb-1">Nama Kegiatan :</label>
            <select id="edit-judul_kegiatan" name="judul_kegiatan" class="form-select mb-2" required>
              <option value="">-- Pilih Kegiatan --</option>
              <option value="Tadarus">Tadarus</option>
              <option value="Tarawih">Tarawih</option>
            </select>
          </div>



          <div class="mb-2">
            <label class="mb-1">Waktu Mulai :</label>
            <input type="time" id="edit-waktu_mulai" name="waktu_mulai" class="form-control mb-2" placeholder="Kamar">
          </div>
          <div class="mb-2">
            <label class="mb-1">Waktu Selesai :</label>
            <input type="time" id="edit-waktu_selesai" name="waktu_selesai" class="form-control mb-2" placeholder="Kamar">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Modal Hapus -->
<div class="modal fade" id="modal-hapus" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('jadwal.delete') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <p>Anda yakin ingin menghapus jadwal ini?</p>
          <input type="hidden" name="id" id="hapus-id">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </div>
    </form>
  </div>
</div>



@endsection

@section('scripts')
<script>
  let table = new DataTable('#myTable');

  function modal_add() {
    $('#modal-add').modal('show');
    $('#modal-add  form')[0].reset();
    $('#modal-add .modal-title').text('Tambah Santri');
  }

  function modal_edit(id, tanggal, judul_kegiatan, waktu_mulai, waktu_selesai) {
    $('#edit-id').val(id);
    $('#edit-tanggal').val(tanggal);
    $('#edit-judul_kegiatan').val(judul_kegiatan);
    $('#edit-waktu_mulai').val(waktu_mulai);
    $('#edit-waktu_selesai').val(waktu_selesai);

    // Set action URL dengan id
    $('#form-edit').attr('action', '/admin/jadwalKegiatan/' + id);

    $('#modal-edit').modal('show');
  }

  function modal_hapus(id) {
    $('#modal-hapus').modal('show');
    $('#hapus-id').val(id);
  }

  function close_modal() {
    $('.modal').modal('hide');
  }

  function close_alert() {
    $('.alertt').remove();
  }


  // Toggle password visibility
  function togglePassword() {
    const pass = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");

    if (pass.type === "password") {
      pass.type = "text";
      icon.textContent = "👁️"; // mata terbuka
    } else {
      pass.type = "password";
      icon.textContent = "🙈"; // mata tertutup
    }
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