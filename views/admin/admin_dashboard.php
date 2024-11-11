<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  header('Location: /views/auth/login.php');
  exit;
}

$isActive = 'dashboard';
$title = 'Admin Dashboard';

require_once '../components/header.php';
?>



<div class="bg-tertiary pt-40px pe-40px ps-40px" style="min-height: 100vh">
  <h2 class="ps-4px fw-semibold fs-5 mb-2 secondary-color">HALAMAN DASHBOARD</h2>
  <div class="row g-4 pt-2">
    <div class="col-12">
      <div class="shadow-sm bg-lightcustom rounded-4 d-flex align-items-center px-3 py-4 mb-4">
        <div class="bg-primary rounded-4 p-3 me-3"><i class="bi bi-bookmark-dash-fill text-white fs-4"></i></div>
        <div class="d-flex justify-content-between w-100 row mb-3 pt-md-3 ms-xl-4">
          <div class="col-12 col-md-3 align-content-center">
            <a href="./mata_kuliah_management.php" class=" text-dark">Manage your Mata Kuliah</a>
          </div>
          <div class="col-12 col-md-3 align-content-center">
            <a class=" text-dark" href="./dosen_management.php">Manage your Dosen</a>
          </div>
          <div class="col-12 col-md-3 align-content-center">
            <a href="./mahasiswa_management.php" class=" text-dark">Manage your Mahasiswa</a>
          </div>
          <div class="col-12 col-md-3 align-content-center">
            <button class="btn btn-content" style="width: 130px">QRCode</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once '../components/footer.php'; ?>