<?php
// Check if user is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get date range from request
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Get statistics
$stats = [
    'total_uploads' => $conn->query("SELECT COUNT(*) as count FROM upload WHERE created_at BETWEEN '$startDate' AND '$endDate'")->fetch_assoc()['count'],
    'total_users' => $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'],
    'active_users' => $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'")->fetch_assoc()['count']
];

// Get uploads by category
$categoryStats = $conn->query("
    SELECT category, COUNT(*) as count 
    FROM upload 
    WHERE created_at BETWEEN '$startDate' AND '$endDate'
    GROUP BY category
");

// Get uploads by month
$monthlyStats = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
    FROM upload 
    GROUP BY month 
    ORDER BY month DESC 
    LIMIT 12
");
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">System Reports</h5>
                    <form class="d-flex gap-2">
                        <input type="hidden" name="reports" value="1">
                        <input type="date" name="start_date" value="<?php echo $startDate; ?>" class="form-control form-control-sm">
                        <input type="date" name="end_date" value="<?php echo $endDate; ?>" class="form-control form-control-sm">
                        <button type="submit" class="btn btn-sm btn-light">Filter</button>
                    </form>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Uploads</h6>
                                    <h2 class="mb-0"><?php echo number_format($stats['total_uploads']); ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Users</h6>
                                    <h2 class="mb-0"><?php echo number_format($stats['total_users']); ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Active Users</h6>
                                    <h2 class="mb-0"><?php echo number_format($stats['active_users']); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Distribution -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h6 class="mb-0">Uploads by Category</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h6 class="mb-0">Monthly Upload Trend</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Category Chart
new Chart(document.getElementById('categoryChart'), {
    type: 'pie',
    data: {
        labels: [<?php 
            $labels = [];
            $data = [];
            mysqli_data_seek($categoryStats, 0);
            while($row = $categoryStats->fetch_assoc()) {
                $labels[] = "'" . ucwords(str_replace('_', ' ', $row['category'])) . "'";
                $data[] = $row['count'];
            }
            echo implode(',', $labels);
        ?>],
        datasets: [{
            data: [<?php echo implode(',', $data); ?>],
            backgroundColor: ['#3e95cd', '#8e5ea2', '#3cba9f', '#e8c3b9', '#c45850']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Monthly Chart
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: [<?php 
            $labels = [];
            $data = [];
            mysqli_data_seek($monthlyStats, 0);
            while($row = $monthlyStats->fetch_assoc()) {
                $labels[] = "'" . date('M Y', strtotime($row['month'] . '-01')) . "'";
                $data[] = $row['count'];
            }
            echo implode(',', array_reverse($labels));
        ?>],
        datasets: [{
            label: 'Uploads',
            data: [<?php echo implode(',', array_reverse($data)); ?>],
            borderColor: '#3e95cd',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
