<?php
require_once 'LoginController.php';

class RegisterController
{
  private $pdo;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
  }

  public function register($username, $password, $role)
  {
    // Cek apakah username sudah ada di database
    $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      // Username sudah ada di database
      return false;
    }

    // Hash password sebelum disimpan
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $this->pdo->prepare('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $passwordHash);
    $stmt->bindParam(':role', $role);

    // Jika registrasi berhasil, lakukan login otomatis
    if ($stmt->execute()) {
      $loginController = new LoginController($this->pdo);
      return $loginController->loginAdmin($username, $password);
    }
    return false;
  }
}
