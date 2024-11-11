<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  header('Location: /views/auth/login.php');
  exit;
}
$isActive = 'profile';
$title = 'Admin Profile';

require_once '../components/header.php';
require_once '../../controllers/AdminController.php';

$pdo = connectDatabase();

$adminController = new AdminController($pdo);
$user_id = $_SESSION['user_id'];
// Fetch Dosen Details
$infoTambahan = $adminController->getUserAdminDetails($user_id);


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
                  <strong>Username</strong>: <?php echo $_SESSION['username']; ?>
                </div>
              </div>
              <div>

                <h2 class="fw-semibold fs-5 my-3 secondary-color pt-4">Informasi Tambahan</h2>
                <div class="my-3 pt-1 ">
                  <strong>Tanggal Akun Dibuat</strong>:
                  <?php
                  $date = $infoTambahan['created_at'];
                  $formattedDate = date('l, j F Y H:i', strtotime($date));
                  echo $formattedDate;

                  ?>
                </div>
              </div>
              <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="post" action="../../controllers/AdminController.php?action=updateProfile">
          <div class="mb-3">
            <label for="username" class="form-label">New Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
          </div>
          <div class="mb-3">
            <label for="currentPassword" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="newPassword">
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Profile</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once '../components/footer.php'; ?>