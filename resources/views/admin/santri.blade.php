@extends('layouts.app')

@section('title', 'User Santri')

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
    <h5 class="mb-3">Data Santri</h5>
    <button onclick="modal_add()" class="btn btn-primary m-0">Tambah Santri</button>
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
                  <th>Username</th>
                  <th>Nama</th>
                  <th>Kamar</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($santris as $i => $santri)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $santri->user->username }}</td>
                  <td>{{ $santri->nama }}</td>
                  <td>{{ $santri->kamar }}</td>
                  <td class="d-flex justify-content-center gap-2">
                    <button
                      class="btn btn-warning btn-sm"
                      title="Edit Data"
                      data-bs-toggle="modal"
                      data-bs-target="#modal-edit"
                      onclick="modal_edit('{{ $santri->user->id }}', '{{ $santri->user->username }}', '{{ $santri->nama }}', '{{ $santri->kamar }}')">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button onclick="modal_hapus('{{ $santri->user->id }}')" class="btn btn-danger btn-sm" title="Delete Data">
                      <i class="bi bi-trash"></i>
                    </button>

                    <button onclick="resetPassword('{{ $santri->user->id }}')" class="btn btn-secondary btn-sm" title="Reset Password">
                      <i class="bi bi-lock-fill"></i>
                    </button>
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


<!-- // Modal Tambah Santri -->
<div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between">
        <h5 class="modal-title">Tambah Santri</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('admin_santri.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="role" value="SANTRI">

          <div class="mb-2">
            <label class="mb-1">Username:</label>
            <input type="text" name="username" class="form-control" placeholder="Ketik di sini" required>
          </div>

          <div class="mb-2">
            <label class="mb-1">Nama:</label>
            <input type="text" name="nama" class="form-control" placeholder="Ketik di sini">
          </div>

          <div class="mb-2">
            <label class="mb-1">Kamar :</label>
            <select name="kamar" class="form-control mb-2" required>
              <option value="">-- Pilih Kamar --</option>
              <option value="A-01">A-01</option>
              <option value="A-02">A-02</option>
              <option value="A-03">A-03</option>
              <option value="A-04">A-04</option>
              <option value="B-01">B-01</option>
              <option value="B-02">B-02</option>
              <option value="B-03">B-03</option>
              <option value="B-04">B-04</option>
              <option value="C-01">C-01</option>
              <option value="C-02">C-02</option>
              <option value="C-03">C-03</option>
              <option value="D-01">D-01</option>
              <option value="D-02">D-02</option>
              <option value="D-03">D-03</option>
            </select>
          </div>


          <div class="password-wrapper mb-2">
            <label class="mb-1">Password:</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Password" required />
            <span class="toggle-password" onclick="togglePassword()" id="toggleIcon">🙈</span>
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
            <label class="mb-1">Username :</label>
            <input type="text" id="edit-username" name="username" class="form-control mb-2" placeholder="Username">
          </div>

          <div class="mb-2">
            <label for="nama" class="mb-1">Nama :</label>
            <input type="text" id="edit-nama" name="nama" class="form-control mb-2" placeholder="Nama">
          </div>

          <div class="mb-2">
            <label class="mb-1">Kamar :</label>
            <select id="edit-kamar" name="kamar" class="form-control mb-2" required>
              <option value="">-- Pilih Kamar --</option>
              <option value="A-01">A-01</option>
              <option value="A-02">A-02</option>
              <option value="A-03">A-03</option>
              <option value="A-04">A-04</option>
              <option value="B-01">B-01</option>
              <option value="B-02">B-02</option>
              <option value="B-03">B-03</option>
              <option value="B-04">B-04</option>
              <option value="C-01">C-01</option>
              <option value="C-02">C-02</option>
              <option value="C-03">C-03</option>
              <option value="D-01">D-01</option>
              <option value="D-02">D-02</option>
              <option value="D-03">D-03</option>
            </select>
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
    <form method="POST" action="{{ route('admin_santri.delete') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Hapus</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <p>Anda yakin ingin menghapus user ini?</p>
          <input type="hidden" name="id" id="hapus-id">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Modal Reset Password -->
<div class="modal fade" id="modal-reset" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin_santri.reset_password') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konfirmasi Reset Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <p>Anda yakin ingin mereset password user ini?</p>
          <input type="hidden" name="id" id="reset-id">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Ya, Reset</button>
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

  function modal_edit(id, username, nama, kamar) {
    $('#edit-id').val(id);
    $('#edit-username').val(username);
    $('#edit-nama').val(nama);
    $('#edit-kamar').val(kamar);

    // Set form action secara dinamis
    let url = `/admin/santri/${id}`;
    $('#form-edit').attr('action', url);

    $('#modal-edit').modal('show');
  }

  function modal_hapus(id) {
    $('#modal-hapus').modal('show');
    $('#hapus-id').val(id);
  }

  function resetPassword(id) {
    $('#modal-reset').modal('show');
    $('#reset-id').val(id);
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