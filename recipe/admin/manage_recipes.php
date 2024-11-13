<?php
session_start();
include 'db.php'; // Database connection

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle recipe deletion
if (isset($_GET['delete'])) {
    $recipe_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    header("Location: manage_recipes.php");
    exit();
}

// Fetch all recipes for display
$stmt = $pdo->query("SELECT * FROM recipes");
$recipes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Recipes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table img {
            max-width: 100px; /* Set a max width for the images in the table */
            height: auto;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Manage Recipes</h2>

    <a href="add_recipe.php" class="btn btn-primary mb-3">Add New Recipe</a>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Ingredients</th>
                <th>Instructions</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recipes as $recipe): ?>
                <tr>
                    <td><?php echo htmlspecialchars($recipe['id']); ?></td>
                    <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                    <td><?php echo htmlspecialchars($recipe['description']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></td>
                    <td><img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>"></td>
                    <td>
                        <a href="edit_recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete=<?php echo $recipe['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this recipe?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
