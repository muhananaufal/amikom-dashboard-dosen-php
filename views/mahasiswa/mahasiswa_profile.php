<?php
session_start();
if ($_SESSION['role'] !== 'mahasiswa') {
  header('Location: /views/auth/login.php');
  exit;
}

$isActive = 'profile';
$title = 'Mahasiswa Profile';

require_once '../components/header.php';
require_once '../../controllers/MahasiswaController.php';

$pdo = connectDatabase();
$mahasiswaController = new MahasiswaController($pdo);
$user_id = $_SESSION['user_id'];
// Fetch Dosen Details
$mahasiswaDetails = $mahasiswaController->getMahasiswaByUserId($user_id);


$mahasiswa_id = $mahasiswaController->getMahasiswaIdByUserId($user_id);

$infoTambahan = $mahasiswaController->getJumlahKelasDanRataRataNilai($mahasiswa_id);

?>

<div class="bg-tertiary pt-30px pe-40px ps-40px" style="min-height: 100vh">
  <div class="row g-4 pt-2">
    <div class="col-12">
      <div class="d-flex align-items-center py-2">
        <div class="p-3 my-2">
          <div class="row bg-lightcustom rounded-4 shadow-sm" style="padding-top: 25px; padding-bottom: 25px; padding-left: 15px">
            <!-- Kolom Kiri (Gambar Profil dan Tombol Pilih Foto) -->
            <div class="col-md-4">
              <div class="card p-4" style="border-radius: 10px;">
                <img src="/assets/images/p1.jpg" alt="Profile Picture" class="img-fluid mb-3">
                <button class="btn btn-submit-outline-no-shadow btn-ms">Pilih Foto</button>
                <p class="text-muted mt-2">Besar file: maksimum 10.000.000 bytes (10 Megabytes). Ekstensi file yang diperbolehkan: JPG, JPEG, PNG</p>
              </div>
            </div>

            <!-- Kolom Kanan (Data Diri dan Kontak) -->
            <div class="col-md-8 p-3 ">
              <h1 class="fw-semibold fs-4 mb-2 secondary-color fw-bold">Your Role: <?php echo ucfirst($_SESSION['role']); ?></h1>
              <div>
                <hr style="width: 100%; max-width: 250px;">
              </div>
              <div>

                <h2 class="fw-semibold fs-5 mb-2 secondary-color pt-3">Informasi Akun</h2>
                <div class="my-3 pt-1">
                  <strong>Username</strong>: <?php echo htmlspecialchars($mahasiswaDetails['username']); ?>
                </div>
              </div>
              <div>

                <h2 class="fw-semibold fs-5 mb-2 secondary-color pt-3">Data Dosen</h2>
                <div class="my-3 pt-1">

                  <strong>Nama</strong>: <?php echo htmlspecialchars($mahasiswaDetails['nama']); ?>
                </div>
                <div class="my-3 pt-1">

                  <strong> NIM</strong>: <?php echo htmlspecialchars($mahasiswaDetails['nim']); ?>
                </div>
              </div>
              <div>
                <h2 class="fw-semibold fs-5 my-3 secondary-color pt-4">Informasi Tambahan</h2>
                <div class="my-3 pt-1">
                  <strong>Jumlah Kelas yang Diambil</strong>:
                  <?php
                  echo $infoTambahan['jumlah_kelas'] > 0 ? $infoTambahan['jumlah_kelas'] : 'Tidak ada kelas';
                  ?>
                </div>
                <div class="my-3 pt-1">
                  <strong>IPK</strong>: <?php
                                        echo isset($infoTambahan['rata_rata_nilai']) && $infoTambahan['rata_rata_nilai'] !== null
                                          ? number_format($infoTambahan['rata_rata_nilai'], 2)
                                          : 'Tidak ada nilai';
                                        ?>
                </div>

              </div>
              <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#editProfileModal" disabled>Edit Profile</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<?php require_once '../components/footer.php'; ?>