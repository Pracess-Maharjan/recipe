<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Sample functions to fetch counts (these should connect to your actual database)
include 'db.php';

function getUserCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    return $stmt->fetchColumn();
}

function getRecipeCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM recipes"); // Assuming you have a recipes table
    return $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <a class="navbar-brand" href="admin_index.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_recipes.php">Manage Recipes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">Settings</a>
                </li>
            </ul>
        </div>
        <a href="logout.php" class="btn btn-danger my-2 my-sm-0">Logout</a>
    </nav>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo getUserCount(); ?> registered users</p>
                    <a href="manage_users.php" class="btn btn-light">Manage Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total Recipes</h5>
                    <p class="card-text"><?php echo getRecipeCount(); ?> recipes available</p>
                    <a href="manage_recipes.php" class="btn btn-light">Manage Recipes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-4">
                <div class="card-body">
                    <h5 class="card-title">Settings</h5>
                    <p class="card-text">Manage site settings and content.</p>
                    <a href="settings.php" class="btn btn-light">Settings</a>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <footer class="mt-4">
            <p>&copy; <?php echo date("Y"); ?> Recipe Website Admin Panel</p>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
