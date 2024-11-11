<?php
// controllers/MahasiswaController.php

require_once __DIR__ . '/../models/MataKuliah.php';
require_once __DIR__ . '/../models/Dosen.php';
require_once __DIR__ . '/../models/Mahasiswa.php'; // Corrected path to Mahasiswa.php
require_once __DIR__ . '/../config/db.php'; // Corrected path to db.php


class MahasiswaController
{
  private $pdo;
  private $mahasiswa;
  private $mataKuliah;
  private $dosen;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
    $this->mahasiswa = new Mahasiswa($pdo);
    $this->mataKuliah = new MataKuliah($pdo);
    $this->dosen = new Dosen($pdo);
  }

  public function getMahasiswaByUserId($user_id)
  {
    $query = "
        SELECT u.username, m.nama, m.nim
        FROM users u
        JOIN mahasiswa m ON u.id = m.user_id
        WHERE u.id = :user_id
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getAllMataKuliahWithDosen()
  {
    $courses = $this->mataKuliah->getAllMataKuliah();
    foreach ($courses as &$course) {
      $course['dosen'] = $this->dosen->getDosenByMataKuliahId($course['id']);
    }
    return $courses;
  }


  public function getAllMahasiswaWithDetails()
  {
    $query = "
    SELECT 
        m.id,
        m.nim, 
        m.nama AS nama_mahasiswa, 
        GROUP_CONCAT(
            DISTINCT CONCAT(mk.kode_mk, ' - ', d.nama) 
            ORDER BY mk.kode_mk, d.nama SEPARATOR ', '
        ) AS mata_kuliah_dosen
    FROM mahasiswa m
    LEFT JOIN nilai n ON m.id = n.mahasiswa_id
    LEFT JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
    LEFT JOIN dosen d ON n.dosen_id = d.id
    GROUP BY m.id, m.nim, m.nama
    ORDER BY m.nim;
";;

    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getMahasiswaDetails($id)
  {
    // Fetch basic details of the mahasiswa
    $stmt = $this->pdo->prepare("
          SELECT m.id, m.nim, m.nama 
          FROM mahasiswa m
          WHERE m.id = ?
      ");
    $stmt->execute([$id]);
    $mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($mahasiswa) {
      // Query for mata kuliah and dosen details for this mahasiswa
      $stmtCourses = $this->pdo->prepare("
        SELECT mk.id AS mata_kuliah_id, mk.kode_mk, d.id AS dosen_id, d.nama AS dosen_name
        FROM mahasiswa_mata_kuliah mmk
        JOIN mata_kuliah mk ON mmk.mata_kuliah_id = mk.id
        JOIN dosen_mata_kuliah dm ON mk.id = dm.mata_kuliah_id
        JOIN dosen d ON dm.dosen_id = d.id
        WHERE mmk.mahasiswa_id = ?
      ");
      $stmtCourses->execute([$id]);
      $mahasiswa['mata_kuliah_dosen'] = $stmtCourses->fetchAll(PDO::FETCH_ASSOC);

      // Return the mahasiswa details if found
      return $mahasiswa;
    }

    // Return null if no mahasiswa is found
    return null;
  }


  public function getDosenByMataKuliah($mataKuliahId)
  {
    $stmt = $this->pdo->prepare("
          SELECT d.id, d.nama 
          FROM dosen d
          JOIN dosen_mata_kuliah dm ON d.id = dm.dosen_id
          WHERE dm.mata_kuliah_id = ?
      ");
    $stmt->execute([$mataKuliahId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getMahasiswaByMataKuliahDosen($mataKuliahId, $dosenId)
  {
    $query = "SELECT * FROM mahasiswa WHERE mata_kuliah_id = :mata_kuliah_id AND dosen_id = :dosen_id";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute(['mata_kuliah_id' => $mataKuliahId, 'dosen_id' => $dosenId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getMahasiswaTanpaMataKuliah()
  {
    $query = "
              SELECT mahasiswa.id, mahasiswa.nim, mahasiswa.nama, mahasiswa.nama as nama_mahasiswa
              FROM mahasiswa 
              LEFT JOIN mahasiswa_mata_kuliah 
              ON mahasiswa.id = mahasiswa_mata_kuliah.mahasiswa_id 
              WHERE mahasiswa_mata_kuliah.mahasiswa_id IS NULL;
          ";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
  }


  public function filterTable($mataKuliahId, $dosenId)
  {
    $query = "
      SELECT 
        m.id,
        m.nim, 
        m.nama AS nama_mahasiswa, 
        IFNULL(n.kehadiran, 0) AS kehadiran, 
        IFNULL(n.tugas, 0) AS tugas, 
        IFNULL(n.kuis, 0) AS kuis, 
        IFNULL(n.responsi, 0) AS responsi, 
        IFNULL(n.uts, 0) AS uts, 
        IFNULL(n.uas, 0) AS uas, 
        IFNULL(n.keterangan, '-') AS keterangan
      FROM mahasiswa m
      LEFT JOIN nilai n ON m.id = n.mahasiswa_id
      WHERE n.mata_kuliah_id = :mataKuliahId AND n.dosen_id = :dosenId
      ORDER BY m.nim
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':mataKuliahId', $mataKuliahId, PDO::PARAM_INT);
    $stmt->bindParam(':dosenId', $dosenId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
  }

  // Mendapatkan ID mahasiswa berdasarkan user_id
  public function getMahasiswaIdByUserId($user_id)
  {
    $query = "SELECT id FROM mahasiswa WHERE user_id = :user_id";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
  }

  // Mendapatkan daftar mata kuliah dan dosen untuk mahasiswa tertentu
  public function getMataKuliahAndDosenByMahasiswa($mahasiswa_id)
  {
    $query = "
        SELECT mk.nama_mk, mk.kode_mk, d.nama AS nama_dosen
        FROM nilai n
        JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
        JOIN dosen d ON n.dosen_id = d.id
        WHERE n.mahasiswa_id = :mahasiswa_id
    ";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':mahasiswa_id', $mahasiswa_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public function getJumlahKelasDanRataRataNilai($mahasiswa_id)
  {
    // Query untuk menghitung jumlah kelas dan rata-rata semua nilai
    $query = "
    SELECT 
    COUNT(mata_kuliah_id) AS jumlah_kelas, 
    AVG(total_nilai) AS rata_rata_nilai
    FROM nilai n
    JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
    JOIN dosen d ON n.dosen_id = d.id
    WHERE n.mahasiswa_id = :mahasiswa_id;

";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':mahasiswa_id', $mahasiswa_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  public function getNilaiByMahasiswaId($mahasiswa_id)
  {
    $sql = "SELECT nilai.*, mata_kuliah.nama_mk, mata_kuliah.kode_mk, dosen.nama AS nama_dosen
            FROM nilai
            JOIN mata_kuliah ON nilai.mata_kuliah_id = mata_kuliah.id
            JOIN dosen ON nilai.dosen_id = dosen.id
            WHERE nilai.mahasiswa_id = :mahasiswa_id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['mahasiswa_id' => $mahasiswa_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function addMahasiswa()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $nama = $_POST['nama'];
      $nim = $_POST['nim'];
      $mata_kuliah_ids = $_POST['mata_kuliah'];
      $dosen_ids = $_POST['dosen'];

      $username = strtolower(str_replace(' ', '_', $nama));
      $password = password_hash('defaultpassword', PASSWORD_DEFAULT);
      try {
        $pdo = connectDatabase();
        $pdo->beginTransaction();

        // Insert ke tabel users
        $userQuery = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'mahasiswa')");
        $userQuery->execute(['username' => $username, 'password' => $password]);

        // Dapatkan user_id yang baru ditambahkan
        $user_id = $pdo->lastInsertId();

        // Insert ke tabel mahasiswa
        $mahasiswaQuery = $pdo->prepare("INSERT INTO mahasiswa (user_id, nim, nama) VALUES (:user_id, :nim, :nama)");
        $mahasiswaQuery->execute(['user_id' => $user_id, 'nim' => $nim, 'nama' => $nama]);

        $mahasiswa_id = $pdo->lastInsertId();

        if ($mata_kuliah_ids !== null && $mata_kuliah_ids !== '' && $mata_kuliah_ids !== [] && $dosen_ids !== null && $dosen_ids !== '' && $dosen_ids !== []) {
          // Insert ke tabel mahasiswa_mata_kuliah untuk menyimpan hubungan mahasiswa-mata kuliah
          foreach ($mata_kuliah_ids as $mata_kuliah_id) {
            $mahasiswaMataKuliahQuery = $pdo->prepare("INSERT INTO mahasiswa_mata_kuliah (mahasiswa_id, mata_kuliah_id) VALUES (?, ?)");
            $mahasiswaMataKuliahQuery->execute([$mahasiswa_id, $mata_kuliah_id]);
          }


          // Insert into nilai table to link the selections
          foreach ($mata_kuliah_ids as $mata_kuliah_id) {
            $dosen_id = $dosen_ids[$mata_kuliah_id] ?? null; // Ambil dosen untuk mata kuliah tertentu
            if ($dosen_id) {
              $nilaiQuery = $pdo->prepare("INSERT INTO nilai (mahasiswa_id, mata_kuliah_id, dosen_id) VALUES (?, ?, ?)");
              $nilaiQuery->execute([$mahasiswa_id, $mata_kuliah_id, $dosen_id]);
            }
          }
        }



        // Commit transaksi
        $pdo->commit();

        // Redirect atau tampilkan pesan sukses
        header('Location: /views/admin/mahasiswa_management.php');
        exit();
      } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
      }
    }
  }

  public function updateMahasiswa($id, $nim, $nama, $mata_kuliah_ids, $dosen_ids)
  {
    try {
      $this->pdo->beginTransaction();

      // Update data mahasiswa di tabel mahasiswa
      $stmt = $this->pdo->prepare("UPDATE mahasiswa SET nim = :nim, nama = :nama WHERE id = :id");
      $stmt->bindParam(':id', $id);
      $stmt->bindParam(':nim', $nim);
      $stmt->bindParam(':nama', $nama);
      $stmt->execute();

      // Hapus relasi lama di tabel mahasiswa_mata_kuliah
      $deleteMataKuliahQuery = $this->pdo->prepare("DELETE FROM mahasiswa_mata_kuliah WHERE mahasiswa_id = :id");
      $deleteMataKuliahQuery->execute(['id' => $id]);

      // Hapus entri lama di tabel nilai untuk mahasiswa
      $deleteNilaiQuery = $this->pdo->prepare("DELETE FROM nilai WHERE mahasiswa_id = :id");
      $deleteNilaiQuery->execute(['id' => $id]);

      // Tambahkan relasi baru di tabel mahasiswa_mata_kuliah dan nilai
      if ($mata_kuliah_ids !== null && $mata_kuliah_ids !== '' && $mata_kuliah_ids !== [] && $dosen_ids !== null && $dosen_ids !== '' && $dosen_ids !== []) {

        foreach ($mata_kuliah_ids as $mata_kuliah_id) {
          // Insert ke mahasiswa_mata_kuliah
          $mahasiswaMataKuliahQuery = $this->pdo->prepare("INSERT INTO mahasiswa_mata_kuliah (mahasiswa_id, mata_kuliah_id) VALUES (?, ?)");
          $mahasiswaMataKuliahQuery->execute([$id, $mata_kuliah_id]);

          // Ambil dosen_id terkait dari dosen_ids
          $dosen_id = $dosen_ids[$mata_kuliah_id] ?? null;
          if ($dosen_id) {
            // Insert ke tabel nilai
            $nilaiQuery = $this->pdo->prepare("INSERT INTO nilai (mahasiswa_id, mata_kuliah_id, dosen_id) VALUES (?, ?, ?)");
            $nilaiQuery->execute([$id, $mata_kuliah_id, $dosen_id]);
          }
        }
      }

      // Commit transaksi
      $this->pdo->commit();

      return true; // Jika berhasil
    } catch (Exception $e) {
      $this->pdo->rollBack();
      echo "Error: " . $e->getMessage(); // Menampilkan pesan error
      exit(); // Menghentikan eksekusi untuk debugging
    }
  }


  // Method to delete a mahasiswa record
  public function deleteMahasiswa($id)
  {
    var_dump("ID received for deletion: ", $id);
    try {
      // Mulai transaksi
      $this->pdo->beginTransaction();

      // Langkah 1: Ambil user_id dari tabel mahasiswa berdasarkan ID mahasiswa
      $getUserIdQuery = $this->pdo->prepare("SELECT user_id FROM mahasiswa WHERE id = :id");
      $getUserIdQuery->bindParam(':id', $id);
      $getUserIdQuery->execute();
      $userId = $getUserIdQuery->fetchColumn();



      // Jika user_id ditemukan, lanjutkan penghapusan
      if ($userId) {
        // Hapus data mahasiswa dari tabel mahasiswa (nilai dan relasi akan terhapus otomatis karena ON DELETE CASCADE)
        $deleteMahasiswaQuery = $this->pdo->prepare("DELETE FROM mahasiswa WHERE id = :id");
        $deleteMahasiswaQuery->bindParam(':id', $id);
        $deleteMahasiswaQuery->execute();

        // Hapus data pengguna dari tabel users berdasarkan user_id
        $deleteUserQuery = $this->pdo->prepare("DELETE FROM users WHERE id = :user_id");
        $deleteUserQuery->bindParam(':user_id', $userId);
        $deleteUserQuery->execute();

        // Commit transaksi jika berhasil
        $this->pdo->commit();

        echo "Data mahasiswa dan pengguna terkait berhasil dihapus.";
        header('Location: /views/admin/mahasiswa_management.php?success=1');
        exit;
      } else {
        echo "Data mahasiswa tidak ditemukan.";
      }
    } catch (Exception $e) {
      // Rollback transaksi jika terjadi kesalahan
      $this->pdo->rollBack();
      echo "Error: Tidak dapat menghapus mahasiswa. " . $e->getMessage();
    }
  }
}
// Instantiate the controller
$pdo = connectDatabase();
$mahasiswaController = new MahasiswaController($pdo);

// Define actions and add error handling
try {
  if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    // Menambahkan mahasiswa (fungsi tambahan jika diperlukan)
    if ($action === 'add') {
      MahasiswaController::addMahasiswa();
    }

    if ($action === 'getAllMahasiswaWithDetails') {
      echo json_encode($mahasiswaController->getAllMahasiswaWithDetails());
    }

    // Add new action for getting specific mahasiswa details
    if ($action === 'getMahasiswaDetails' && isset($_GET['id'])) {
      $id = $_GET['id'];
      echo json_encode($mahasiswaController->getMahasiswaDetails($id));
    }

    // Mendapatkan dosen berdasarkan mata kuliah yang dipilih
    if ($action === 'getDosenByMataKuliah' && isset($_GET['mataKuliahId'])) {
      $mataKuliahId = $_GET['mataKuliahId'];
      echo json_encode($mahasiswaController->getDosenByMataKuliah($mataKuliahId));
    }

    if ($action === 'getMahasiswaTanpaMataKuliah') {
      echo json_encode($mahasiswaController->getMahasiswaTanpaMataKuliah());
    }

    // Filter tabel mahasiswa berdasarkan dosen dan mata kuliah yang dipilih
    if ($action === 'filterTable' && isset($_GET['mataKuliahId']) && isset($_GET['dosenId'])) {
      $mataKuliahId = (int)$_GET['mataKuliahId'];
      $dosenId = (int)$_GET['dosenId'];
      $mahasiswaController->filterTable($mataKuliahId, $dosenId);
    }

    if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'];
      $nim = $_POST['nim'];
      $nama = $_POST['nama'];
      $mata_kuliah_ids = $_POST['mata_kuliah']; // Pastikan form mengirimkan array id mata kuliah
      $dosen_ids = $_POST['dosen']; // Pastikan form mengirimkan array dosen terkait mata kuliah

      if ($mahasiswaController->updateMahasiswa($id, $nim, $nama, $mata_kuliah_ids, $dosen_ids)) {
        header('Location: /views/admin/mahasiswa_management.php?status=updated');
      } else {
        header('Location: /views/admin/mahasiswa_management.php?status=error');
      }
      exit;
    }


    if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;  // konversi ke integer
      if ($id && $mahasiswaController->deleteMahasiswa($id)) {
        header('Location: /views/admin/mahasiswa_management.php?status=deleted');
      } else {
        header('Location: /views/admin/mahasiswa_management.php?status=error');
      }
      exit;
    }
    exit;
  }
} catch (Exception $e) {
  error_log($e->getMessage());
  // Return the error as JSON to prevent HTML output
  header('Content-Type: application/json');
  echo json_encode(["error" => $e->getMessage()]);
  exit;
}
