<?php
// Check if user is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Add audit_logs table if it doesn't exist
$checkTable = $conn->query("SHOW TABLES LIKE 'audit_logs'");
if ($checkTable->num_rows === 0) {
    $conn->query("CREATE TABLE audit_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT,
        action VARCHAR(255) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
}

// Get audit logs with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = "SELECT al.*, u.name as user_name, u.email 
          FROM audit_logs al 
          LEFT JOIN users u ON al.user_id = u.id 
          ORDER BY al.created_at DESC 
          LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Get total count for pagination
$totalRows = $conn->query("SELECT COUNT(*) as count FROM audit_logs")->fetch_assoc()['count'];
$totalPages = ceil($totalRows / $limit);
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">System Audit Logs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($log = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td>
                                        <?php if ($log['user_name']): ?>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-user me-2"></i>
                                                <?php echo htmlspecialchars($log['user_name']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($log['action']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['details']); ?></td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars($log['ip_address']); ?></small></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?audit_logs&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
