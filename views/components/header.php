<!DOCTYPE html>
<html lang="en">


<head>
  <meta charset="utf-8" />
  <title><?php echo $title ?? '23.01.4989'; ?></title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta content="" name="keywords" />
  <meta content="" name="description" />
  <!-- Web Icon -->
  <link rel="shortcut icon" href="../../assets/images/logo-amikom.png" type="image/x-icon" />
  <!-- BS Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <!-- BS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <!-- Jquery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <!-- Sidebar Start -->
  <div class="sidebar position-absolute pt-1">
    <a href="index.html" class="navbar-brand d-flex align-items-center ms-4 mt-4">
      <img src="../../assets/images/logo-amikom.png" alt="" width="20%" class="ms-2" />
      <p class="primary-color fw-bolder fs-4 mb-0 mt-1 ms-3">AMIKOM</p>
    </a>
    <div class="navbar-nav w-100 mt-40px">
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="../admin/admin_dashboard.php" class="nav-item nav-link d-flex align-items-center unactive-sidebar mb-15px ps-4 <?= $isActive === 'dashboard' ? 'active' : ''; ?>">
          <i class="bi bi-house-door-fill fs-4 ms-2"></i><span class="ms-4">Dashboard</span>
        </a>
        <a href="../admin/mata_kuliah_management.php" class="nav-item nav-link d-flex align-items-center primary-color unactive-sidebar mb-15px ps-4 <?= $isActive === 'matakuliah management' ? 'active' : ''; ?>">
          <i class="bi bi-file-text-fill fs-4 ms-2"></i><span class="ms-4">Mata Kuliah Management</span>
        </a>
        <a href="../admin/dosen_management.php" class="nav-item nav-link d-flex align-items-center primary-color unactive-sidebar mb-15px ps-4 <?= $isActive === 'dosen management' ? 'active' : ''; ?>">
          <i class="bi bi-file-text-fill fs-4 ms-2"></i><span class="ms-4">Dosen Management</span>
        </a>
        <a href="../admin/mahasiswa_management.php" class="nav-item nav-link d-flex align-items-center primary-color unactive-sidebar mb-15px ps-4 <?= $isActive === 'mahasiswa management' ? 'active' : ''; ?>">
          <i class="bi bi-file-text-fill fs-4 ms-2"></i><span class="ms-4">Mahasiswa Management</span>
        </a>
        <a href="../admin/admin_profile.php" class="nav-item nav-link d-flex align-items-center unactive-sidebar mb-15px ps-4 <?= $isActive === 'profile' ? 'active' : ''; ?>">
          <i class="bi bi-person-fill fs-4 ms-2"></i><span class="ms-4">Accounts</span>
        </a>
      <?php endif; ?>

      <?php if ($_SESSION['role'] === 'dosen'): ?>
        <a href="../dosen/dosen_dashboard.php" class="nav-item nav-link d-flex align-items-center unactive-sidebar mb-15px ps-4 <?= $isActive === 'dashboard' ? 'active' : ''; ?>">
          <i class="bi bi-house-door-fill fs-4 ms-2"></i><span class="ms-4">Dashboard</span>
        </a>
        <a href="../dosen/mahasiswa_management.php" class="nav-item nav-link d-flex align-items-center primary-color unactive-sidebar mb-15px ps-4 <?= $isActive === 'mahasiswa management' ? 'active' : ''; ?>">
          <i class="bi bi-file-text-fill fs-4 ms-2"></i><span class="ms-4">Mahasiswa Management</span>
        </a>

        <a href="../dosen/dosen_profile.php" class="nav-item nav-link d-flex align-items-center unactive-sidebar mb-15px ps-4 <?= $isActive === 'profile' ? 'active' : ''; ?>">
          <i class="bi bi-person-fill fs-4 ms-2"></i><span class="ms-4">Accounts</span>
        </a>
      <?php endif; ?>

      <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
        <a href="../mahasiswa/mahasiswa_dashboard.php" class="nav-item nav-link d-flex align-items-center unactive-sidebar mb-15px ps-4 <?= $isActive === 'dashboard' ? 'active' : ''; ?>">
          <i class="bi bi-house-door-fill fs-4 ms-2"></i><span class="ms-4">Dashboard</span>
        </a>
        <a href="../mahasiswa/mahasiswa_management.php" class="nav-item nav-link d-flex align-items-center primary-color unactive-sidebar mb-15px ps-4 <?= $isActive === 'mahasiswa management' ? 'active' : ''; ?>">
          <i class="bi bi-file-text-fill fs-4 ms-2"></i><span class="ms-4">Mahasiswa Management</span>
        </a>
        <a href="../mahasiswa/mahasiswa_profile.php" class="nav-item nav-link d-flex align-items-center unactive-sidebar mb-15px ps-4 <?= $isActive === 'profile' ? 'active' : ''; ?>">
          <i class="bi bi-person-fill fs-4 ms-2"></i><span class="ms-4">Accounts</span>
        </a>

      <?php endif; ?>
    </div>
  </div>
  <!-- Sidebar End -->

  <!-- Content Start -->
  <div class="content">
    <!-- Navbar Start -->
    <nav class="navbar navbar-expand bg-lightcustom sticky-top px-4 py-3">
      <h1 class="fw-semibold fs-2 secondary-color pt-3 ps-3">APLIKASI DASHBOARD DOSEN AMIKOM</h1>
      <div class="navbar-nav ms-auto">
        <div class="nav-item d-flex align-items-center">
          <a href="#" class="nav-link sidebar-toggler flex-shrink-0">
            <i class="bi bi-list primary-color bg-tertiary fs-4 me-2"></i>
          </a>
          <div class="nav-link flex-shrink-0 hidden-icon mouse-pointer">
            <form id="logoutForm" action="../../controllers/Auth/LogoutController.php" method="post" style="display: none;">
              <button type="submit"></button>
            </form>
            <i class="bi bi-box-arrow-right primary-color bg-tertiary fs-4 me-2" onclick="document.getElementById('logoutForm').submit();"></i>
          </div>

          <a href="#" class="nav-link flex-shrink-0 hidden-icon">
            <i class="bi bi-gear-fill primary-color bg-tertiary fs-4 me-2"></i>
          </a>
        </div>
        <!-- <div class="nav-item">
          <a href="../admin/admin_profile.php" class="nav-link">
          </a>
        </div> -->
        <div class="dropdown nav-item position-relative mt-2">
          <img
            class="rounded-circle me-2"
            src="../../assets/images/p1.jpg"
            alt=""
            style="width: 40px; height: 40px; cursor: pointer;"
            data-bs-toggle="dropdown"
            aria-expanded="false" />

          <!-- Dropdown Menu -->
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="ms-3">
              <p>Name: <?php echo $_SESSION['username']; ?></p>
              <?php if ($_SESSION['role'] === 'admin'): ?>
                <p>Role: Admin</p>

              <?php endif; ?>
              <?php if ($_SESSION['role'] === 'dosen'): ?>
                <p>Role: Dosen</p>

              <?php endif; ?>
              <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                <p>Role: Mahasiswa</p>

              <?php endif; ?>
            </li>
            <hr>

            <li>
              <?php if ($_SESSION['role'] === 'admin'): ?>
                <a class="dropdown-item" href="../admin/admin_profile.php">My Profile</a>

              <?php endif; ?>
              <?php if ($_SESSION['role'] === 'dosen'): ?>
                <a class="dropdown-item" href="../dosen/dosen_profile.php">My Profile</a>

              <?php endif; ?>
              <?php if ($_SESSION['role'] === 'mahasiswa'): ?>
                <a class="dropdown-item" href="../mahasiswa/mahasiswa_profile.php">My Profile</a>

              <?php endif; ?>
            </li>
            <hr>
            <li class="mouse-pointer">
              <form id="logoutForm" action="../../controllers/Auth/LogoutController.php" method="post" style="display: none;">
                <button type="submit"></button>
              </form>
              <p class="dropdown-item" onclick="document.getElementById('logoutForm').submit();">Logout</p>
            </li>
          </ul>
        </div>

      </div>
    </nav>
    <!-- Navbar End -->
    <!--  Main Start -->