<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit;
}

// Fetch all members
function fetchMembers($conn) {
    $stmt = $conn->prepare("SELECT * FROM members ORDER BY id ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$members = fetchMembers($conn);

function displayMessage($type, $message) {
    echo "<div class='message $type'>$message</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #444;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            margin: 10px auto;
            margin-left: 20px;
            padding: 10px 15px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
        }
        .message {
            text-align: center;
            margin: 10px auto;
            padding: 10px;
            width: 90%;
            max-width: 600px;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }

        /* Table styles */
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007BFF;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        img {
            border-radius: 50%;
        }
        td a {
            color: #007BFF;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            th, td {
                padding: 10px;
                text-align: left;
                display: block;
            }
            th {
                background: #007BFF;
                color: #fff;
                font-weight: bold;
            }
            tr {
                margin-bottom: 10px;
            }
            td {
                border: none;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            td img {
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a href="add-member.php" class="btn">Add Member</a>
    <a href="../index.php" class="btn">Website</a>

    <?php
    if (isset($_SESSION['success_message'])) {
        displayMessage('success', $_SESSION['success_message']);
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        displayMessage('error', $_SESSION['error_message']);
        unset($_SESSION['error_message']);
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Profile Picture</th>
                <th>Additional Info</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?= htmlspecialchars($member['id']) ?></td>
                    <td><?= htmlspecialchars($member['name']) ?></td>
                    <td><?= htmlspecialchars($member['category']) ?></td>
                    <td>
                        <img src="../uploads/<?= htmlspecialchars($member['profile_picture']) ?>" 
                             alt="<?= htmlspecialchars($member['name']) ?>" width="50">
                    </td>
                    <td><?= isset($member['additional_info']) ? htmlspecialchars($member['additional_info']) : 'No info available' ?></td>
                    <td>
                        <a href="edit-member.php?id=<?= htmlspecialchars($member['id']) ?>">Edit</a> |
                        <a href="delete-member.php?id=<?= htmlspecialchars($member['id']) ?>" 
                           onclick="return confirm('Are you sure you want to delete this member?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>