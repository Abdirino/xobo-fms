<?php

session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="website" type="png" href="Xobo-Logo.jpeg">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="user.css">
</head>

<body>

    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex justify-content-between p-4">
                <div class="sidebar-logo">
                    <a href="#">XoboFMS</a>
                </div>
                <button class="toggle-btn border-0" type="button">
                    <i id="icon" class='bx bxs-chevrons-right'></i>
                </button>
            </div>
            <ul class="sidebar-nav">

                <li class="sidebar-item">
                    <a href="user.php" class="sidebar-link">
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="user.php?files_repository" class="sidebar-link">
                        <i class='bx bxs-hdd'></i>
                        <span>Files Repository</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="user.php?upload" class="sidebar-link">
                        <i class='bx bxs-file-import'></i>
                        <span>Upload File</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="user.php?audit_logs" class="sidebar-link">
                        <i class='bx bxl-blogger'></i>
                        <span>Audit Logs</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class='bx bxs-report'></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class='bx bxs-cog'></i>
                        <span>Setting</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class='bx bxs-user-account'></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li class="sidebar-item sidebar-footer">
                    <a href="../logout.php" class="sidebar-link">
                        <i class='bx bx-log-out'></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>

        </aside>
        <div class="main">
            <nav class="navbar navbar-expand px-4 py-3">
                <form action="#" class="d-none d-sm-inline-block">
                    <div class="input-group input-group-navbar">
                        <input type="text" class="form-control border-0 rounded-0 pe-0" placeholder="Search..."
                            aria-label="Search">
                        <button class="btn border-0 rounded-0" type="button">
                            <i class='bx bx-search-alt'></i>
                        </button>
                    </div>
                </form>
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-icon pe-md-0">
                                <span><?= $_SESSION['name']; ?></span>
                                <img src="../Images/profile-img.jpeg" class="avatar img-fluid" alt="">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end rounded-0 border-0 shadow mt-3">
                                <a href="#" class="dropdown-item">
                                    <i class="bx bx-data"></i>
                                    <span>Analytics</span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="bx bx-cog"></i>
                                    <span>Settings</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item">
                                    <i class="bx bx-help-circle"></i>
                                    <span>Help Center</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content px-3 py-4">
                <div class="container-fluid">
                    <?php
                    if (isset($_GET['upload'])) {
                        include('../Upload/upload.php');
                    } elseif (isset($_GET['manage_users'])) {
                        include('manage_users.php');                    } elseif (isset($_GET['files_repository'])) {
                        include('../Files/files.php');
                    } elseif (isset($_GET['audit_logs'])) {
                        include('audit_logs.php');
                    } elseif (isset($_GET['reports'])) {
                        include('reports.php');
                    } elseif (isset($_GET['settings'])) {
                        include('settings.php');
                    } elseif (isset($_GET['profile'])) {
                        include('profile.php');
                    } else {
                        ?>
                    <div class="mb-3">
                        <h1 class="mb-2">Welcome, <span><?= $_SESSION['name']; ?></span></h1>
                        <h3 class="fw-bold fs-4 mb-3">
                            User Dashboard
                        </h3>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="card shadow">
                                    <div class="card-body py-4">
                                        <h6 class="mb-2 fw-bold">
                                            Member Progress
                                        </h6>
                                        <p class="fw-bold">
                                            $89,189
                                        </p>
                                        <div class="mb-0">
                                            <span class="badge text-success me-2">
                                                +9.0%
                                            </span>
                                            <span class="fw-bold">
                                                Since Last Month
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card shadow">
                                    <div class="card-body py-4">
                                        <h6 class="mb-2 fw-bold">
                                            Member Progress
                                        </h6>
                                        <p class="fw-bold">
                                            $89,189
                                        </p>
                                        <div class="mb-0">
                                            <span class="badge text-success me-2">
                                                +9.0%
                                            </span>
                                            <span class="fw-bold">
                                                Since Last Month
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="card shadow">
                                    <div class="card-body py-4">
                                        <h6 class="mb-2 fw-bold">
                                            Member Progress
                                        </h6>
                                        <p class="fw-bold">
                                            $89,189
                                        </p>
                                        <div class="mb-0">
                                            <span class="badge text-success me-2">
                                                +9.0%
                                            </span>
                                            <span class="fw-bold">
                                                Since Last Month
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php } ?>
                </div>
            </main>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-body-secondary text-primary">
                        <div class="col-6 text-start">
                            <a href="#" class="text-body-secondary text-primary">
                                <strong>XoboFMS</strong>
                            </a>
                            <p>Beta Version 1.0</p>
                        </div>
                        <div class="col-6 text-end text-body-secondary d-none d-md-block">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="#" class="text-body-secondary">© 2025 XOBO</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-body-secondary">System Status</a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="text-body-secondary">Privacy Policy</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
                crossorigin="anonymous"></script>
            <script src="user.js"></script>
</body>

</html>