<?php
session_start();
include 'db.php'; // Database connection

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch existing recipe if editing
if (isset($_GET['id'])) {
    $recipe_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch();

    if (!$recipe) {
        die("Recipe not found.");
    }
}

// Handle recipe update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $image = $_FILES['image']['name'] ? $_FILES['image']['name'] : $recipe['image'];
    $target = "../uploads/" . basename($image);

    if ($_FILES['image']['name']) {
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // Update the recipe in the database
    $stmt = $pdo->prepare("UPDATE recipes SET title = ?, description = ?, ingredients = ?, instructions = ?, image = ? WHERE id = ?");
    $stmt->execute([$title, $description, $ingredients, $instructions, $target, $recipe_id]);
    header("Location: manage_recipes.php");
    exit();
}
// Fetch locations for the current recipe
$stmt = $pdo->prepare("SELECT * FROM locations WHERE recipe_id = ?");
$stmt->execute([$recipe_id]);
$locations = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Edit Recipe</h2>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Recipe Title:</label>
            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" name="description" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="ingredients">Ingredients:</label>
            <textarea class="form-control" name="ingredients" required><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="instructions">Instructions:</label>
            <textarea class="form-control" name="instructions" required><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Recipe Image:</label>
            <input type="file" class="form-control-file" name="image">
            <small>Current image: <img src="<?php echo htmlspecialchars($recipe['image']); ?>" width="100" alt="Current Recipe Image"></small>
        </div>
        <button type="submit" class="btn btn-primary">Update Recipe</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
