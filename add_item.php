<?php
require_once 'db.php';
require_once 'session.php';
checkLogin();

$photo = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM photos WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $photo = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            if ($photo) {
                // Update existing photo
                $stmt = $pdo->prepare("UPDATE photos SET title = ?, category = ?, description = ?, image_path = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$title, $category, $description, $fileName, $photo['id'], $user_id]);
            } else {
                // Insert new photo
                $stmt = $pdo->prepare("INSERT INTO photos (title, category, description, image_path, user_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $category, $description, $fileName, $user_id]);
            }
            header("Location: dashboard.php");
            exit();
        }
    } elseif ($photo) {
        // Update without changing the image
        $stmt = $pdo->prepare("UPDATE photos SET title = ?, category = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $category, $description, $photo['id'], $user_id]);
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $photo ? 'Edit' : 'Add'; ?> Photo - Photo Portfolio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h1><?php echo $photo ? 'Edit' : 'Add'; ?> Photo</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo $photo ? htmlspecialchars($photo['title']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" value="<?php echo $photo ? htmlspecialchars($photo['category']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo $photo ? htmlspecialchars($photo['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo" <?php echo $photo ? '' : 'required'; ?>>
                <?php if ($photo): ?>
                    <p>Current photo: <?php echo htmlspecialchars($photo['image_path']); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn"><?php echo $photo ? 'Update' : 'Add'; ?> Photo</button>
                <a href="dashboard.php" class="btn cancel">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html> 