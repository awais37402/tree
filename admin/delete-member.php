<?php
// Include database connection
include '../config/db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit;
}

// Check if `id` is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: admin-dashboard.php");
    exit;
}

$id = $_GET['id'];

try {
    // Begin a transaction
    $conn->beginTransaction();

    // Fetch the member to delete their profile picture (if any)
    $stmt = $conn->prepare("SELECT profile_picture FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the member doesn't exist, redirect back
    if (!$member) {
        header("Location: admin-dashboard.php");
        exit;
    }

    // Delete profile picture from the server (if exists)
    if (!empty($member['profile_picture']) && file_exists("../uploads/" . $member['profile_picture'])) {
        unlink("../uploads/" . $member['profile_picture']);
    }

    // Delete related entries from the `relationships` table
    $stmt = $conn->prepare("DELETE FROM relationships WHERE member1_id = ? OR member2_id = ?");
    $stmt->execute([$id, $id]);

    // Delete the member from the `members` table
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt->execute([$id]);

    // Commit the transaction
    $conn->commit();

    // Set a success message
    $_SESSION['success_message'] = "Member and related relationships deleted successfully!";
} catch (Exception $e) {
    // Rollback the transaction if an error occurs
    $conn->rollBack();
    $_SESSION['error_message'] = "Error deleting member: " . $e->getMessage();
}

// Redirect back to the admin dashboard
header("Location: dashboard.php");
exit;
?>
