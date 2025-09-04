@extends('layouts.app')

@section('title', 'Profile')

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
    <h5 class="mb-3">Data Profile</h5>
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

        <div class="row align-items-center">
          <div class="col-md-12">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <div class="row">
                <div class="col-md-4 text-center mb-3">
                  <label for="foto" style="cursor: pointer;">
                    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px;">

                      <img
                        id="preview-foto"
                        src="{{ $profile->foto ? asset('storage/' . $profile->foto) : '' }}"
                        class="rounded-circle img-fluid {{ $profile->foto ? '' : 'd-none' }}"
                        style="height: 250px; width: 250px; object-fit: cover;">

                      <svg
                        id="svg-placeholder"
                        width="100%" height="250px" viewBox="0 0 24 24"
                        fill="none" stroke="#888" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="{{ $profile->foto ? 'd-none' : '' }}">
                        <circle cx="12" cy="8" r="4" />
                        <path d="M4 20c0-4 4-7 8-7s8 3 8 7" />
                      </svg>

                      <p class="mt-2 text-muted">Klik untuk ganti foto</p>
                    </div>
                  </label>

                  <input type="file" name="foto" id="foto" style="display: none;" accept="image/*">


                  @error('foto')
                  <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-8">

                  <div class="form-group mb-3">
                    <label for="username">Username:</label>
                    @if ($role === 'ADMINISTRATOR')
                    <input type="text" name="username" class="form-control" value="{{ old('username', $profile->username) }}">
                    @else
                    <input type="text" name="username" class="form-control" value="{{ old('username', $profile->username) }}" disabled>
                    <small><i>*Jika ingin mengubah Username Hubungi admin</i></small>
                    @endif

                    @error('username')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                  </div>

                  <div class="form-group mb-3">
                    <label for="nama">Nama Lengkap:</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $profile->nama) }}">
                    @error('nama')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label>Password Lama :</label>
                    <input type="password" name="password_lama" class="form-control">
                    @error('password_lama')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label>Password Baru :</label>
                    <input type="password" name="password" class="form-control">
                    @error('password')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label>Konfirmasi Password Baru :</label>
                    <input type="password" name="password_confirmation" class="form-control">
                  </div>

                  <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
              </div>
            </form>


          </div>
        </div>



      </div>
    </div>
  </div>
</div>


@endsection

@section('scripts')
<script>
  function close_alert() {
    $('.alertt').remove();
  }
  document.getElementById('foto').addEventListener('change', function(event) {
    const [file] = event.target.files;
    if (file) {
      const preview = document.getElementById('preview');
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    }
  });

  // preview foto
  document.getElementById('foto').addEventListener('change', function(event) {
    const fileInput = event.target;
    const file = fileInput.files[0];
    const previewImage = document.getElementById('preview-foto');
    const svgPlaceholder = document.getElementById('svg-placeholder');

    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        previewImage.src = e.target.result;
        previewImage.classList.remove('d-none');
        svgPlaceholder.classList.add('d-none');
      };
      reader.readAsDataURL(file);
    }
  });

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