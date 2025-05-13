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
    <link rel="stylesheet" href="style.css">
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
                    <a href="" class="sidebar-link">
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class='bx bx-file'></i> 
                        <span>My Files</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class='bx bxs-file-import'></i>
                        <span>Upload File</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class='bx bx-share'></i> 
                        <span>Share with me</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class='bx bx-search-alt'></i>
                        <span>Search Files</span>
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
                        <i class='bx bx-history'></i>
                        <span>File History</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="" class="sidebar-link">
                        <i class='bx bxs-user-account'></i>
                        <span>Profile</span>
                    </a>
                </li>

            </ul>
            <div class="sidebar-footer">
                <a href="../logout.php" class="sidebar-link">
                    <i class='bx bx-log-out'></i>
                    <span>Logout</span>
                </a>
            </div>

        </aside>
        <div class="main">
            <div class="navbar navbar-expand px-4 py-3">
                <form action="#" class="d-none d-sm-inline-block">
                    <div class="input-group input-group-navbar">
                        <input type="text" class="form-control border-0 rounded-0 pe-0" placeholder="Search..."
                            aria-label="Search">
                        <button class="btn- border-0 rounded-0" type="button">
                            <i class='bx bx-search-alt'></i>
                        </button>
                    </div>
                </form>
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown"></li>
                    </ul>
                </div>
            </div>

            <div class="box">
                <h1>Welcome, <span><?= $_SESSION['name']; ?></span></h1>
                <p>This is the user page</p>
            </div>
            
            <div class="col-12 col-md-5">
                <h3 class="fw-bold fs-4 my-3">
                    Report Overview
                </h3>
                <canvas id="bar-chart-grouped" width="800" height="450"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="script.js"></script>
</body>

</html>