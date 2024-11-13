

<header class="bg-light py-2">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="#" class="navbar-brand text-warning"><strong>allrecipes</strong></a>
        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Find a recipe or ingredient" aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-outline-warning" type="submit">Search</button>
        </form>
        <div class="dropdown">
            <a class="btn btn-light dropdown-toggle" href="#" role="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                My Account
            </a>
            <ul class="dropdown-menu" aria-labelledby="accountDropdown">
                <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                <li><a class="dropdown-item" href="favorites.php">Saved Recipes & Collections</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Add a Recipe</a></li>
                <li><a class="dropdown-item" href="#">Help</a></li>
            </ul>
        </div>
    </div>
</header>


<!-- Main Navigation -->
<nav class="bg-warning py-2">
    <div class="container">
        <ul class="nav">
            <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="recipe.php">Recipes</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="about.php">About us</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="#">Contact us</a></li>
            
        </ul>
    </div>
</nav>