<!-- resources/views/login.blade.php -->
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Monitoring Santri</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/logo.png') }}">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #d0e7f7;
      background: url('../assets/images/bg.jpeg') no-repeat center center fixed;
      background-size: cover;
    }

    .login-container {
      background: white;
      padding: 2rem;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .login-container h2 {
      margin-bottom: 1.5rem;
      color: #2c3e50;
    }

    .login-container p {
      font-style: italic;
      color: #2980b9;
      margin-bottom: 2rem;
    }

    .login-container input,
    .login-container select,
    .login-container button {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    .login-container button {
      background-color: #3498db;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
    }

    .login-container button:hover {
      background-color: #2980b9;
    }


    .login-header {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }

    .login-header img {
      width: 100px;
      height: auto;
    }

    .login-text {
      flex: 1;
    }

    .login-text h2 {
      margin: 0;
      font-size: 1.5rem;
    }

    .login-text p {
      margin: 4px 0 0;
      font-size: 0.5rem;
      color: #666;
    }




    /* password Toggle */
    .password-wrapper {
      position: relative;
    }

    .password-wrapper input {
      width: 100%;
      padding-right: 40px;
    }

    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 1.2rem;
    }

    /* Alert Style */
    .alert {
      position: relative;
      opacity: 0;
      transform: translateY(-100%);
      transition: all 0.5s ease-in-out;

      background-color: #fef3c7;
      border: 1px solid #fcd34d;
      padding: 15px;
      color: #92400e;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
    }

    .alert.slide-down.show {
      opacity: 1;
      transform: translateY(0);
    }

    .footer {
      margin-top: 1.5rem;
      font-size: 0.9rem;
      color: #7f8c8d;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 1rem;
      }
    }

    @media (max-width: 500px) {
      .login-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .login-text h2 {
        font-size: 1.2rem;
      }
    }
  </style>
</head>

<body>
  <div class="wrapper" style="margin: 20px;">

    @if(session('error'))
    <div class="alert slide-down" id="alert">
      <span class="text-center" style="font-weight: bold; color: #e74c3c; font-size: 14px;">
        <strong>⚠️ Gagal Login!</strong><br>
      </span>
      {{ session('error') }}
    </div>
    @endif

    <div class="login-container">
      <div class="login-header">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        <div class="login-text">
          <h2>Login Monitoring Santri</h2>
          <p>"Belajar hari ini, berakhlak untuk selamanya."</p>
        </div>
      </div>

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="text" name="username" placeholder="Username" required>

        <div class="password-wrapper">
          <input type="password" name="password" id="password" placeholder="Password" required />
          <span class="toggle-password" onclick="togglePassword()" id="toggleIcon">🙈</span>
        </div>

        <select name="role" required>
          <option value="">-- Pilih Peran --</option>
          <option value="ADMINISTRATOR">Administrator</option>
          <option value="SANTRI">Santri</option>
          <option value="ORANG TUA">Orang Tua</option>
        </select>

        <button type="submit">Masuk</button>


      </form>

      <div class="footer">© <?= date('Y') ?> Monitoring Santri</div>
    </div>

  </div>

  <script>
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
      const alert = document.getElementById('alert');
      if (alert) {
        setTimeout(() => {
          alert.classList.add('show');
        }, 100);

        setTimeout(() => {
          alert.classList.remove('show');

          setTimeout(() => {
            alert.remove();
          }, 500);
        }, 4000);
      }
    });
  </script>

</body>

</html>