<?php
include('db_connect.php'); 


$sql = "SELECT COUNT(*) as total, 
                SUM(CASE WHEN ClockInTime <= '09:00:00' THEN 1 ELSE 0 END) as Present,
                SUM(CASE WHEN ClockInTime > '09:00:00' AND ClockInTime <= '09:30:00' THEN 1 ELSE 0 END) as Late,
                SUM(CASE WHEN ClockInTime > '09:30:00' THEN 1 ELSE 0 END) as Absent
        FROM attendancerecord";
$result = $conn->query($sql);
$data = $result->fetch_assoc();

$present = $data['Present'];
$late = $data['Late'];
$absent = $data['Absent'];

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
        canvas {
            width: 60% !important;  /* Set to 80% of the container width */
            height: auto !important; /* Auto height for proportional scaling */
            margin: 0 auto;
            display: block;
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
                    <a class="nav-link" href="login.html"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Reports & Analytics</h2>
                    <div>
                        <button class="btn btn-primary" onclick="printReport()">
                            <i class="fas fa-print me-2"></i>Print Report
                        </button>
                    </div>
                </div>

                <!-- Attendance Overview Chart -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Overview</h5>
                        <!-- Buttons to toggle the data shown on the chart -->
                        <div class="btn-group mb-4" role="group" aria-label="Attendance Filter">
                            <button type="button" class="btn btn-primary" id="presentBtn" onclick="toggleSelection('present')">Present</button>
                            <button type="button" class="btn btn-warning" id="lateBtn" onclick="toggleSelection('late')">Late</button>
                            <button type="button" class="btn btn-danger" id="absentBtn" onclick="toggleSelection('absent')">Absent</button>
                        </div>

                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var chartData = {
            present: <?php echo $present; ?>,
            late: <?php echo $late; ?>,
            absent: <?php echo $absent; ?>
        };

        var selectedData = {
            present: true,
            late: true,
            absent: true
        };

        var attendanceChart = new Chart(ctx, {
            type: 'pie',
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
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' attendance(s)';
                            }
                        }
                    }
                }
            }
        });

        function toggleSelection(category) {
            selectedData[category] = !selectedData[category];


            if (selectedData[category]) {
                document.getElementById(category + 'Btn').classList.add('active');
            } else {
                document.getElementById(category + 'Btn').classList.remove('active');
            }

            updateChart();
        }

        function updateChart() {
            let data = [];
            let labels = [];

            if (selectedData.present) {
                data.push(chartData.present);
                labels.push('Present');
            }
            if (selectedData.late) {
                data.push(chartData.late);
                labels.push('Late');
            }
            if (selectedData.absent) {
                data.push(chartData.absent);
                labels.push('Absent');
            }

            attendanceChart.data.labels = labels;
            attendanceChart.data.datasets[0].data = data;
            attendanceChart.update();
        }

        function printReport() {
            var canvas = document.getElementById('attendanceChart');
            var printWindow = window.open('', '', 'height=800,width=800');
            printWindow.document.write('<html><head><title>Print Report</title></head><body>');
            printWindow.document.write('<h2>Attendance Overview</h2>'); // Title or header
            printWindow.document.write('<img src="' + canvas.toDataURL() + '" />'); // Convert canvas to image and display
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
