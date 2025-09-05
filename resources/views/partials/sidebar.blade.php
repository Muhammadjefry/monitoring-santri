<aside class="app-sidebar bg-primary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="#" class="brand-link">

      <span class="brand-text m-0 fw-light text-white">
        <img src="{{ asset('assets/images/logo.png') }}" style="width:50px" alt="Logo">
        <span>Monitoring Santri</span>
      </span>
    </a>
  </div>
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul
        class="nav sidebar-menu flex-column"
        data-lte-toggle="treeview"
        role="navigation"
        aria-label="Main navigation"
        data-accordion="false"
        id="navigation">

        <li class="nav-header">Home</li>
        <li class="nav-item">
          @if($role === 'ADMINISTRATOR')
          <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house"></i>
            <p>Dashboard</p>
          </a>
          @elseif($role === 'SANTRI')
          <a href="{{ route('santri.dashboard') }}" class="nav-link text-white {{ request()->routeIs('santri.dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house"></i>
            <p>Dashboard</p>
          </a>
          @elseif($role === 'ORANG TUA')
          <a href="{{ route('ortu.dashboard') }}" class="nav-link text-white {{ request()->routeIs('ortu.dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house"></i>
            <p>Dashboard</p>
          </a>
          @endif
        </li>
        <li class="nav-header">Menu Utama</li>
        <li class="nav-item">

          @if($role === 'ADMINISTRATOR')
          <a href="{{ route('admin.jadwal') }}" class="nav-link text-white {{ request()->routeIs('admin.jadwal') ? 'active' : '' }}">
            <i class="nav-icon bi bi-calendar"></i>
            <p>Jadwal Kegiatan</p>
          </a>
          @elseif($role === 'SANTRI')
          <a href="{{ route('santri.jadwal') }}" class="nav-link text-white {{ request()->routeIs('santri.jadwal') ? 'active' : '' }}">
            <i class="nav-icon bi bi-calendar"></i>
            <p>Jadwal Kegiatan</p>
          </a>
          @elseif($role === 'ORANG TUA')
          <a href="{{ route('ortu.jadwal') }}" class="nav-link text-white {{ request()->routeIs('ortu.jadwal') ? 'active' : '' }}">
            <i class="nav-icon bi bi-calendar"></i>
            <p>Jadwal Kegiatan</p>
          </a>
          @endif


        </li>
        <li class="nav-item">

          @if($role === 'ADMINISTRATOR')
          <a href="{{ route('laporanMasuk') }}" class="nav-link text-white {{ request()->routeIs('laporanMasuk', 'admin.rekapLaporan') ? 'active' : '' }}">
            <i class="nav-icon bi bi-box-arrow-in-right"></i>
            <p>Laporan Masuk</p>
          </a>
          @elseif($role === 'SANTRI')
          <a href="{{ route('santri.laporan') }}"
            class="nav-link text-white {{ request()->routeIs('santri.laporan', 'santri.rekapLaporan') ? 'active' : '' }}">
            <i class="nav-icon bi bi-box-arrow-in-right"></i>
            <p>Isi Laporan</p>
          </a>

          @elseif($role === 'ORANG TUA')
          <a href="{{ route('ortu.laporan') }}" class="nav-link text-white {{ request()->routeIs('ortu.laporan') ? 'active' : '' }}">
            <i class="nav-icon bi bi-box-arrow-in-right"></i>
            <p>Laporan Santri</p>
          </a>
          @endif

        </li>
        <li class="nav-header">Users</li>

        @if($role === 'ADMINISTRATOR')
        <li class="nav-item {{ request()->routeIs('admin.santri', 'admin.santriDetail') || request()->routeIs('admin.ortu') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link  text-white {{ request()->routeIs('admin.santri', 'admin.santriDetail') || request()->routeIs('admin.ortu') ? 'active' : '' }}">
            <i class="nav-icon bi bi-people"></i>
            <p>
              Akun Pengguna
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.santri') }}" class="nav-link text-white {{ request()->routeIs('admin.santri', 'admin.santriDetail') ? 'active' : '' }}">
                <i class="nav-icon bi bi-circle"></i>
                <p>Santri</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.ortu') }}" class="nav-link  text-white {{ request()->routeIs('admin.ortu') ? 'active' : '' }}">
                <i class="nav-icon bi bi-circle"></i>
                <p>Orang tua</p>
              </a>
            </li>
          </ul>
        </li>
        @endif


        <li class="nav-item">
          <a href="{{ route('profile') }}" class="nav-link text-white {{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="nav-icon bi bi-person"></i>
            <p>Profile</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('logout') }}" onclick="return confirm('Yakin ingin logout?')" class="nav-link text-white">
            <i class="nav-icon bi bi-box-arrow-in-right"></i>
            <p>Logout</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>