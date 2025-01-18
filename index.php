<?php
include '../config/db.php';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Fetch members from the database
function fetchMembers($conn) {
    $stmt = $conn->prepare("SELECT * FROM members ORDER BY id ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Recursive function to render the tree
function renderTree($members, $parentId = null, $isParent = false) {
    $children = array_filter($members, function ($member) use ($parentId) {
        return $member['parent_id'] == $parentId || ($parentId === null && $member['parent_id'] == 0);
    });

    if (!empty($children)) {
        $html = '<ul>';
        foreach ($children as $child) {
            $childClass = $isParent ? 'parent-node' : 'child-node';
            $html .= '<li>';
            $html .= '<div class="node ' . $childClass . '" onclick="showDetails(\'' 
                . htmlspecialchars($child['name']) . '\', \'' 
                . htmlspecialchars($child['category']) . '\', \'' 
                . htmlspecialchars($child['additional_info'] ?? 'No additional info available') . '\', \'' 
                . htmlspecialchars($child['profile_picture'] ?? '') . '\')">';

            if (!empty($child['profile_picture'])) {
                $html .= '<img src="uploads/' . htmlspecialchars($child['profile_picture']) . '" alt="' . htmlspecialchars($child['name']) . '">';
            }
            $html .= '<p><strong>Name:</strong> ' . htmlspecialchars($child['name']) . '</p>';
            $html .= '<p><strong>Category:</strong> ' . htmlspecialchars($child['category']) . '</p>';
            $html .= '</div>';
            $html .= renderTree($members, $child['id'], true);
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    return '';
}

$members = fetchMembers($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Tree</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    text-align: center;
}

h1 {
    margin-bottom: 30px;
    font-size: 28px;
}

.tree ul {
    padding-top: 20px;
    display: flex;
    margin-left: 0;
    margin-left: 0;
}

.tree li {
    list-style-type: none;
    position: relative;
    padding: 20px 10px 0 10px;
    text-align: center;
}

/* Lines connecting nodes */
.tree li::before,
.tree li::after {
    content: '';
    position: absolute;
    top: 0;
    width: 50%;
    height: 20px;
    border-top: 2px solid #000;
}

.tree li::before {
    left: 0;
    border-right: 2px solid #000;
}

.tree li::after {
    right: 0;
    border-left: 2px solid #000;
}

/* Remove lines for single children */
.tree li:only-child::before,
.tree li:only-child::after {
    display: none;
}

/* Remove right line for the first child */
.tree li:first-child::before {
    border-right: none;
}

/* Remove left line for the last child */
.tree li:last-child::after {
    border-left: none;
}

/* Remove lines for parents without children */
.tree li:empty::before,
.tree li:empty::after {
    display: none;
}

/* Node styles */
.node {
    display: inline-block;
    text-align: center;
    background-color: #fff;
    border: 2px solid #000;
    padding: 10px;
    border-radius: 10px;
    cursor: pointer;
}

.node img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.node p {
    margin: 5px 0;
}

/* Parent Node Styles */
.parent-node {
    background-color: #D3F8E2; /* Light green for parent nodes */
    border-color: #4CAF50; /* Green border */
}

/* Child Node Styles */
.child-node {
    background-color: #F8D3E2; /* Light pink for child nodes */
    border-color: #F44336; /* Red border */
}

/* Popup Styles */
.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border: 2px solid #000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    z-index: 1000;
    text-align: left;
}

.popup img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

.popup-close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
    color: #000;
}

.popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

    </style>
</head>
<body>
<h1><a href="admin/admin-login.php" style="text-decoration: none; color: inherit;">Family Tree</a></h1>
    <div class="tree">
        <?= renderTree($members); ?>
    </div>

    <div class="popup-overlay" onclick="closePopup()"></div>
    <div class="popup" id="popup">
        <span class="popup-close" onclick="closePopup()">×</span>
        <img id="popup-img" src="" alt="">
        <p id="popup-name"><strong>Name:</strong></p>
        <p id="popup-category"><strong>Category:</strong></p>
        <p id="popup-info"><strong>Info:</strong></p>
    </div>

    <script>
        function showDetails(name, category, info, image) {
            document.getElementById('popup-name').innerHTML = `<strong>Name:</strong> ${name}`;
            document.getElementById('popup-category').innerHTML = `<strong>Category:</strong> ${category}`;
            document.getElementById('popup-info').innerHTML = `<strong>Info:</strong> ${info}`;
            document.getElementById('popup-img').src = 'uploads/' + image;
            document.querySelector('.popup-overlay').style.display = 'block';
            document.getElementById('popup').style.display = 'block';
        }

        function closePopup() {
            document.querySelector('.popup-overlay').style.display = 'none';
            document.getElementById('popup').style.display = 'none';
        }
    </script>
</body>
</html>
