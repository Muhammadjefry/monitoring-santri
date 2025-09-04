<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a
          class="nav-link"
          data-lte-toggle="sidebar"
          href="#"
          role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>

    </ul>
    <ul class="navbar-nav ms-auto">
      @if($role === 'SANTRI' || $role === 'ORANG TUA')

      @php
      $unreadCount = $notifications->where('is_read', false)->count();
      @endphp

      <li class="nav-item dropdown">
        <a class="nav-link" data-bs-toggle="dropdown" href="#">
          <i class="bi bi-bell-fill"></i>
          @if ($unreadCount > 0)
          <span class="navbar-badge badge text-bg-warning">{{ $unreadCount }}</span>
          @endif
        </a>

        <div class="dropdown-menu dropdown-menu-xl dropdown-menu-end">
          <span class="dropdown-item dropdown-header">
            {{ $unreadCount }} Notifikasi Baru
          </span>

          @forelse ($notifications as $notif)
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="bi bi-info-circle me-2 {{ $notif->is_read ? 'text-muted' : 'text-primary' }}"></i>
            <strong>{{ $notif->judul }}</strong>
            <span class="float-end text-secondary fs-7">
              {{ $notif->created_at->diffForHumans() }}
            </span>
            <div class="small text-muted text-wrap">
              {{ $notif->pesan }}
            </div>
          </a>
          @empty
          <div class="dropdown-divider"></div>
          <span class="dropdown-item text-muted text-center">Tidak ada notifikasi</span>
          @endforelse

          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('notifikasi.baca_semua') }}" class="dropdown-item text-center">
            @csrf
            <button type="submit" class="btn btn-sm btn-link">Tandai Semua Dibaca</button>
          </form>
        </div>
      </li>
      @endif



      <li class="nav-item dropdown user-menu">
        <a
          href="#"
          class="nav-link dropdown-toggle"
          data-bs-toggle="dropdown">

          @if (!is_null($profile) && $profile->foto)
          <img src="{{ asset('storage/' . $profile->foto) }}"
            class="user-image rounded-circle shadow"
            alt="User Image">
          @else
          <svg class="user-image rounded-circle shadow"
            alt="User Image" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="1.5"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4" />
            <path d="M4 20c0-4 4-7 8-7s8 3 8 7" />
          </svg>
          @endif

          @php
          use App\Models\Admin;
          use App\Models\Santri;
          use App\Models\Ortu;

          $userId = session('user_id');
          $role = session('role');
          $nama = 'Pengguna';

          if ($role === 'ADMINISTRATOR') {
          $admin = Admin::where('user_id', $userId)->first();
          $nama = $admin?->nama ?? 'Admin';
          } elseif ($role === 'SANTRI') {
          $santri = Santri::where('user_id', $userId)->first();
          $nama = $santri?->nama ?? 'Santri';
          } elseif ($role === 'ORANG TUA') {
          $ortu = Ortu::where('user_id', $userId)->first();
          $nama = $ortu?->nama ?? 'Orang Tua';
          }

          @endphp

          @if(session()->has('user_id'))
          <span class="d-none d-md-inline">{{ $nama }}</span>
          @else
          <span class="text-danger">Belum login</span>
          @endif

        </a>
        <ul class="dropdown-menu dropdown-menu-xl dropdown-menu-end">
          <li class="user-header text-bg-primary">

            @if (!is_null($profile) && $profile->foto)
            <img src="{{ asset('storage/' . $profile->foto) }}"
              class="rounded-circle shadow"
              alt="User Image">
            @else
            <svg style="width: 50%;" class="rounded-circle shadow"
              alt="User Image" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="1.5"
              stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="8" r="4" />
              <path d="M4 20c0-4 4-7 8-7s8 3 8 7" />
            </svg>
            @endif
            <p>
              @if(session()->has('user_id'))
              <span class="d-none d-md-inline">{{ $nama }}</span>
              <small><span class="d-none d-md-inline">{{ $role }}</span></small>
              @else
              <span class="text-danger">Belum login</span>
              @endif
            </p>
          </li>
          <li class="user-footer text-center">
            <a href="{{ route('profile') }}" class="btn btn-default">Profile</a>
            <a href="{{ route('logout') }}" onclick="return confirm('Yakin ingin logout?')" class="btn btn-default">Log out</a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>