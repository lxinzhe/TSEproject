<?php
include('db_connect.php');

$records_per_page = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Employee Statistics
$sql = "SELECT COUNT(*) AS total_employees FROM employees";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
$total_employees = $data['total_employees'];

// Today's Statistics
$today = date('Y-m-d');

$sql = "SELECT COUNT(*) AS present_today FROM attendancerecord WHERE Date = '$today' AND ClockInTime IS NOT NULL";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
$present_today = $data['present_today'];

$sql = "SELECT COUNT(*) AS late_today FROM attendancerecord WHERE Date = '$today' AND ClockInTime > '09:00:00'";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
$late_today = $data['late_today'];

$sql = "SELECT COUNT(*) AS absent_today FROM attendancerecord WHERE Date = '$today' AND (ClockInTime IS NULL OR ClockOutTime IS NULL)";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
$absent_today = $data['absent_today'];

// Recent Attendance Records
$sql = "SELECT * FROM attendancerecord ORDER BY Date DESC LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Employee Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .nav-link {
            color: white;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #495057;
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .stats-card {
            border-radius: 15px;
            padding: 25px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border: none;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card h5 {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .stats-card h2 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
        }
        .stats-card i {
            font-size: 2.5rem;
            opacity: 0.5;
        }
        .bg-employees { background: linear-gradient(135deg, #007bff, #0056b3); }
        .bg-present { background: linear-gradient(135deg, #28a745, #20c997); }
        .bg-late { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .bg-absent { background: linear-gradient(135deg, #dc3545, #e83e8c); }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border-bottom: none;
            border-radius: 15px 15px 0 0 !important;
            font-weight: bold;
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .badge {
            font-size: 0.75rem;
            padding: 5px 10px;
        }
        .pagination .page-link {
            border-radius: 10px;
            margin: 0 2px;
            border: none;
            color: #495057;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
        }
        .header-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .stats-card h2 { font-size: 2rem; }
            .main-content { padding: 15px; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <h3 class="text-center mb-4">Admin Panel</h3>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="admin.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    <a class="nav-link" href="employees.php"><i class="fas fa-users me-2"></i>Employees</a>
                    <a class="nav-link" href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
                    <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-2"></i>Reports</a>
                    <a class="nav-link" href="cpd_management.php"><i class="fas fa-graduation-cap me-2"></i>Event Programs</a>
                    <a class="nav-link" href="cpd_records.php"><i class="fas fa-clipboard-list me-2"></i>Event Records</a>
                    <a class="nav-link" href="cpd_categories.php"><i class="fas fa-tags me-2"></i>Event Categories</a>
                    <a class="nav-link" href="admin_login.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header Section -->
                <div class="header-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back! Here's today's attendance overview.</p>
                        </div>
                        <div class="text-end">
                            <div class="text-muted">
                                <i class="fas fa-calendar me-1"></i><?= date('l, F j, Y') ?>
                            </div>
                            <div class="text-muted">
                                <i class="fas fa-clock me-1"></i><?= date('g:i A') ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card bg-employees">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5><i class="fas fa-users me-2"></i>Total Employees</h5>
                                    <h2><?= $total_employees ?></h2>
                                </div>
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card bg-present">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5><i class="fas fa-check-circle me-2"></i>Present Today</h5>
                                    <h2><?= $present_today ?></h2>
                                </div>
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card bg-late">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5><i class="fas fa-clock me-2"></i>Late Today</h5>
                                    <h2><?= $late_today ?></h2>
                                </div>
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card bg-absent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Absent Today</h5>
                                    <h2><?= $absent_today ?></h2>
                                </div>
                                <i class="fas fa-user-times"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Recent Attendance Records
                            </h5>
                            <span class="badge bg-light text-dark">Last <?= $records_per_page ?> records</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-id-badge me-1"></i>Employee ID</th>
                                            <th><i class="fas fa-user me-1"></i>Name</th>
                                            <th><i class="fas fa-calendar me-1"></i>Date</th>
                                            <th><i class="fas fa-sign-in-alt me-1"></i>Check In</th>
                                            <th><i class="fas fa-sign-out-alt me-1"></i>Check Out</th>
                                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): 
                                            // Determine status
                                            $status = 'Present';
                                            $status_class = 'success';
                                            
                                            if (empty($row["ClockInTime"]) || empty($row["ClockOutTime"])) {
                                                $status = 'Absent';
                                                $status_class = 'danger';
                                            } elseif (strtotime($row["ClockInTime"]) > strtotime('09:00:00')) {
                                                $status = 'Late';
                                                $status_class = 'warning';
                                            }
                                        ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($row["EmployeeID"]) ?></strong></td>
                                                <td><?= htmlspecialchars($row["Name"]) ?></td>
                                                <td><?= date('M j, Y', strtotime($row["Date"])) ?></td>
                                                <td>
                                                    <?php if ($row["ClockInTime"]): ?>
                                                        <span class="text-success">
                                                            <i class="fas fa-clock me-1"></i><?= $row["ClockInTime"] ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not clocked in</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($row["ClockOutTime"]): ?>
                                                        <span class="text-info">
                                                            <i class="fas fa-clock me-1"></i><?= $row["ClockOutTime"] ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not clocked out</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $status_class ?>">
                                                        <?= $status ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No attendance records found</h5>
                                <p class="text-muted">Attendance records will appear here when employees clock in.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php
                $sql = "SELECT COUNT(*) AS total_records FROM attendancerecord";
                $count_result = $conn->query($sql);
                $count_row = $count_result->fetch_assoc();
                $total_records = $count_row['total_records'];
                $total_pages = ceil($total_records / $records_per_page);

                if ($total_pages > 1):
                ?>
                    <nav aria-label="Attendance pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="admin.php?page=<?= $current_page - 1 ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                    <a class="page-link" href="admin.php?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="admin.php?page=<?= $current_page + 1 ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Showing page <?= $current_page ?> of <?= $total_pages ?> 
                            (<?= $total_records ?> total records)
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 5 minutes to keep data current
        setTimeout(function() {
            window.location.reload();
        }, 300000);
        
        // Add smooth hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stats-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>
