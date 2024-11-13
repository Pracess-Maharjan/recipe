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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            background-color: #fff;
            border: none;
            border-radius: 50%;
            padding: 5px;
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
            <?php
            // Fetch average rating and total review count for each recipe
            $stmt = $pdo->prepare("SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews FROM reviews WHERE recipe_id = ?");
            $stmt->execute([$recipe['id']]);
            $ratingData = $stmt->fetch();
            $averageRating = $ratingData['average_rating'];
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
