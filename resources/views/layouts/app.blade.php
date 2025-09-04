<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Dashboard')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/logo.png') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  @yield('style')
  <style>
    .text-wrap {
      white-space: normal !important;
      word-break: break-word;
    }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
  <div class="app-wrapper">

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Sidebar --}}
    @include('partials.sidebar')

    <main class="app-main">
      @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="app-footer">
      <strong>
        Copyright &copy; {{ date('Y') }}
      </strong> All rights reserved.
    </footer>

  </div>

  {{-- Scripts --}}


  <script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    crossorigin="anonymous"></script>
  <script src="{{ asset('dist/js/adminlte.js') }}"></script>
  <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

  @yield('scripts')
</body>

</html>