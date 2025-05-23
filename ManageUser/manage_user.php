<?php
require_once('../connect_db.php');

// Check if admin is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Add status column if it doesn't exist
$checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
if ($checkColumn->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
}

// Fetch users from database
$sql = "SELECT id, name, email, role, created_at, status FROM users ORDER BY role, created_at DESC";
$result = $conn->query($sql);

// Permission definitions
$permissions = [
    'admin' => [
        'can_manage_users' => true,
        'can_upload_all' => true,
        'can_view_all' => true,
        'can_delete' => true,
        'viewable_categories' => ['purchase_receipts', 'sales_invoices', 'petty_cash_reports', 'client_agreements', 'partner_agreements'],
    ],
    'user' => [
        'can_manage_users' => false,
        'can_upload_all' => false,
        'can_view_all' => false,
        'can_delete' => false,
        'viewable_categories' => ['client_agreements', 'partner_agreements'],
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .role-badge-admin {
            background-color: #0d6efd;
        }

        .role-badge-user {
            background-color: #6c757d;
        }

        .permission-list {
            font-size: 0.9em;
        }

        .permission-list i {
            width: 20px;
        }

        .alert-popup {
            position: fixed;
            bottom: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 1050;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <h2 class="mb-4">User Management</h2>

        <!-- Role Permissions -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Admin Permissions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled permission-list">
                            <li><i class="bi bi-check-circle-fill text-success"></i> Manage all users</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Upload all document types</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> View & download all documents</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Delete documents</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Access to all categories</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">User Permissions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled permission-list">
                            <li><i class="bi bi-x-circle-fill text-danger"></i> Cannot manage users</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Upload client & partner agreements
                                only</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> View & download agreements only
                            </li>
                            <li><i class="bi bi-x-circle-fill text-danger"></i> Cannot delete documents</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i> Limited category access</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Registered Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle me-2"></i>
                                            <?php echo htmlspecialchars($user['name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge role-badge-<?php echo $user['role']; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</body>

</html>