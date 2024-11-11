<?php
session_start();
if ($_SESSION['role'] !== 'dosen') {
  header('Location: /views/auth/login.php');
  exit;
}

$isActive = 'dashboard';
$title = 'Dosen Dashboard';
require_once '../components/header.php';
require_once '../../controllers/DosenController.php';

$dosenController = new DosenController();
$user_id = $_SESSION['user_id'];
$dosen_id = $dosenController->getDosenIdByUserId($user_id);

// Sekarang ambil daftar mata kuliah yang diajarkan oleh dosen
$mata_kuliah_list = $dosenController->getMataKuliahByDosen($dosen_id);

// Ambil jumlah mahasiswa per mata kuliah
$jumlahMahasiswaList = $dosenController->getJumlahMahasiswaByMataKuliah($dosen_id);
$jumlahMahasiswaMap = [];
foreach ($jumlahMahasiswaList as $item) {
  $jumlahMahasiswaMap[$item['mata_kuliah_id']] = $item['jumlah_mahasiswa'];
}
?>

<div class="bg-tertiary pt-40px pe-40px ps-40px" style="min-height: 100vh">
  <h2 class="ps-4px fw-semibold fs-5 mb-2 secondary-color">HALAMAN DASHBOARD</h2>
  <div class="row g-4 pt-2">
    <div class="col-12">
      <?php foreach ($mata_kuliah_list as $mata_kuliah): ?>
        <div class="shadow-sm bg-lightcustom rounded-4 d-flex align-items-center px-3 py-4 mb-4">
          <div class="bg-primary rounded-4 p-3 me-3"><i class="bi bi-bookmark-dash-fill text-white fs-4"></i></div>
          <div class="d-flex justify-content-between w-100 row mb-3  ms-xl-4">
            <div class="col-12 col-md-3 align-content-center">
              <p>Nama Mata Kuliah</p>
              <a href="/views/dosen/mahasiswa_management.php" class="text-dark">
                <?= htmlspecialchars($mata_kuliah['nama_mk']) ?>
              </a>
            </div>
            <div class="col-12 col-md-3 align-content-center">
              <p>Kode Mata Kuliah</p>
              <a href="/views/dosen/mahasiswa_management.php" class="text-dark">
                <?= htmlspecialchars($mata_kuliah['kode_mk']) ?>
              </a>
            </div>
            <div class="col-12 col-md-3 align-content-center">
              <p>Jumlah Mahasiswa</p>
              <a href="/views/dosen/mahasiswa_management.php" class="text-dark">
                <?= isset($jumlahMahasiswaMap[$mata_kuliah['id']]) ? $jumlahMahasiswaMap[$mata_kuliah['id']] : '0' ?>
              </a>
            </div>
            <div class="col-12 col-md-3 align-content-center">
              <button class="btn btn-content" style="width: 130px">QRCode</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require_once '../components/footer.php'; ?>