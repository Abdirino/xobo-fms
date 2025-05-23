<?php
session_start();
require_once('../connect_db.php');

// Check if user is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = intval($_POST['user_id'] ?? 0);

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit();
    }

    switch ($action) {
        case 'toggle_status':
            $currentStatus = $_POST['current_status'] ?? '';
            $newStatus = $currentStatus === 'active' ? 'inactive' : 'active';
            
            $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role != 'admin'");
            $stmt->bind_param('si', $newStatus, $userId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
            }
            $stmt->close();
            break;

        case 'delete':
            // Don't allow deleting admin users
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
            $stmt->bind_param('i', $userId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
            }
            $stmt->close();
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
