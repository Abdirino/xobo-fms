<?php
// Database connection details
require_once('../connect_db.php');

// Check if user is logged in - using email since that's what we store in session
if (!isset($_SESSION['email'])) {
    // Store the message in session and redirect
    $_SESSION['login_error'] = 'Please log in to access files';
    $_SESSION['active_form'] = 'login';
    header("Location: ../index.php");
    exit();
}

// Get selected category and year from query parameters
$category = isset($_GET['category']) ? $_GET['category'] : null;
$year = isset($_GET['year']) ? $_GET['year'] : null;

// Prepare the SQL query based on filters
$sql = "SELECT * FROM upload WHERE 1=1";
if ($category) {
    $sql .= " AND category = ?";
}
if ($year) {
    $sql .= " AND year = ?";
}
$sql .= " ORDER BY filename ASC";

$stmt = $conn->prepare($sql);

// Bind parameters if they exist
if ($category && $year) {
    $stmt->bind_param("ss", $category, $year);
} elseif ($category) {
    $stmt->bind_param("s", $category);
} elseif ($year) {
    $stmt->bind_param("s", $year);
}

$stmt->execute();
$result = $stmt->get_result();

// Get unique categories and years for filters
$categoriesQuery = "SELECT DISTINCT category FROM upload ORDER BY category";
$yearsQuery = "SELECT DISTINCT year FROM upload WHERE year IS NOT NULL ORDER BY year DESC";
$categories = $conn->query($categoriesQuery);
$years = $conn->query($yearsQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files Repository</title>    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        *{
            box-sizing: border-box;
            text-decoration: none;
            list-style: none;        
        }
        .card {
            transition: transform 0.2s ease-in-out;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .table td {
            vertical-align: middle;
        }
        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }
        .btn-group .btn {
            margin: 0 2px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        .form-select {
            min-width: 150px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">        <div class="mb-4">
            <h2>Document Repository</h2>
        </div><!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-4">
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="files_repository" value="1">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                <?php echo ($category === $cat['category']) ? 'selected' : ''; ?>>
                                <?php echo ucwords(str_replace('_', ' ', $cat['category'])); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        <?php while ($y = $years->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($y['year']); ?>"
                                <?php echo ($year === $y['year']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($y['year']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if ($category || $year): ?>
                        <a href="files.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="table-responsive">            <!-- Category Statistics -->
            <div class="row mb-4">
                <?php
                $categoryStats = $conn->query("SELECT category, COUNT(*) as count FROM upload GROUP BY category");
                while ($stat = $categoryStats->fetch_assoc()):
                    $icon = match($stat['category']) {
                        'purchase_receipts' => 'bi-receipt',
                        'sales_invoices' => 'bi-file-earmark-spreadsheet',
                        'petty_cash_reports' => 'bi-cash',
                        'client_agreements' => 'bi-file-earmark-text',
                        'partner_agreements' => 'bi-file-earmark-text',
                        default => 'bi-file-earmark'
                    };
                ?>
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi <?php echo $icon; ?> fs-3 me-3"></i>
                                <div>
                                    <h6 class="mb-0"><?php echo ucwords(str_replace('_', ' ', $stat['category'])); ?></h6>
                                    <small class="text-muted"><?php echo $stat['count']; ?> files</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 30%">File Name</th>
                        <th style="width: 15%">Category</th>
                        <th style="width: 10%">Year</th>
                        <th style="width: 10%">Size</th>
                        <th style="width: 10%">Type</th>
                        <th style="width: 25%">Actions</th>
                    </tr>
                </thead><tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Format the file path correctly
                        $file_path = "../" . $row['file_path'];
                        
                        // Format the file size
                        $size = $row['filesize'];
                        if ($size < 1024) {
                            $formatted_size = $size . " B";
                        } elseif ($size < 1048576) {
                            $formatted_size = round($size / 1024, 2) . " KB";
                        } else {
                            $formatted_size = round($size / 1048576, 2) . " MB";
                        }
                        ?>                        <tr>
                            <td>
                                <?php
                                $extension = strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION));
                                $fileIcon = match($extension) {
                                    'pdf' => 'bi-file-earmark-pdf text-danger',
                                    'doc', 'docx' => 'bi-file-earmark-word text-primary',
                                    'xls', 'xlsx' => 'bi-file-earmark-excel text-success',
                                    'jpg', 'jpeg', 'png', 'gif' => 'bi-file-earmark-image text-info',
                                    default => 'bi-file-earmark text-secondary'
                                };
                                ?>
                                <i class="bi <?php echo $fileIcon; ?> me-2"></i>
                                <?php echo htmlspecialchars($row['filename']); ?>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    <?php echo ucwords(str_replace('_', ' ', $row['category'])); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['year']): ?>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($row['year']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo $formatted_size; ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <?php echo strtoupper(pathinfo($row['filename'], PATHINFO_EXTENSION)); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?php echo htmlspecialchars($file_path); ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       download="<?php echo htmlspecialchars($row['filename']); ?>"
                                       title="Download file">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                    <a href="<?php echo htmlspecialchars($file_path); ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       target="_blank"
                                       title="View file">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">No files found matching the selected criteria.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>