<?php
session_start();
include 'db.php'; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if a recipe_id is provided
if (isset($_GET['recipe_id'])) {
    $recipe_id = $_GET['recipe_id'];

    // Remove the recipe from the user's favorites
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);

    // Redirect back to the favorites page
    header('Location: favorites.php');
    exit;
} else {
    // If no recipe_id is provided, redirect back to favorites
    header('Location: favorites.php');
    exit;
}
