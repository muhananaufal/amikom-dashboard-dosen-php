<?php
session_start();

// Cek apakah sudah login, jika ya, redirect ke dashboard sesuai peran
if (isset($_SESSION['role'])) {
  switch ($_SESSION['role']) {
    case 'admin':
      header('Location: /views/admin/admin_dashboard.php');
      exit;
    case 'dosen':
      header('Location: /views/dosen/dosen_dashboard.php');
      exit;
    case 'mahasiswa':
      header('Location: /views/mahasiswa/mahasiswa_dashboard.php');
      exit;
  }
}

require_once '../../config/db.php';
require_once '../../controllers/Auth/LoginController.php';

$pdo = connectDatabase();
$loginController = new LoginController($pdo);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $role = $_POST['role'];
  $password = $_POST['password'];

  // Sesuaikan input berdasarkan role
  switch ($role) {
    case 'admin':
      $username = $_POST['username'];
      $loginSuccess = $loginController->loginAdmin($username, $password);
      break;
    case 'dosen':
      $nidn = $_POST['nidn'];
      $loginSuccess = $loginController->loginDosen($nidn, $password);
      break;
    case 'mahasiswa':
      $nim = $_POST['nim'];
      $loginSuccess = $loginController->loginMahasiswa($nim, $password);
      break;
    default:
      $loginSuccess = false;
  }

  if ($loginSuccess) {
    // Redirect sesuai dengan peran pengguna
    switch ($_SESSION['role']) {
      case 'admin':
        header('Location: /views/admin/admin_dashboard.php');
        break;
      case 'dosen':
        header('Location: /views/dosen/dosen_dashboard.php');
        break;
      case 'mahasiswa':
        header('Location: /views/mahasiswa/mahasiswa_dashboard.php');
        break;
    }
    exit;
  } else {
    $error = 'Login gagal, periksa kembali data yang Anda masukkan.';
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <title>Login</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />

  <!-- Web Icon -->
  <link rel="shortcut icon" href="../../assets/images/logo-amikom.png" type="image/x-icon" />
  <!-- BS Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <!-- BS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/css/style.css" />

  <script>
    function toggleInputFields() {
      const role = document.getElementById('role').value;
      document.getElementById('usernameField').style.display = role === 'admin' ? 'block' : 'none';
      document.getElementById('nidnField').style.display = role === 'dosen' ? 'block' : 'none';
      document.getElementById('nimField').style.display = role === 'mahasiswa' ? 'block' : 'none';
    }
  </script>
</head>

<body>
  <div class="container-fluid">
    <div class="row d-flex flex-md-row-reverse align-items-center">
      <div class="col-12 col-md-7 col-lg-8 bg-hero d-flex justify-content-center align-items-center" style="min-height: 100vh; overflow: hidden">
        <img class="rounded img-fluid" src="../../assets/images/login.png" />
      </div>
      <div class="col-12 col-md-5 col-lg-4 ps-4 d-flex justify-content-center align-items-center" style="min-height: 100vh">
        <div class="row w-100">
          <div class="col-12 mb-2 text-center">
            <div class="text-decoration-none d-flex align-items-center justify-content-center mb-2 mb-xl-3">
              <img src="../../assets/images/logo-amikom.png" alt="" class="img-fluid" width="15%" />
              <h1 class="primary-color fw-bolder ms-3 mt-1 d-inline">AMIKOM</h1>
            </div>
            <h2 class="fw-bold fs-5 secondary-color pt-1">Login into your account</h2>
          </div>

          <!-- Display error message if login fails -->
          <?php if ($error): ?>
            <p class="error text-center" style="color: red;"><?php echo htmlspecialchars($error); ?></p>
          <?php endif; ?>

          <form class="col-12 mt-4" method="POST" action="">
            <div class="mb-3 px-xxl-5">
              <label for="role" class="form-label">Pilih Role</label>
              <select name="role" id="role" onchange="toggleInputFields()" class="form-select" required style="height: 50px;">
                <option value="" disabled selected>Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="dosen">Dosen</option>
                <option value="mahasiswa">Mahasiswa</option>
              </select>
            </div>

            <div id="usernameField" style="display: none;" class="mb-3 px-xxl-5">
              <label for="username" class="form-label">Username (Admin)</label>
              <input type="text" name="username" id="username" class="form-control" placeholder="Username" style="height: 50px;">
            </div>

            <div id="nidnField" style="display: none;" class="mb-3 px-xxl-5">
              <label for="nidn" class="form-label">NIDN (Dosen)</label>
              <input type="text" name="nidn" id="nidn" class="form-control" placeholder="NIDN" style="height: 50px;">
            </div>

            <div id="nimField" style="display: none;" class="mb-3 px-xxl-5">
              <label for="nim" class="form-label">NIM (Mahasiswa)</label>
              <input type="text" name="nim" id="nim" class="form-control" placeholder="NIM" style="height: 50px;">
            </div>

            <div class="mb-3 px-xxl-5">
              <label for="password" class="form-label">Password</label>
              <div class="input-container position-relative">
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                <i class="bi bi-lock-fill input-icon"></i>
              </div>
            </div>

            <div class="mt-3 d-flex align-items-center justify-content-center px-xxl-5">
              <button type="submit" class="btn btn-submit fw-bolder w-100" style="max-width: 410px; height: 50px">Login Now</button>
            </div>
          </form>

          <div class="divider mt-4">OR</div>
          <div class="mt-3 d-flex align-items-center justify-content-center px-xxl-5">
            <a href="./register.php" class="btn btn-submit-outline fw-bolder w-100 text-decoration-none d-flex align-items-center justify-content-center" style="max-width: 410px; height: 50px">Signup Now</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- BS Javascript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>