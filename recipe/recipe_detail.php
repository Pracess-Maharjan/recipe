<?php
session_start();
include 'db.php'; // Database connection

// Fetch recipe details
if (isset($_GET['id'])) {
    $recipe_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch();

    if (!$recipe) {
        die("Recipe not found.");
    }
}

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $rating = (int)$_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $pdo->prepare("INSERT INTO reviews (recipe_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$recipe_id, $user_id, $rating, $comment]);
    header("Location: recipe_detail.php?id=" . $recipe_id);
    exit();
}

// Fetch reviews for the recipe
$stmt = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.recipe_id = ?");
$stmt->execute([$recipe_id]);
$reviews = $stmt->fetchAll();

// Restaurant and hotel locations (example data)
$locations = [
    ['name' => 'The Gourmet Place', 'lat' => 40.712776, 'lng' => -74.005974],
    ['name' => 'Delicious Bites', 'lat' => 40.730610, 'lng' => -73.935242],
    ['name' => 'Tasty Spot', 'lat' => 40.758896, 'lng' => -73.985130]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - Recipe Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 800px;
        }
        .card-img-top {
            max-height: 300px;
            object-fit: cover;
            margin-bottom: 20px;
        }
        #map {
            width: 100%;
            height: 400px;
            margin-top: 20px;
            border: 2px solid #ddd;
        }
        .review-section, .map-section, .review-form {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center"><?php echo htmlspecialchars($recipe['title']); ?></h2>
    <img src="<?php echo htmlspecialchars('uploads/' . basename($recipe['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">

    <h4>Description</h4>
    <p><?php echo htmlspecialchars($recipe['description']); ?></p>

    <h4>Ingredients</h4>
    <p><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>

    <h4>Instructions</h4>
    <p><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>

    <!-- Map Section -->
    <div class="map-section">
        <h4>Find This Recipe in Restaurants and Hotels</h4>
        <div id="map"></div>
    </div>

    <!-- Reviews Section -->
<div class="review-section">
    <h4>Reviews</h4>
    <div class="mb-4">
        <?php foreach ($reviews as $review): ?>
            <div class="border p-3 mb-2">
                <strong><?php echo htmlspecialchars($review['name']); ?></strong>
                <div class="stars">
                    <!-- Display filled and unfilled stars based on the rating -->
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $review['rating']) {
                            echo '<span class="star">&#9733;</span>'; // Filled star
                        } else {
                            echo '<span class="star">&#9734;</span>'; // Unfilled star
                        }
                    }
                    ?>
                </div>
                <p><?php echo htmlspecialchars($review['comment']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .stars .star {
        font-size: 20px;
        color: #f39c12; /* Filled star color */
        margin-right: 2px;
    }
    .stars .star:not(.filled) {
        color: #ddd; /* Unfilled star color */
    }
</style>


<!-- Review Form -->
<?php if (isset($_SESSION['user_id'])): ?>
    <h4>Submit Your Review</h4>
    <form method="post" class="review-form">
        <div class="form-group">
            <label for="rating">Rating:</label>
            <div id="star-rating" class="mb-2">
                <!-- Display 5 stars as clickable elements -->
                <span class="star" data-value="1">&#9734;</span>
                <span class="star" data-value="2">&#9734;</span>
                <span class="star" data-value="3">&#9734;</span>
                <span class="star" data-value="4">&#9734;</span>
                <span class="star" data-value="5">&#9734;</span>
                <span id="rating-display" class="ml-2"></span> <!-- Display rating value -->
            </div>
            <!-- Hidden input to store the selected rating value -->
            <input type="hidden" name="rating" id="rating-input" required>
        </div>
        <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea name="comment" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
<?php else: ?>
    <p>You must be logged in to submit a review. <a href="login.php">Login here</a>.</p>
<?php endif; ?>

<style>
    #star-rating .star {
        font-size: 24px;
        cursor: pointer;
        color: #ddd; /* Default color for unselected stars */
    }
    #star-rating .star.selected {
        color: #f39c12; /* Color for selected stars */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('#star-rating .star');
        const ratingInput = document.getElementById('rating-input');
        const ratingDisplay = document.getElementById('rating-display');

        stars.forEach(star => {
            star.addEventListener('click', function () {
                const ratingValue = this.getAttribute('data-value');
                ratingInput.value = ratingValue;
                ratingDisplay.textContent = `Rating: ${ratingValue}/5`;

                // Update the stars to show the selected state
                stars.forEach(s => {
                    s.classList.remove('selected');
                    if (s.getAttribute('data-value') <= ratingValue) {
                        s.classList.add('selected');
                    }
                });
            });
        });
    });
</script>



<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
<script>
    var locations = <?php echo json_encode($locations); ?>;

    function initMap() {
        // Set initial map options
        var mapOptions = {
            zoom: 12,
            center: { lat: 40.730610, lng: -73.935242 } // Example center point
        };
        
        // Initialize map
        var map = new google.maps.Map(document.getElementById('map'), mapOptions);
        
        // Marker data
        var locations = <?php echo json_encode($locations); ?>;
        
        // Add markers to the map
        locations.forEach(function(location) {
            var marker = new google.maps.Marker({
                position: { lat: location.lat, lng: location.lng },
                map: map,
                title: location.name
            });
            
            // Info window for each marker
            var infoWindow = new google.maps.InfoWindow({
                content: '<h6>' + location.name + '</h6>'
            });
            
            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });
        });
    }
    // Initialize the map when the page loads
    window.onload = initMap;
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
