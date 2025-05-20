<?php

include('db.php');

$records_per_page = 10;

$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

$offset = ($current_page - 1) * $records_per_page;

$sql = "SELECT COUNT(*) AS total_employees FROM employees";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
$total_employees = $data['total_employees'];

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

$sql = "SELECT * FROM attendancerecord ORDER BY Date DESC LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance System - Admin Dashboard</title>
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
        }
        .nav-link:hover {
            background-color: #495057;
            color: white;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
                    <a class="nav-link" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a>
                    <a class="nav-link" href="login.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4">Dashboard</h2>
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Employees</h5>
                                <h2 class="card-text"><?php echo $total_employees; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Present Today</h5>
                                <h2 class="card-text"><?php echo $present_today; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Late Today</h5>
                                <h2 class="card-text"><?php echo $late_today; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Absent Today</h5>
                                <h2 class="card-text"><?php echo $absent_today; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance Table -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Attendance</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    while ($row = $result->fetch_assoc()) {

                                        $status = 'Present';

                                        if (strtotime($row["ClockInTime"]) > strtotime('09:00:00')) {
                                            $status = 'Late';
                                        }

                                        if (empty($row["ClockInTime"]) || empty($row["ClockOutTime"])) {
                                            $status = 'Absent';
                                        }

                                        echo "<tr>";
                                        echo "<td>" . $row["EmployeeID"] . "</td>";
                                        echo "<td>" . $row["Name"] . "</td>";
                                        echo "<td>" . $row["Date"] . "</td>";
                                        echo "<td>" . $row["ClockInTime"] . "</td>";
                                        echo "<td>" . $row["ClockOutTime"] . "</td>";
                                        echo "<td><span class='badge bg-" . ($status == 'Present' ? "success" : ($status == 'Late' ? "warning" : "danger")) . "'>" . $status . "</span></td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php
                $sql = "SELECT COUNT(*) AS total_records FROM attendancerecord";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $total_records = $row['total_records'];

                $total_pages = ceil($total_records / $records_per_page);

                echo '<nav aria-label="Page navigation">';
                echo '<ul class="pagination">';

                if ($current_page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="admin.php?page=' . ($current_page - 1) . '">Previous</a></li>';
                }

                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '"><a class="page-link" href="admin.php?page=' . $i . '">' . $i . '</a></li>';
                }

                if ($current_page < $total_pages) {
                    echo '<li class="page-item"><a class="page-link" href="admin.php?page=' . ($current_page + 1) . '">Next</a></li>';
                }

                echo '</ul>';
                echo '</nav>';
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
