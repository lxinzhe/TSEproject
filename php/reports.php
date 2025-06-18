<?php
include('db_connect.php'); 

// Get selected month and year from GET parameters
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Build date filter for SQL query
$date_filter = "";
if ($selected_month && $selected_year) {
    $date_filter = "WHERE MONTH(Date) = '$selected_month' AND YEAR(Date) = '$selected_year'";
}

// Get attendance statistics for the selected month
$sql = "SELECT COUNT(*) as total, 
                SUM(CASE WHEN ClockInTime <= '09:00:00' THEN 1 ELSE 0 END) as Present,
                SUM(CASE WHEN ClockInTime > '09:00:00' AND ClockInTime <= '09:30:00' THEN 1 ELSE 0 END) as Late,
                SUM(CASE WHEN ClockInTime > '09:30:00' OR ClockOutTime IS NULL THEN 1 ELSE 0 END) as Absent
        FROM attendancerecord $date_filter";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

$total = $data['total'];
$present = $data['Present'];
$late = $data['Late'];
$absent = $data['Absent'];

// Get daily attendance for the selected month (for line chart)
$daily_sql = "SELECT DATE(Date) as attendance_date, 
                     COUNT(*) as daily_total,
                     SUM(CASE WHEN ClockInTime <= '09:00:00' THEN 1 ELSE 0 END) as daily_present,
                     SUM(CASE WHEN ClockInTime > '09:00:00' AND ClockInTime <= '09:30:00' THEN 1 ELSE 0 END) as daily_late,
                     SUM(CASE WHEN ClockInTime > '09:30:00' OR ClockOutTime IS NULL THEN 1 ELSE 0 END) as daily_absent
              FROM attendancerecord $date_filter
              GROUP BY DATE(Date)
              ORDER BY DATE(Date)";
$daily_result = $conn->query($daily_sql);

$daily_data = array();
while ($row = $daily_result->fetch_assoc()) {
    $daily_data[] = $row;
}

// Get employee statistics for the selected month
$employee_sql = "SELECT Name, 
                        COUNT(*) as total_days,
                        SUM(CASE WHEN ClockInTime <= '09:00:00' THEN 1 ELSE 0 END) as present_days,
                        SUM(CASE WHEN ClockInTime > '09:00:00' AND ClockInTime <= '09:30:00' THEN 1 ELSE 0 END) as late_days,
                        SUM(CASE WHEN ClockInTime > '09:30:00' OR ClockOutTime IS NULL THEN 1 ELSE 0 END) as absent_days,
                        AVG(TotalWorkHours) as avg_work_hours,
                        SUM(OvertimeHours) as total_overtime
                 FROM attendancerecord $date_filter
                 GROUP BY Name, EmployeeID
                 ORDER BY total_days DESC";
$employee_result = $conn->query($employee_sql);

// Get total employees for attendance rate calculation
$total_employees_sql = "SELECT COUNT(DISTINCT EmployeeID) as total_employees FROM attendancerecord $date_filter";
$total_employees_result = $conn->query($total_employees_sql);
$total_employees = $total_employees_result->fetch_assoc()['total_employees'];

// Calculate attendance rate
$attendance_rate = $total > 0 ? (($present + $late) / $total) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .btn-group {
            margin-bottom: 20px;
        }
        .chart-container {
            position: relative;
            height: 400px;
            margin: 20px 0;
        }
        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            color: white;
            margin-bottom: 20px;
        }
        .stats-card h3 {
            margin: 0;
            font-size: 2.5em;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            font-size: 1.1em;
        }
        .bg-present { background: linear-gradient(135deg, #28a745, #20c997); }
        .bg-late { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .bg-absent { background: linear-gradient(135deg, #dc3545, #e83e8c); }
        .bg-rate { background: linear-gradient(135deg, #007bff, #6f42c1); }
        
        @media print {
            .sidebar, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; }
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
                    <a class="nav-link" href="admin.php"><i class="fas fa-home me-2"></i>Dashboard</a>
                    <a class="nav-link" href="employees.php"><i class="fas fa-users me-2"></i>Employees</a>
                    <a class="nav-link" href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
                    <a class="nav-link active" href="reports.php"><i class="fas fa-chart-bar me-2"></i>Reports</a>
                    <a class="nav-link" href="cpd_management.php"><i class="fas fa-graduation-cap me-2"></i>Event Programs</a>
                    <a class="nav-link" href="cpd_records.php"><i class="fas fa-clipboard-list me-2"></i>Event Records</a>
                    <a class="nav-link" href="cpd_categories.php"><i class="fas fa-tags me-2"></i>Event Categories</a>
                    <a class="nav-link" href="admin_login.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Reports & Analytics</h2>
                    <div class="no-print">
                        <button class="btn btn-success me-2" onclick="exportData()">
                            <i class="fas fa-file-excel me-2"></i>Export Data
                        </button>
                        <button class="btn btn-primary" onclick="printReport()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                    </div>
                </div>

                <!-- Month/Year Filter -->
                <div class="card mb-4 no-print">
                    <div class="card-body">
                        <h5 class="card-title">Filter by Month</h5>
                        <form method="GET" action="reports.php" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Month</label>
                                <select name="month" class="form-select">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" 
                                                <?= ($selected_month == str_pad($m, 2, '0', STR_PAD_LEFT)) ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Year</label>
                                <select name="year" class="form-select">
                                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                        <option value="<?= $y ?>" <?= ($selected_year == $y) ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                                    <a href="reports.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Display selected period -->
                <div class="alert alert-info">
                    <i class="fas fa-calendar me-2"></i>
                    Showing data for: <strong><?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?></strong>
                    <?php if ($total == 0): ?>
                        <br><small class="text-muted">No attendance records found for this period.</small>
                    <?php endif; ?>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card bg-present">
                            <h3><?= $present ?></h3>
                            <p>Present</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-late">
                            <h3><?= $late ?></h3>
                            <p>Late</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-absent">
                            <h3><?= $absent ?></h3>
                            <p>Absent</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-rate">
                            <h3><?= number_format($attendance_rate, 1) ?>%</h3>
                            <p>Attendance Rate</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row">
                    <!-- Pie Chart -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Attendance Overview</h5>
                                <?php if ($total > 0): ?>
                                    <div class="btn-group mb-3 no-print" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success active" id="presentBtn" onclick="toggleSelection('present')">Present</button>
                                        <button type="button" class="btn btn-sm btn-outline-warning active" id="lateBtn" onclick="toggleSelection('late')">Late</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger active" id="absentBtn" onclick="toggleSelection('absent')">Absent</button>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="attendanceChart"></canvas>
                                    </div>
                                <?php else: ?>
                                    <p class="text-center text-muted">No data available for the selected period.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Trend Chart -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Daily Attendance Trend</h5>
                                <?php if (!empty($daily_data)): ?>
                                    <div class="chart-container">
                                        <canvas id="dailyTrendChart"></canvas>
                                    </div>
                                <?php else: ?>
                                    <p class="text-center text-muted">No daily data available for the selected period.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Statistics Table -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Employee Statistics</h5>
                        <?php if ($employee_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Total Days</th>
                                            <th>Present</th>
                                            <th>Late</th>
                                            <th>Absent</th>
                                            <th>Avg Work Hours</th>
                                            <th>Total Overtime</th>
                                            <th>Attendance %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($emp = $employee_result->fetch_assoc()): 
                                            $emp_attendance_rate = $emp['total_days'] > 0 ? (($emp['present_days'] + $emp['late_days']) / $emp['total_days']) * 100 : 0;
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($emp['Name']) ?></td>
                                                <td><?= $emp['total_days'] ?></td>
                                                <td><span class="badge bg-success"><?= $emp['present_days'] ?></span></td>
                                                <td><span class="badge bg-warning"><?= $emp['late_days'] ?></span></td>
                                                <td><span class="badge bg-danger"><?= $emp['absent_days'] ?></span></td>
                                                <td><?= number_format($emp['avg_work_hours'], 2) ?>h</td>
                                                <td><?= number_format($emp['total_overtime'], 2) ?>h</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar <?= $emp_attendance_rate >= 90 ? 'bg-success' : ($emp_attendance_rate >= 80 ? 'bg-warning' : 'bg-danger') ?>" 
                                                             style="width: <?= $emp_attendance_rate ?>%">
                                                            <?= number_format($emp_attendance_rate, 1) ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">No employee data available for the selected period.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pie Chart Data
        var chartData = {
            present: <?= $present ?>,
            late: <?= $late ?>,
            absent: <?= $absent ?>
        };

        var selectedData = {
            present: true,
            late: true,
            absent: true
        };

        // Initialize Pie Chart
        <?php if ($total > 0): ?>
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var attendanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Late', 'Absent'],
                datasets: [{
                    data: [chartData.present, chartData.late, chartData.absent],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderColor: ['#ffffff', '#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                var total = chartData.present + chartData.late + chartData.absent;
                                var percentage = ((tooltipItem.raw / total) * 100).toFixed(1);
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Daily Trend Chart
        <?php if (!empty($daily_data)): ?>
        var dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
        var dailyData = <?= json_encode($daily_data) ?>;
        
        var dailyTrendChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(function(item) {
                    return new Date(item.attendance_date).toLocaleDateString();
                }),
                datasets: [{
                    label: 'Present',
                    data: dailyData.map(function(item) { return item.daily_present; }),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Late',
                    data: dailyData.map(function(item) { return item.daily_late; }),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Absent',
                    data: dailyData.map(function(item) { return item.daily_absent; }),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        function toggleSelection(category) {
            <?php if ($total > 0): ?>
            selectedData[category] = !selectedData[category];
            
            var btn = document.getElementById(category + 'Btn');
            if (selectedData[category]) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
            
            updateChart();
            <?php endif; ?>
        }

        function updateChart() {
            <?php if ($total > 0): ?>
            let data = [];
            let labels = [];
            let colors = [];
            
            if (selectedData.present) {
                data.push(chartData.present);
                labels.push('Present');
                colors.push('#28a745');
            }
            if (selectedData.late) {
                data.push(chartData.late);
                labels.push('Late');
                colors.push('#ffc107');
            }
            if (selectedData.absent) {
                data.push(chartData.absent);
                labels.push('Absent');
                colors.push('#dc3545');
            }
            
            attendanceChart.data.labels = labels;
            attendanceChart.data.datasets[0].data = data;
            attendanceChart.data.datasets[0].backgroundColor = colors;
            attendanceChart.update();
            <?php endif; ?>
        }

        function printReport() {
            window.print();
        }

        function exportData() {
            var month = '<?= $selected_month ?>';
            var year = '<?= $selected_year ?>';
            window.location.href = 'attendance.php?export=true&start_date=' + year + '-' + month + '-01&end_date=' + year + '-' + month + '-31';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
