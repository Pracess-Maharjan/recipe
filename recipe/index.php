<?php
session_start();
include 'db.php';

// Fetch demo recipes (you can replace this with a database query later)
$stmt = $pdo->query("SELECT * FROM recipes LIMIT 3");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Website</title>
    <!-- FontAwesome CDN for heart icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .card {
            height: 100%;
            position: relative;
            cursor: pointer;
        }
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .favorite-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.6); /* Light background for better visibility */
            border: none;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            z-index: 1;
        }
        .favorite-icon i {
            color: #e74c3c;
            font-size: 24px;
            transition: color 0.3s ease;
        }
        .favorite-icon.active i {
            color: red; /* Red when added to favorites */
        }
        .favorite-icon.inactive i {
            color: #ccc; /* Default color when not added */
        }
        .stars {
            color: #f39c12;
            font-size: 18px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Recipe Website</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="recipes.php">Recipes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="favorites.php">Favorites</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Top 3 Recipes</h2>

    <div class="row">
        <?php foreach ($recipes as $recipe): ?>
            <?php
            // Fetch average rating and total review count for each recipe
            $stmt = $pdo->prepare("SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews FROM reviews WHERE recipe_id = ?");
            $stmt->execute([$recipe['id']]);
            $ratingData = $stmt->fetch();
            $averageRating = $ratingData['average_rating'] ?? 0;
            $totalReviews = $ratingData['total_reviews'];
            ?>
            <div class="col-md-4 mb-4">
                <div class="card" onclick="window.location.href='recipe_detail.php?id=<?php echo $recipe['id']; ?>'">
                    <img src="<?php echo htmlspecialchars('uploads/' . basename($recipe['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">

                    <!-- Favorite icon button -->
                    <?php if ($user_id): ?>
                        <button class="favorite-icon <?php echo in_array($recipe['id'], $favorites) ? 'active' : 'inactive'; ?>" onclick="event.stopPropagation(); window.location.href='add_to_favorites.php?recipe_id=<?php echo $recipe['id']; ?>'">
                            <i class="fas fa-heart"></i>
                        </button>
                    <?php endif; ?>

                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($recipe['description']); ?></p>
                        
                        <!-- Display ratings as stars -->
                        <div class="recipe-rating">
                            <div class="d-flex align-items-center">
                                <div class="stars">
                                    <!-- Display filled and unfilled stars for average rating -->
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= round($averageRating)) {
                                            echo '<span class="star">&#9733;</span>'; // Filled star
                                        } else {
                                            echo '<span class="star">&#9734;</span>'; // Unfilled star
                                        }
                                    }
                                    ?>
                                </div>
                                <span class="ml-2">
                                    <?php echo number_format($averageRating, 1); ?>/5
                                </span>
                                <span class="ml-2 text-muted">
                                    (<?php echo $totalReviews; ?> <?php echo $totalReviews == 1 ? 'review' : 'reviews'; ?>)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
