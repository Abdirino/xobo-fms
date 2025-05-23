<?php
// Get user details
$email = $_SESSION['email'];
$user_query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (password_verify($current_password, $user['password'])) {
            if (!empty($new_password)) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_query = "UPDATE users SET name = ?, password = ? WHERE email = ?";
                    $stmt = $conn->prepare($update_query);
                    $stmt->bind_param("sss", $name, $hashed_password, $email);
                } else {
                    $_SESSION['message'] = 'New passwords do not match!';
                    $_SESSION['messageType'] = 'danger';
                }
            } else {
                $update_query = "UPDATE users SET name = ? WHERE email = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("ss", $name, $email);
            }

            if (isset($stmt) && $stmt->execute()) {
                $_SESSION['name'] = $name;
                $_SESSION['message'] = 'Profile updated successfully!';
                $_SESSION['messageType'] = 'success';
            } else {
                $_SESSION['message'] = 'Error updating profile!';
                $_SESSION['messageType'] = 'danger';
            }
        } else {
            $_SESSION['message'] = 'Current password is incorrect!';
            $_SESSION['messageType'] = 'danger';
        }
    }
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Profile Settings</h5>
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

                    <div class="text-center mb-4">
                        <img src="../Images/profile-img.jpeg" alt="Profile" class="rounded-circle" width="150" height="150">
                        <h4 class="mt-3"><?php echo htmlspecialchars($user['name']); ?></h4>
                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'primary' : 'secondary'; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </div>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" name="new_password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password">
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

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
