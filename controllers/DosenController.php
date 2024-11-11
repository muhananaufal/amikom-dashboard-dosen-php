<?php
require_once __DIR__ . '/../config/db.php'; // Corrected path to db.php


class DosenController
{
  private $pdo;

  public function __construct()
  {
    $this->pdo = connectDatabase();
  }

  public function getDosenIdByUserId($user_id)
  {
    $query = "SELECT id FROM dosen WHERE user_id = :user_id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn(); // Returns dosen_id
  }

  public function getDosenDetails($user_id)
  {
    $query = "
        SELECT u.username, d.nama, d.nidn
        FROM users u
        JOIN dosen d ON u.id = d.user_id
        WHERE u.id = :user_id
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  public function getAllDosen()
  {
    $query = "SELECT * FROM dosen ORDER BY nidn";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getMataKuliahByDosen($dosen_id)
  {
    $query = "
      SELECT mk.id, mk.kode_mk, mk.nama_mk, mk.sks
      FROM mata_kuliah AS mk
      JOIN dosen_mata_kuliah AS dmk ON mk.id = dmk.mata_kuliah_id
      WHERE dmk.dosen_id = :dosen_id
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':dosen_id', $dosen_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getAllDosenWithMataKuliah()
  {
    $query = $this->pdo->prepare("
          SELECT d.id, d.nama, d.nidn, GROUP_CONCAT(mk.kode_mk SEPARATOR ', ') AS kode_mata_kuliah, GROUP_CONCAT(mk.nama_mk SEPARATOR ', ') AS mata_kuliah 
          FROM dosen d
          LEFT JOIN dosen_mata_kuliah dm ON d.id = dm.dosen_id
          LEFT JOIN mata_kuliah mk ON dm.mata_kuliah_id = mk.id
          GROUP BY d.id
      ");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  // Mendapatkan daftar mata kuliah
  // Mendapatkan daftar dosen beserta mata kuliah yang diampu
  public function getDosenMataKuliah($dosenId)
  {
    $query = $this->pdo->prepare("
          SELECT mk.id
          FROM dosen_mata_kuliah dm
          JOIN mata_kuliah mk ON dm.mata_kuliah_id = mk.id
          WHERE dm.dosen_id = :dosen_id
      ");
    $query->bindParam(':dosen_id', $dosenId);
    $query->execute();
    return array_column($query->fetchAll(PDO::FETCH_ASSOC), 'id');
  }

  public function getMataKuliah()
  {
    $query = $this->pdo->prepare("SELECT * FROM mata_kuliah");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getJumlahMahasiswaByMataKuliah($dosen_id)
  {
    $sql = "SELECT mata_kuliah_id, COUNT(DISTINCT mahasiswa_id) AS jumlah_mahasiswa
              FROM nilai
              WHERE dosen_id = :dosen_id
              GROUP BY mata_kuliah_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['dosen_id' => $dosen_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Mengembalikan jumlah mahasiswa per mata kuliah
  }

  public function getProfileDetailInformations($dosen_id)
  {
    $query = "SELECT COUNT(DISTINCT mata_kuliah_id) AS jumlah_mata_kuliah, COUNT(DISTINCT mahasiswa_id) AS jumlah_mahasiswa
              FROM nilai
              WHERE dosen_id = :dosen_id";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':dosen_id', $dosen_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }



  // Menambahkan dosen
  public function addDosen()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $nama = $_POST['nama'];
      $nidn = $_POST['nidn'];
      $mataKuliahIds = $_POST['mata_kuliah_ids'];

      $username = strtolower(str_replace(' ', '_', $nama));
      $password = password_hash('defaultpassword', PASSWORD_DEFAULT);

      try {
        // Mulai transaksi
        $this->pdo->beginTransaction();

        // Tambahkan pengguna ke tabel users
        $userQuery = $this->pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'dosen')");
        $userQuery->bindParam(':username', $username);
        $userQuery->bindParam(':password', $password);
        $userQuery->execute();
        $userId = $this->pdo->lastInsertId();

        // Masukkan data dosen ke tabel dosen
        $query = $this->pdo->prepare("INSERT INTO dosen (user_id, nidn, nama) VALUES (:user_id, :nidn, :nama)");
        $query->bindParam(':user_id', $userId);
        $query->bindParam(':nidn', $nidn);
        $query->bindParam(':nama', $nama);
        $query->execute();
        $dosenId = $this->pdo->lastInsertId();

        // Cek nilai variabel sebelum digunakan
        if ($dosenId && $mataKuliahIds) {
          // Masukkan relasi dosen dan mata kuliah
          foreach ($mataKuliahIds as $mataKuliahId) {
            $queryDosenMk = $this->pdo->prepare("INSERT INTO dosen_mata_kuliah (dosen_id, mata_kuliah_id) VALUES (:dosen_id, :mata_kuliah_id)");
            $queryDosenMk->bindParam(':dosen_id', $dosenId);
            $queryDosenMk->bindParam(':mata_kuliah_id', $mataKuliahId);
            $queryDosenMk->execute();
          }
        } else {
          throw new Exception("Error: ID dosen atau ID mata kuliah tidak ditemukan.");
        }

        // Commit transaksi
        $this->pdo->commit();

        // Redirect jika sukses
        header('Location: /views/admin/dosen_management.php?success=1');
        exit;
      } catch (Exception $e) {
        // Rollback jika ada error
        $this->pdo->rollBack();
        echo "Failed: " . $e->getMessage();
      }
    }
  }




  // Mengedit dosen
  public function editDosen()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'];
      $namaDosen = $_POST['nama'];
      $nidn = $_POST['nidn'];
      $mataKuliahIds = $_POST['mata_kuliah_ids'];

      // Update tabel dosen
      $query = $this->pdo->prepare("UPDATE dosen SET nama = :nama, nidn = :nidn WHERE id = :id");
      $query->bindParam(':nama', $namaDosen);
      $query->bindParam(':nidn', $nidn);
      $query->bindParam(':id', $id);
      $query->execute();

      // Hapus mata kuliah yang tidak lagi diampu
      $this->pdo->prepare("DELETE FROM dosen_mata_kuliah WHERE dosen_id = :dosen_id")->execute([':dosen_id' => $id]);

      // Tambahkan mata kuliah baru yang dipilih
      foreach ($mataKuliahIds as $mataKuliahId) {
        $query = $this->pdo->prepare("INSERT INTO dosen_mata_kuliah (dosen_id, mata_kuliah_id) VALUES (:dosen_id, :mata_kuliah_id)");
        $query->bindParam(':dosen_id', $id);
        $query->bindParam(':mata_kuliah_id', $mataKuliahId);
        $query->execute();
      }

      header('Location: /views/admin/dosen_management.php?success=1');
    }
  }

  // Menghapus dosen
  public function deleteDosen()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
      $id = $_POST['id'];

      // Debugging untuk memastikan `id` diterima
      var_dump("ID received for deletion: ", $id);

      // Mulai transaksi

      try {
        // Mulai transaksi
        $this->pdo->beginTransaction();

        // Langkah 1: Hapus data terkait di tabel nilai
        $query = $this->pdo->prepare("DELETE FROM nilai WHERE dosen_id = :id");
        $query->bindParam(':id', $id);
        $query->execute();

        // Langkah 2: Hapus relasi dosen dengan mata kuliah dari tabel dosen_mata_kuliah
        $query = $this->pdo->prepare("DELETE FROM dosen_mata_kuliah WHERE dosen_id = :id");
        $query->bindParam(':id', $id);
        $query->execute();

        // Langkah 3: Ambil user_id dari tabel dosen berdasarkan ID dosen yang ingin dihapus
        $getUserIdQuery = $this->pdo->prepare("SELECT user_id FROM dosen WHERE id = :id");
        $getUserIdQuery->bindParam(':id', $id); // Menggunakan $id sebagai ID dosen
        $getUserIdQuery->execute();
        $userId = $getUserIdQuery->fetchColumn();

        if ($userId) {
          // Langkah 4: Hapus data dosen dari tabel dosen
          $deleteDosenQuery = $this->pdo->prepare("DELETE FROM dosen WHERE id = :id");
          $deleteDosenQuery->bindParam(':id', $id);
          $deleteDosenQuery->execute();

          // Langkah 5: Hapus data pengguna dari tabel users berdasarkan user_id yang ditemukan
          $deleteUserQuery = $this->pdo->prepare("DELETE FROM users WHERE id = :user_id");
          $deleteUserQuery->bindParam(':user_id', $userId);
          $deleteUserQuery->execute();
        }

        // Commit transaksi jika berhasil
        $this->pdo->commit();
        header('Location: /views/admin/dosen_management.php?success=1');
        exit;
      } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $this->pdo->rollBack();
        echo "Error: Tidak dapat menghapus dosen." . $e->getMessage();
      }
    } else {
      echo "Invalid request method or missing ID.";
    }
  }

  public function getCourses()
  {
    $dosenId = $_SESSION['user_id'];

    $sql = "SELECT mk.id, mk.kode_mk, mk.nama_mk 
              FROM mata_kuliah mk
              JOIN dosen_mata_kuliah dmk ON mk.id = dmk.mata_kuliah_id
              WHERE dmk.dosen_id = :dosenId";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':dosenId', $dosenId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Get students enrolled in a specific course
  public function getStudentsByCourse($courseId)
  {
    $dosenId = $_SESSION['user_id'];

    $sql = "SELECT m.id AS mahasiswa_id, m.nim, m.nama, n.kehadiran, n.tugas, n.kuis, 
              n.responsi, n.uts, n.uas, n.total_nilai, n.keterangan 
              FROM mahasiswa m
              JOIN mahasiswa_mata_kuliah mmk ON m.id = mmk.mahasiswa_id
              JOIN nilai n ON m.id = n.mahasiswa_id AND n.mata_kuliah_id = mmk.mata_kuliah_id
              WHERE mmk.mata_kuliah_id = :courseId AND n.dosen_id = :dosenId";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
    $stmt->bindParam(':dosenId', $dosenId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  // Update a student's grades


  public function editNilai($dosenId, $studentId, $courseId, $grades)
  {
    try {
      $sql = "UPDATE nilai 
                  SET kehadiran = :kehadiran, tugas = :tugas, kuis = :kuis, 
                      responsi = :responsi, uts = :uts, uas = :uas
                  WHERE mahasiswa_id = :studentId AND mata_kuliah_id = :courseId AND dosen_id = :dosenId";

      $stmt = $this->pdo->prepare($sql);

      // Bind parameters
      $stmt->bindParam(':kehadiran', $grades['kehadiran']);
      $stmt->bindParam(':tugas', $grades['tugas']);
      $stmt->bindParam(':kuis', $grades['kuis']);
      $stmt->bindParam(':responsi', $grades['responsi']);
      $stmt->bindParam(':uts', $grades['uts']);
      $stmt->bindParam(':uas', $grades['uas']);
      $stmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
      $stmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
      $stmt->bindParam(':dosenId', $dosenId, PDO::PARAM_INT);

      // Execute the statement
      $success = $stmt->execute();

      // If the update was successful, redirect
      if ($success) {
        header('Location: /views/dosen/mahasiswa_management.php');
        exit; // Always call exit after header redirection
      } else {
        // Handle failure
        throw new Exception('Failed to update the grades.');
      }
    } catch (Exception $e) {
      // Handle errors (e.g., log the error, display a message)
      echo 'Error: ' . $e->getMessage();
    }
  }

  // Delete or reset a student's grades
  public function hapusNilai($dosenId, $studentId, $courseId)
  {
    try {
      $sql = "UPDATE nilai 
                  SET kehadiran = 0.0, tugas = 0.0, kuis = 0.0, responsi = 0.0, uts = 0.0, uas = 0.0
                  WHERE mahasiswa_id = :studentId AND mata_kuliah_id = :courseId AND dosen_id = :dosenId";

      $stmt = $this->pdo->prepare($sql);

      // Bind parameters
      $stmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
      $stmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
      $stmt->bindParam(':dosenId', $dosenId, PDO::PARAM_INT);

      // Execute the statement
      $success = $stmt->execute();

      // If the update was successful, redirect
      if ($success) {
        header('Location: /views/dosen/mahasiswa_management.php');
        exit; // Always call exit after header redirection
      } else {
        // Handle failure
        throw new Exception('Failed to update the grades.');
      }
    } catch (Exception $e) {
      // Handle errors (e.g., log the error, display a message)
      echo 'Error: ' . $e->getMessage();
    }
  }
}

// Menentukan tindakan berdasarkan parameter `action` pada URL
if (isset($_GET['action'])) {
  $controller = new DosenController();

  if ($_GET['action'] === 'add') {
    $controller->addDosen();
  } elseif ($_GET['action'] === 'edit') {
    $controller->editDosen();
  } elseif ($_GET['action'] === 'delete') {
    $controller->deleteDosen();
  } elseif ($_GET['action'] === 'editNilai') {
    // Memeriksa apakah semua data dari form tersedia
    if (!isset($_POST['student_id'], $_POST['course_id'], $_POST['id'])) {
      throw new Exception("ID Mahasiswa atau ID Mata Kuliah tidak ditemukan dalam permintaan.");
    }


    // Mengambil data yang diperlukan dari $_POST
    $dosenId = $_POST['id'];
    $studentId = $_POST['student_id'];
    $courseId = $_POST['course_id'];
    $grades = [
      'kehadiran' => $_POST['kehadiran'] ?? 0,
      'tugas' => $_POST['tugas'] ?? 0,
      'kuis' => $_POST['kuis'] ?? 0,
      'responsi' => $_POST['responsi'] ?? 0,
      'uts' => $_POST['uts'] ?? 0,
      'uas' => $_POST['uas'] ?? 0
    ];

    // Memperbarui nilai
    $controller->editNilai($dosenId, $studentId, $courseId, $grades);
  } elseif ($_GET['action'] === 'hapusNilai') {
    // Memeriksa apakah semua data dari form tersedia
    if (!isset($_POST['student_id'], $_POST['course_id'], $_POST['id'])) {
      throw new Exception("ID Mahasiswa atau ID Mata Kuliah tidak ditemukan dalam permintaan.");
    }


    // Mengambil data yang diperlukan dari $_POST
    $dosenId = $_POST['id'];
    $studentId = $_POST['student_id'];
    $courseId = $_POST['course_id'];

    // Memperbarui nilai
    $controller->hapusNilai($dosenId, $studentId, $courseId);
  }
}
