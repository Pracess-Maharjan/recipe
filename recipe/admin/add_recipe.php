<?php
session_start();
include 'db.php'; // Database connection

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission for new recipe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image); // Adjust the target path

// Move the uploaded file to the target directory
if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
    // Store only the relative path in the database
    $imagePath = "uploads/" . basename($image);
    $stmt = $pdo->prepare("INSERT INTO recipes (title, description, ingredients, instructions, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $ingredients, $instructions, $imagePath]);
    header("Location: manage_recipes.php");
    exit();
} else {
    $error_message = "Failed to upload image.";
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Recipe</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Add Recipe</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Recipe Title:</label>
            <input type="text" class="form-control" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="ingredients">Ingredients:</label>
            <textarea class="form-control" name="ingredients" required></textarea>
        </div>
        <div class="form-group">
            <label for="instructions">Instructions:</label>
            <textarea class="form-control" name="instructions" required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Recipe Image:</label>
            <input type="file" class="form-control-file" name="image" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Recipe</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
