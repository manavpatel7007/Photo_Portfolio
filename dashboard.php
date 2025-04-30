<?php
require_once 'db.php';
require_once 'session.php';
checkLogin();

// Fetch all photos for the current user
$stmt = $pdo->prepare("SELECT * FROM photos WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$photos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Photo Portfolio</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Photo Portfolio Dashboard</h1>
            <div class="user-info">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <div class="add-photo-section">
            <a href="add_item.php" class="btn">Add New Photo</a>
        </div>

        <div class="photos-grid">
            <?php if (empty($photos)): ?>
                <div class="no-photos">
                    <p>No photos found. Click "Add New Photo" to start uploading.</p>
                </div>
            <?php else: ?>
                <?php foreach ($photos as $photo): ?>
                    <div class="photo-card">
                        <img src="uploads/<?php echo htmlspecialchars($photo['image_path']); ?>" alt="<?php echo htmlspecialchars($photo['title']); ?>">
                        <h3><?php echo htmlspecialchars($photo['title']); ?></h3>
                        <p class="category"><?php echo htmlspecialchars($photo['category']); ?></p>
                        <p class="description"><?php echo htmlspecialchars($photo['description']); ?></p>
                        <div class="actions">
                            <a href="add_item.php?id=<?php echo $photo['id']; ?>" class="btn">Edit</a>
                            <a href="delete_item.php?id=<?php echo $photo['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this photo?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 