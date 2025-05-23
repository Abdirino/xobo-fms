<?php
// This script handles file uploads and stores file information in a database
// session_start();

// Include database connection
require_once('../connect_db.php');

// Initialize message variables for popup alerts
$message = '';
$messageType = '';

// Set upload directory path
$target_dir = "../uploads/";

// Check if the request is a POST request (form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check user permissions
    $allowed_categories = $_SESSION['role'] === 'admin' 
        ? ['purchase_receipts', 'sales_invoices', 'petty_cash_reports', 'client_agreements', 'partner_agreements']
        : ['client_agreements', 'partner_agreements'];    // Check if a file was uploaded and there are no upload errors
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0 && isset($_POST['category'])) {
        $category = $_POST['category'];
        // Validate category
        if (!in_array($category, $allowed_categories)) {
            $message = "You don't have permission to upload to this category.";
            $messageType = "danger";
            $_SESSION['message'] = $message;
            $_SESSION['messageType'] = $messageType;
            header("Location: ../Admin_Page/admin.php?upload");
            exit();
        }

        // File was uploaded successfully
        $filename = $_FILES["file"]["name"];
        $target_file = $target_dir . basename($filename);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Define allowed file types for security
        $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");

        // Validate file type against allowed types
        if (!in_array($file_type, $allowed_types)) {
            $message = "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
            $messageType = "danger";
        } else {            // Get category and year for proper file path storage            $category = $_POST['category'] ?? null;
            $year = $_POST['year'] ?? date('Y');

            // Set up target directory structure
            $target_subdir = $target_dir . $category . '/';
            if ($year) {
                $target_subdir .= $year . '/';
            }

            // Create directory if it doesn't exist
            if (!file_exists($target_subdir)) {
                if (!mkdir($target_subdir, 0755, true)) {
                    $message = "Error: Failed to create directory structure.";
                    $messageType = "danger";
                    die();
                }
            }

            // Set the final target file path and ensure it's unique
            $filename = basename($_FILES["file"]["name"]);
            $fileinfo = pathinfo($filename);
            $counter = 1;
            while (file_exists($target_subdir . $filename)) {
                $filename = $fileinfo['filename'] . '_' . $counter . '.' . $fileinfo['extension'];
                $counter++;
            }
            $target_file = $target_subdir . $filename;

            // Attempt to move the file from temporary location to target directory
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // Get file details from the $_FILES array
                $filesize = $_FILES["file"]["size"];
                $filetype = $_FILES["file"]["type"];
                $file_path = str_replace($target_dir, "uploads/", $target_file);                // Prepare SQL query with prepared statements to prevent SQL injection
                $sql = "INSERT INTO upload (filename, filesize, filetype, file_path, category, year) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $message = "Database Error: " . $conn->error;
                    $messageType = "danger";
                    die();
                }
                $stmt->bind_param("sissss", $filename, $filesize, $filetype, $file_path, $category, $year);

                // Execute the query and check if it was successful
                if ($stmt->execute()) {
                    $message = "File uploaded successfully!";
                    $messageType = "success";
                    echo "<script>document.getElementById('uploadForm').reset();</script>";
                } else {
                    $message = "Database Error: " . $stmt->error;
                    $messageType = "danger";
                }

                $stmt->close();
            } else {
                $message = "Sorry, there was an error uploading your file.";
                $messageType = "danger";
            }
        }
    } else {
        $message = "Please select a file to upload.";
        $messageType = "warning";
    }
}

// Check for session messages
if (isset($_SESSION['message']) && isset($_SESSION['messageType'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    // Clear the session messages
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Files</title>
    <link rel="stylesheet" href="upload.css">
    <style>
        .alert-popup {
            position: fixed;
            bottom: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 1050;
        }
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1>Upload Files</h1>
        <form action="../Admin_Page/admin.php?upload" method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="mb-3">
                <label for="category" class="form-label">Document Category</label>                <select class="form-select" name="category" id="category" required>
                    <option value="">Select a category</option>
                    <?php
                    $allowed_categories = $_SESSION['role'] === 'admin' 
                        ? ['purchase_receipts', 'sales_invoices', 'petty_cash_reports', 'client_agreements', 'partner_agreements']
                        : ['client_agreements', 'partner_agreements'];
                    
                    $category_labels = [
                        'purchase_receipts' => 'Purchase Receipts',
                        'sales_invoices' => 'Sales Invoices',
                        'petty_cash_reports' => 'Petty Cash Reports',
                        'client_agreements' => 'Client Agreements',
                        'partner_agreements' => 'Partner Agreements'
                    ];

                    foreach ($allowed_categories as $cat) {
                        echo '<option value="' . $cat . '">' . $category_labels[$cat] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3" id="yearField" style="display: none;">
                <label for="year" class="form-label">Year</label>
                <select class="form-select" name="year" id="year">
                    <?php
                    $currentYear = date('Y');
                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                        echo "<option value=\"$year\">$year</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">Select file</label>
                <input type="file" class="form-control" name="file" id="file" required>
                <div class="form-text">Allowed file types: JPG, JPEG, PNG, GIF, PDF</div>
            </div>
            <button type="submit" class="btn">Upload File</button>
        </form>
    </div> <!-- Alert popup for messages -->    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-popup alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
            setTimeout(function() {
                var alertElement = document.querySelector('.alert-popup');
                if (alertElement) {
                    var bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            // Auto hide alert after 5 seconds
            if ($('.alert-popup').length > 0) {
                setTimeout(function () {
                    $('.alert-popup').fadeOut('slow');
                }, 5000);
            }            // Show year field for all categories
            $('#category').change(function () {
                const category = $(this).val();
                if (category) {
                    $('#yearField').show();
                    $('#year').prop('required', true);
                } else {
                    $('#yearField').hide();
                    $('#year').prop('required', false);
                }
            });

            // Clear form after successful upload
            if ($('.alert-success').length > 0) {
                $('#uploadForm')[0].reset();
                $('#yearField').hide();
            }
        });
    </script>
</body>

</html>