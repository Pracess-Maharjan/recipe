<?php
session_start();
include 'db.php'; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's favorite recipes
$stmt = $pdo->prepare("SELECT r.id, r.title, r.description, r.image FROM recipes r JOIN favorites f ON r.id = f.recipe_id WHERE f.user_id = ?");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorite Recipes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">My Favorite Recipes</h2>
    
    <?php if (count($favorites) > 0): ?>
        <div class="row">
            <?php foreach ($favorites as $recipe): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars('uploads/' . basename($recipe['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($recipe['description']); ?></p>

                            <!-- View full details link -->
                            <a href="recipe_detail.php?id=<?php echo $recipe['id']; ?>" class="btn btn-info">View Details</a>

                            <!-- Remove from favorites button -->
                            <a href="remove_favorite.php?recipe_id=<?php echo $recipe['id']; ?>" class="btn btn-danger">Remove from Favorites</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You don't have any favorite recipes yet.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
