<?php
session_start();
include 'db.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Get the recipe ID from the URL
$recipe_id = isset($_GET['recipe_id']) ? $_GET['recipe_id'] : null;

if ($recipe_id) {
    $user_id = $_SESSION['user_id'];
    
    // Check if the recipe is already in the user's favorites
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);
    $favorite = $stmt->fetch();

    if ($favorite) {
        // Remove the recipe from favorites if it's already added
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $recipe_id]);
    } else {
        // Add the recipe to favorites if it's not already added
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $recipe_id]);
    }
}

// Redirect back to the previous page (recipes page)
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>
