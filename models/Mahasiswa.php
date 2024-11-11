<?php
// models/Mahasiswa.php

class Mahasiswa
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  // Mendapatkan semua data mahasiswa
  public function getAllMahasiswa()
  {
    $stmt = $this->pdo->prepare("SELECT * FROM mahasiswa");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Mendapatkan data mahasiswa berdasarkan ID
  public function getMahasiswaById($id)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM mahasiswa WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Menambahkan data mahasiswa
  public function addMahasiswa($nim, $nama, $user_id)
  {
    $stmt = $this->pdo->prepare("INSERT INTO mahasiswa (nim, nama, user_id) VALUES (?, ?, ?)");
    return $stmt->execute([$nim, $nama, $user_id]);
  }

  // Mengedit data mahasiswa
  public function updateMahasiswa($id, $nim, $nama)
  {
    $stmt = $this->pdo->prepare("UPDATE mahasiswa SET nim = ?, nama = ? WHERE id = ?");
    return $stmt->execute([$nim, $nama, $id]);
  }

  // Menghapus data mahasiswa
  public function deleteMahasiswa($id)
  {
    $stmt = $this->pdo->prepare("DELETE FROM mahasiswa WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
