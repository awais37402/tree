<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit;
}

// Fetch all members to show in the "linked members" dropdown and Parent selection
function fetchMembers($conn) {
    $stmt = $conn->prepare("SELECT id, name FROM members");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$members = fetchMembers($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize POST inputs
    $name = $_POST['name'];
    $category = $_POST['category'];
    $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $additional_info = $_POST['additional_info'];
    $linked_members = $_POST['linked_members'] ?? [];
    $relationship_type = $_POST['relationship_type'];

    // File Upload
    $profile_picture = $_FILES['profile_picture']['name'];
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($profile_picture);

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        // Insert the new member into the database
        $stmt = $conn->prepare("INSERT INTO members (name, category, parent_id, profile_picture, additional_info) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $parent_id, $profile_picture, $additional_info]);
        $new_member_id = $conn->lastInsertId();

        // Handle relationships if any linked members are selected
        foreach ($linked_members as $linked_member_id) {
            $stmt = $conn->prepare("INSERT INTO relationships (member1_id, member2_id, relationship_type) VALUES (?, ?, ?)");
            $stmt->execute([$new_member_id, $linked_member_id, $relationship_type]);
        }

        // Success message
        $_SESSION['success_message'] = "Member added successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Failed to upload profile picture.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Member</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .message {
            text-align: center;
            font-weight: bold;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        @media screen and (max-width: 768px) {
            .container {
                padding: 15px;
            }
            input, select, textarea, button {
                width: calc(100% - 10px);
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Member</h1>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success"><?= $_SESSION['success_message'] ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <div class="message error"><?= $_SESSION['error_message'] ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Member Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter Member Name" required>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" placeholder="Enter Category" required>
            </div>

            <div class="form-group">
                <label for="parent_id">Parent Member (Optional):</label>
                <select id="parent_id" name="parent_id">
                    <option value="">-- Select Parent Member --</option>
                    <?php foreach ($members as $member): ?>
                        <option value="<?= $member['id'] ?>"><?= htmlspecialchars($member['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="additional_info">Additional Info:</label>
                <textarea id="additional_info" name="additional_info" rows="4" placeholder="Enter Additional Info"></textarea>
            </div>

            <div class="form-group">
                <label for="profile_picture">Upload Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" required>
            </div>

            

            <button type="submit">Add Member</button>
        </form>
    </div>
</body>
</html>
