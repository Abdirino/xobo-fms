<?php
// This script handles file uploads and stores file information in a database

// Initialize message variables for popup alerts
$message = '';
$messageType = '';

// Check if the request is a POST request (form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file was uploaded and there are no upload errors
    // $_FILES['file'] contains the uploaded file information

    if (isset($_FILES["upload"]) && $_FILES["upload"]["error"] == 0) {
        // Set the target directory for file uploads
        $target_dir = "uploads/"; // Directory where files will be stored
        // Create the complete file path by combining directory and filename
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        // Get the file extension in lowercase for validation
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Define allowed file types for security
        // This prevents users from uploading potentially harmful files
        $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");        // Validate file type against allowed types
        if (!in_array($file_type, $allowed_types)) {
            echo "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
        } else {
            // Attempt to move the file from temporary location to target directory
            // move_uploaded_file() returns true if the move was successful
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // File upload successful, prepare to store file information
                // Get file details from the $_FILES array
                $filename = $_FILES["file"]["name"];    // Original filename
                $filesize = $_FILES["file"]["size"];    // File size in bytes
                $filetype = $_FILES["file"]["type"];    // File MIME type                // Set up database connection parameters
                $db_host = "localhost";           // Database server (usually localhost)
                $db_user = "root";               // MySQL username
                $db_pass = "";                   // MySQL password
                $db_name = "xobo-file-system";   // Database name

                // Create a new database connection
                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

                // Check if the connection was successful
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Prepare SQL query to insert file information
                // This stores the file metadata in the database for tracking
                $sql = "INSERT INTO upload (filename, filesize, filetype) VALUES ('$filename', $filesize, '$filetype')";                // Prepare SQL query with prepared statements to prevent SQL injection
                $sql = "INSERT INTO upload (filename, filesize, filetype, file_path) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $file_path = $target_dir . $filename;
                $stmt->bind_param("siss", $filename, $filesize, $filetype, $file_path);

                // Execute the query and check if it was successful
                if ($stmt->execute()) {
                    $message = "File uploaded successfully!";
                    $messageType = "success";
                } else {
                    $message = "Database Error: " . $stmt->error;
                    $messageType = "danger";
                }
                $stmt->close();

                $conn->close();
            } else {                $message = "Sorry, there was an error uploading your file.";
                $messageType = "danger";
            }
        }
    } else {
        $message = "Please select a file to upload.";
        $messageType = "warning";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="upload.css">
    <style>
        .alert-popup {
            position: fixed;
            bottom: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 1050;
            display: none;
        }
    </style>
</head>

<body>    <div class="container mt-5">
        <h1>Upload Files</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="mb-3">
                <label for="file" class="form-label">Select file</label>
                <input type="file" class="form-control" name="file" id="file" required>
                <div class="form-text">Allowed file types: JPG, JPEG, PNG, GIF, PDF</div>
            </div>
            <button type="submit" class="btn btn-primary">Upload File</button>
        </form>
    </div>

    <!-- Alert popup for messages -->
    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-popup alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>