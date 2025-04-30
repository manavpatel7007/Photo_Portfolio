<?php
require_once 'db.php';
require_once 'session.php';
checkLogin();

if (isset($_GET['id'])) {
    // First get the photo details to delete the file
    $stmt = $pdo->prepare("SELECT image_path FROM photos WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $photo = $stmt->fetch();
    
    if ($photo) {
        // Delete the file
        $filePath = 'uploads/' . $photo['image_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM photos WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    }
}

header("Location: dashboard.php");
exit();
?> 