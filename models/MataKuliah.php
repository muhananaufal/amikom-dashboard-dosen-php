<?php
// models/MataKuliah.php

class MataKuliah
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  // Mendapatkan semua data mata kuliah
  public function getAllMataKuliah()
  {
    $stmt = $this->pdo->prepare("SELECT * FROM mata_kuliah");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Mendapatkan data mata kuliah berdasarkan ID
  public function getMataKuliahById($id)
  {
    $stmt = $this->pdo->prepare("SELECT * FROM mata_kuliah WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Menambahkan data mata kuliah
  public function addMataKuliah($kode_mk, $nama_mk, $sks)
  {
    $stmt = $this->pdo->prepare("INSERT INTO mata_kuliah (kode_mk, nama_mk, sks) VALUES (?, ?, ?)");
    return $stmt->execute([$kode_mk, $nama_mk, $sks]);
  }

  // Mengedit data mata kuliah
  public function updateMataKuliah($id, $kode_mk, $nama_mk, $sks)
  {
    $stmt = $this->pdo->prepare("UPDATE mata_kuliah SET kode_mk = ?, nama_mk = ?, sks = ? WHERE id = ?");
    return $stmt->execute([$kode_mk, $nama_mk, $sks, $id]);
  }

  // Menghapus data mata kuliah
  public function deleteMataKuliah($id)
  {
    $stmt = $this->pdo->prepare("DELETE FROM mata_kuliah WHERE id = ?");
    return $stmt->execute([$id]);
  }
}
