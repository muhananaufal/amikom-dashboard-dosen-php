<?php
require_once __DIR__ . '/../models/MataKuliah.php';
require_once __DIR__ . '/../config/db.php';

class MataKuliahController
{
  private $pdo;

  public function __construct()
  {
    $this->pdo = connectDatabase();
  }

  public function getAllMataKuliah()
  {
    $query = "SELECT * FROM mata_kuliah ORDER BY kode_mk";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Method untuk menambah mata kuliah baru
  public function addMataKuliah()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $namaMk = $_POST['nama_mk'];
      $nomorMk = $_POST['nomor_mk'];
      $sks = $_POST['sks'];

      // Generate kode mata kuliah otomatis
      $kodeMk = $this->generateKodeMataKuliah($namaMk, $nomorMk);

      if (strlen($kodeMk) > 10) {
        echo "Error: Kode mata kuliah terlalu panjang.";
        return;
      }

      $query = $this->pdo->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, nomor_mk, sks) VALUES (:kode_mk, :nama_mk, :nomor_mk, :sks)");
      $query->bindParam(':kode_mk', $kodeMk);
      $query->bindParam(':nama_mk', $namaMk);
      $query->bindParam(':nomor_mk', $nomorMk);
      $query->bindParam(':sks', $sks);

      if ($query->execute()) {
        header('Location: /views/admin/mata_kuliah_management.php?success=1');
      } else {
        echo "Error: Tidak dapat menambah mata kuliah.";
      }
    }
  }

  // Method untuk memperbarui mata kuliah
  public function updateMataKuliah()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = $_POST['id'];
      $namaMk = $_POST['nama_mk'];
      $nomorMk = $_POST['nomor_mk'];
      $sks = $_POST['sks'];

      // Generate kode mata kuliah otomatis
      $kodeMk = $this->generateKodeMataKuliah($namaMk, $nomorMk);

      if (strlen($kodeMk) > 10) {
        echo "Error: Kode mata kuliah terlalu panjang.";
        return;
      }

      $query = $this->pdo->prepare("UPDATE mata_kuliah SET kode_mk = :kode_mk, nama_mk = :nama_mk, nomor_mk = :nomor_mk, sks = :sks WHERE id = :id");
      $query->bindParam(':kode_mk', $kodeMk);
      $query->bindParam(':nama_mk', $namaMk);
      $query->bindParam(':nomor_mk', $nomorMk);
      $query->bindParam(':sks', $sks);
      $query->bindParam(':id', $id);

      if ($query->execute()) {
        header('Location: /views/admin/mata_kuliah_management.php?success=2');
      } else {
        echo "Error: Tidak dapat memperbarui mata kuliah.";
      }
    }
  }
  private function generateKodeMataKuliah($namaMk, $nomorMk)
  {
    $kata = explode(' ', $namaMk);
    $kode = '';
    foreach ($kata as $k) {
      $kode .= substr($k, 0, 3);
    }

    return strtolower(substr($kode, 0, 6)) . str_pad($nomorMk, 2, '0', STR_PAD_LEFT);
  }

  public function deleteMataKuliah()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
      $id = $_POST['id'];

      // Debugging untuk memastikan `id` diterima
      var_dump("ID received for deletion: ", $id);

      // Mulai transaksi
      $this->pdo->beginTransaction();

      try {
        // Hapus nilai mahasiswa yang berkaitan dengan mata kuliah
        $query = $this->pdo->prepare("DELETE FROM nilai WHERE mata_kuliah_id = :id");
        $query->bindParam(':id', $id);
        $query->execute();

        // Hapus data mata kuliah dari database
        $query = $this->pdo->prepare("DELETE FROM mata_kuliah WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();

        // Commit transaksi jika berhasil
        $this->pdo->commit();
        header('Location: /views/admin/mata_kuliah_management.php?success=1');
        exit;
      } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $this->pdo->rollBack();
        echo "Error: Tidak dapat menghapus mata kuliah.";
      }
    } else {
      echo "Invalid request method or missing ID.";
    }
  }
}

// Handle action berdasarkan parameter `action` pada URL
// Handle action berdasarkan parameter `action` pada URL
if (isset($_GET['action'])) {
  $controller = new MataKuliahController();

  if ($_GET['action'] === 'add') {
    $controller->addMataKuliah();
  } elseif ($_GET['action'] === 'update') {
    $controller->updateMataKuliah();
  } elseif ($_GET['action'] === 'delete') {  // Tambahkan ini
    $controller->deleteMataKuliah();
  }
}
