<?php
session_start();
include 'db.php'; // Database connection

// Fetch all recipes for display, filtered by search if a search term is provided
$search = isset($_GET['search']) ? $_GET['search'] : '';
$stmt = $pdo->prepare("SELECT * FROM recipes WHERE title LIKE ?");
$stmt->execute(["%$search%"]);
$recipes = $stmt->fetchAll();

// Fetch user's favorites if logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$favorites = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT recipe_id FROM favorites WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $favorites = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            height: 100%;
        }
        .card-img-top {
            width: 100%;
            height: 200px; /* Set a fixed height for all images */
            object-fit: cover; /* Scale and crop images to fit */
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Recipes</h2>
    
    <!-- Search bar for recipes -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search recipes by name..." value="<?php echo htmlspecialchars($search); ?>">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php foreach ($recipes as $recipe): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?php echo htmlspecialchars('uploads/' . basename($recipe['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($recipe['description']); ?></p>
                        <a href="recipe_detail.php?id=<?php echo $recipe['id']; ?>" class="btn btn-primary">View Details</a>
                        <?php if ($user_id): ?>
                            <a href="add_to_favorites.php?id=<?php echo $recipe['id']; ?>" class="btn btn-success <?php echo in_array($recipe['id'], $favorites) ? 'disabled' : ''; ?>">
                                <?php echo in_array($recipe['id'], $favorites) ? 'Added to Favorites' : 'Add to Favorites'; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
