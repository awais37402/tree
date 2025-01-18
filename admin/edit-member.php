<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];

// Fetch the member being edited
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header("Location: dashboard.php");
    exit;
}

// Fetch all members for dropdowns
function fetchMembers($conn) {
    $stmt = $conn->prepare("SELECT id, name FROM members");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$members = fetchMembers($conn);

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $additional_info = $_POST['additional_info'];

    // Handle profile picture upload if provided
    if (!empty($_FILES['profile_picture']['name'])) {
        $profile_picture = $_FILES['profile_picture']['name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($profile_picture);

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE members SET name = ?, category = ?, parent_id = ?, profile_picture = ?, additional_info = ? WHERE id = ?");
            $stmt->execute([$name, $category, $parent_id, $profile_picture, $additional_info, $id]);
        }
    } else {
        $stmt = $conn->prepare("UPDATE members SET name = ?, category = ?, parent_id = ?, additional_info = ? WHERE id = ?");
        $stmt->execute([$name, $category, $parent_id, $additional_info, $id]);
    }

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Basic styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            width: 100%;
        }
        input, select, textarea, button {
            width: calc(100% - 20px);
            margin: 10px auto;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
        }
        button {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Member</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Member Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($member['name']) ?>" required>
        </div>

        <div>
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="<?= htmlspecialchars($member['category']) ?>" required>
        </div>

        <div>
            <label for="parent_id">Parent Member (Optional):</label>
            <select id="parent_id" name="parent_id">
                <option value="">-- Select Parent Member --</option>
                <?php foreach ($members as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $m['id'] == $member['parent_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="additional_info">Additional Info:</label>
            <textarea id="additional_info" name="additional_info" rows="4"><?= htmlspecialchars($member['additional_info']) ?></textarea>
        </div>

        <div>
            <label for="profile_picture">Upload Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture">
        </div>

        <button type="submit">Update Member</button>
    </form>
</div>
</body>
</html>