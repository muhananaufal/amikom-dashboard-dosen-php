<?php
// controllers/AdminController.php

require_once __DIR__ . '/../models/Admin.php'; // Corrected path to Admin.php
require_once __DIR__ . '/../config/db.php'; // Corrected path to db.php

class AdminController
{
  private $pdo;
  private $admin;

  public function __construct($pdo)
  {
    $this->pdo = $pdo;
    $this->admin = new Admin($pdo); // Instance of Admin model
  }

  public function getUserAdminDetails($user_id)
  {
    $query = "
        SELECT *
        FROM users u
        WHERE u.id = :user_id
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  public function updateProfile()
  {
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
      $username = $_POST['username'];
      $currentPassword = $_POST['currentPassword'];
      $newPassword = $_POST['newPassword'];
      $confirmPassword = $_POST['confirmPassword'];

      // Validasi password saat ini
      if (!$this->admin->verifyPassword($_SESSION['username'], $currentPassword)) {
        echo "Error: Current password is incorrect.";
        return;
      }

      // Validasi konfirmasi password baru
      if ($newPassword && $newPassword !== $confirmPassword) {
        echo "Error: New passwords do not match.";
        return;
      }

      // Update profile admin
      if ($this->admin->updateProfile($_SESSION['username'], $username, $newPassword)) {
        $_SESSION['username'] = $username; // Update username in session
        header('Location: /views/admin/admin_profile.php?success=1');
        exit;
      } else {
        echo "Error: Failed to update profile.";
      }
    }
  }
}

// Check if action `updateProfile` is called
if (isset($_GET['action'])) {
  // Initialize PDO connection
  $pdo = connectDatabase();

  // Pass $pdo to the AdminController instance
  $controller = new AdminController($pdo);

  if ($_GET['action'] === 'updateProfile') {
    $controller->updateProfile();
  }
}
