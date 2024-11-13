<?php
session_start();
include 'db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone_no = $_POST['phone_no'];

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Check if the uploaded file is an image
        $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $_SESSION['user_id'] . '.' . $fileExtension; // Use user ID for unique file names

            // Move the file to the destination path
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, address = ?, date_of_birth = ?, phone_no = ?, profile_picture = ? WHERE id = ?");
                $stmt->execute([$name, $email, $address, $date_of_birth, $phone_no, $dest_path, $_SESSION['user_id']]);
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            echo "Upload failed. Only images are allowed.";
        }
    } else {
        // Update user information without changing the profile picture
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, address = ?, date_of_birth = ?, phone_no = ? WHERE id = ?");
        $stmt->execute([$name, $email, $address, $date_of_birth, $phone_no, $_SESSION['user_id']]);
    }

    echo "<div class='alert alert-success'>Profile updated successfully!</div>";
}

// Toggle view and edit form
$isEditing = isset($_GET['edit']) && $_GET['edit'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Your Profile</h2>

    <?php if ($isEditing): ?>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" class="form-control" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_no">Phone Number:</label>
                <input type="text" class="form-control" name="phone_no" value="<?php echo htmlspecialchars($user['phone_no']); ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <?php if ($user['profile_picture']): ?>
                    <div>
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" style="width: 100px; height: auto;"/>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" name="profile_picture">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    <?php else: ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Profile Information</h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['date_of_birth']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_no']); ?></p>
                <?php if ($user['profile_picture']): ?>
                    <div>
                        <strong>Profile Picture:</strong><br>
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" style="width: 100px; height: auto;"/>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <a href="profile.php?edit=true" class="btn btn-warning">Edit Profile</a>
    <?php endif; ?>

    <p><a href="index.php" class="btn btn-info">Back to Home</a></p>
</div>
</body>
</html>
