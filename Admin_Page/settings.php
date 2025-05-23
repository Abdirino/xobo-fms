<?php
// Check if user is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        // Update settings logic here
        $_SESSION['message'] = 'Settings updated successfully!';
        $_SESSION['messageType'] = 'success';
    }
}

// Get settings from database or config
$settings = [
    'max_file_size' => ini_get('upload_max_filesize'),
    'allowed_extensions' => '.jpg, .jpeg, .png, .gif, .pdf',
    'storage_limit' => '100GB',
    'backup_frequency' => 'Daily',
    'retention_period' => '30 days'
];
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">System Settings</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['messageType']; ?> alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['messageType']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" class="row g-3">
                        <!-- File Upload Settings -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">File Upload Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Maximum File Size</label>
                                        <input type="text" class="form-control" value="<?php echo $settings['max_file_size']; ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Allowed File Extensions</label>
                                        <input type="text" class="form-control" value="<?php echo $settings['allowed_extensions']; ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Storage Limit</label>
                                        <input type="text" class="form-control" value="<?php echo $settings['storage_limit']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Backup Settings -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Backup Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Backup Frequency</label>
                                        <select class="form-select" name="backup_frequency">
                                            <option value="daily" <?php echo $settings['backup_frequency'] === 'Daily' ? 'selected' : ''; ?>>Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Retention Period</label>
                                        <select class="form-select" name="retention_period">
                                            <option value="7">7 days</option>
                                            <option value="30" selected>30 days</option>
                                            <option value="90">90 days</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Email Notifications</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="newUserNotif" checked>
                                                <label class="form-check-label" for="newUserNotif">New User Registration</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="fileUploadNotif" checked>
                                                <label class="form-check-label" for="fileUploadNotif">File Upload Notifications</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="storageAlertNotif" checked>
                                                <label class="form-check-label" for="storageAlertNotif">Storage Alerts</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="securityAlertNotif" checked>
                                                <label class="form-check-label" for="securityAlertNotif">Security Alerts</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" name="update_settings" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
