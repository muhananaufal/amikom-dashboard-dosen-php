<?php
session_start(); // Pastikan session_start dipanggil di awal file

// Redirect jika pengguna sudah login
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
require_once '../../controllers/Auth/RegisterController.php';
require_once '../../controllers/Auth/LoginController.php';

$pdo = connectDatabase();
$registerController = new RegisterController($pdo);
$loginController = new LoginController($pdo);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $role = 'admin'; // Set default role ke 'admin'

  // Proses registrasi dan login otomatis
  if ($registerController->register($username, $password, $role)) {
    if ($loginController->loginAdmin($username, $password)) {
      header('Location: /views/admin/admin_dashboard.php'); // redirect ke dashboard admin
      exit;
    } else {
      $error = 'Pendaftaran berhasil, tetapi login otomatis gagal.';
    }
  } else {
    $error = 'Gagal menambahkan pengguna. Username mungkin sudah digunakan.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <!-- Web Icon -->
  <link rel="shortcut icon" href="../../assets/images/logo-amikom.png" type="image/x-icon" />
  <!-- BS Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <!-- BS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/css/style.css" />
</head>

<body>
  <div class="container-fluid">
    <div class="row d-flex flex-md-row-reverse align-items-center">
      <div class="col-12 col-md-7 col-lg-8 bg-hero d-flex justify-content-center align-items-center" style="min-height: 100vh; overflow: hidden">
        <img class="rounded img-fluid" src="../../assets/images/register.png" />
      </div>
      <div class="col-12 col-md-5 col-lg-4 ps-4 d-flex justify-content-center align-items-center" style="min-height: 100vh">
        <div class="row w-100">
          <div class="col-12 mb-2 text-center">
            <div class="text-decoration-none d-flex align-items-center justify-content-center mb-2 mb-xl-3">
              <img src="../../assets/images/logo-amikom.png" alt="" class="img-fluid" width="15%" />
              <h1 class="primary-color fw-bolder ms-3 mt-1 d-inline">AMIKOM</h1>
            </div>
            <h2 class="fw-bold fs-5 secondary-color pt-1">Create your new Admin account</h2>
          </div>
          <?php if ($error): ?>
            <p class="text-danger text-center"><?php echo htmlspecialchars($error); ?></p>
          <?php endif; ?>
          <form action="" method="post" class="col-12 mt-4">
            <div class="mb-3 px-xxl-5">
              <label for="username" class="form-label form-label-custom-color">Username</label>
              <div class="input-container position-relative">
                <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username" required />
                <i class="bi bi-person input-icon"></i>
              </div>
            </div>
            <div class="mb-3 px-xxl-5">
              <label for="password" class="form-label form-label-custom-color">Password</label>
              <div class="input-container position-relative">
                <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required />
                <i class="bi bi-lock-fill input-icon"></i>
              </div>
            </div>
            <!-- <div class="d-flex justify-content-end mb-2 px-xxl-5">
              <a href="./login.php" class="primary-color small">Already have an account?</a>
            </div> -->
            <div class="mt-3 d-flex align-items-center justify-content-center px-xxl-5">
              <button type="submit" class="btn btn-submit fw-bolder w-100 text-decoration-none d-flex align-items-center justify-content-center" style="max-width: 410px; height: 50px">Signup Now</button>
            </div>
          </form>
          <div class="divider mt-4">OR</div>
          <div class="mt-3 d-flex align-items-center justify-content-center px-xxl-5">
            <a href="./login.php" class="btn btn-submit-outline fw-bolder w-100 text-decoration-none d-flex align-items-center justify-content-center" style="max-width: 410px; height: 50px">Login Now</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- BS Javascript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>