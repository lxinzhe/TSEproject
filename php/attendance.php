<?php
include('db_connect.php');

$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Initialize filter variables
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$employee_filter = isset($_GET['employee_id']) ? $_GET['employee_id'] : '';

// Handle Delete
if (isset($_GET['delete'])) {
    $record_id = (int)$_GET['delete'];
    $sql = "DELETE FROM attendancerecord WHERE RecordID = $record_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: attendance.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle Manual Entry
if (isset($_POST['submit_entry'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $clockin_time = mysqli_real_escape_string($conn, $_POST['clockin_time']);
    $clockout_time = mysqli_real_escape_string($conn, $_POST['clockout_time']);
    
    // Calculate total work hours
    $total_hours = 0;
    $overtime_hours = 0;
    
    if ($clockin_time && $clockout_time) {
        $checkin = new DateTime($clockin_time);
        $checkout = new DateTime($clockout_time);
        $interval = $checkin->diff($checkout);
        $total_hours = $interval->h + ($interval->i / 60);
        
        // Calculate overtime (assuming 8 hours is standard work day)
        if ($total_hours > 8) {
            $overtime_hours = $total_hours - 8;
        }
    }

    $sql = "INSERT INTO attendancerecord (EmployeeID, Name, Date, ClockInTime, ClockOutTime, TotalWorkHours, OvertimeHours) 
            VALUES ('$employee_id', '$name', '$date', '$clockin_time', '$clockout_time', '$total_hours', '$overtime_hours')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: attendance.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle Update
if (isset($_POST['update'])) {
    $record_id = (int)$_POST['record_id'];
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $clockin_time = mysqli_real_escape_string($conn, $_POST['clockin_time']);
    $clockout_time = mysqli_real_escape_string($conn, $_POST['clockout_time']);
    
    // Calculate total work hours
    $total_hours = 0;
    $overtime_hours = 0;
    
    if ($clockin_time && $clockout_time) {
        $checkin = new DateTime($clockin_time);
        $checkout = new DateTime($clockout_time);
        $interval = $checkin->diff($checkout);
        $total_hours = $interval->h + ($interval->i / 60);
        
        // Calculate overtime
        if ($total_hours > 8) {
            $overtime_hours = $total_hours - 8;
        }
    }

    $sql = "UPDATE attendancerecord SET 
            EmployeeID='$employee_id', 
            Name='$name', 
            Date='$date', 
            ClockInTime='$clockin_time', 
            ClockOutTime='$clockout_time',
            TotalWorkHours='$total_hours',
            OvertimeHours='$overtime_hours'
            WHERE RecordID=$record_id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: attendance.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Build the main SQL query with filters
$where_conditions = array();
$params = array();

if ($start_date) {
    $where_conditions[] = "Date >= ?";
    $params[] = $start_date;
}

if ($end_date) {
    $where_conditions[] = "Date <= ?";
    $params[] = $end_date;
}

if ($employee_filter) {
    $where_conditions[] = "EmployeeID = ?";
    $params[] = $employee_filter;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = ' WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM attendancerecord" . $where_clause;
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get the actual records
$sql = "SELECT * FROM attendancerecord" . $where_clause . " ORDER BY Date DESC, ClockInTime DESC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params) - 2) . 'ii'; // Last two are integers (limit, offset)
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Handle Export
if (isset($_GET['export'])) {
    $filename = "attendance_report_" . date('Y-m-d') . ".csv";
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Employee ID', 'Name', 'Date', 'Check In', 'Check Out', 'Total Hours', 'Overtime Hours', 'Status']);
    
    // Re-run query for export
    $export_stmt = $conn->prepare("SELECT * FROM attendancerecord" . $where_clause . " ORDER BY Date DESC");
    if (!empty($where_conditions)) {
        $export_params = array_slice($params, 0, count($where_conditions));
        $types = str_repeat('s', count($export_params));
        $export_stmt->bind_param($types, ...$export_params);
    }
    $export_stmt->execute();
    $export_result = $export_stmt->get_result();
    
    while ($row = $export_result->fetch_assoc()) {
        $status = 'Present';
        if (empty($row["ClockOutTime"])) {
            $status = 'Absent';
        } elseif (strtotime($row["ClockInTime"]) > strtotime('09:00:00')) {
            $status = 'Late';
        }
        
        fputcsv($output, [
            $row['EmployeeID'],
            $row['Name'],
            $row['Date'],
            $row['ClockInTime'],
            $row['ClockOutTime'],
            $row['TotalWorkHours'],
            $row['OvertimeHours'],
            $status
        ]);
    }
    
    fclose($output);
    exit();
}

// Fetch employees for dropdown
$employee_sql = "SELECT employee_id, name FROM employees ORDER BY name";
$employee_result = $conn->query($employee_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        .table th, .table td {
            text-align: center;
        }
        .table .btn {
            margin: 2px;
        }
        .right-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
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
                    <a class="nav-link active" href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
                    <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-2"></i>Reports</a>
                    <a class="nav-link" href="cpd_management.php"><i class="fas fa-graduation-cap me-2"></i>CPD Programs</a>
                    <a class="nav-link" href="cpd_records.php"><i class="fas fa-clipboard-list me-2"></i>CPD Records</a>
                    <a class="nav-link" href="login.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4">Attendance Management</h2>

                <!-- Buttons (Export and Manual Entry) -->
                <div class="right-buttons">
                    <a href="attendance.php?export=true&<?= http_build_query($_GET) ?>" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i> Export to Excel
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#manualEntryModal">
                        <i class="fas fa-plus me-2"></i> Manual Entry
                    </button>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form class="row g-3" method="GET" action="attendance.php">
                            <div class="col-md-2">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" 
                                    value="<?= htmlspecialchars($start_date) ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" 
                                    value="<?= htmlspecialchars($end_date) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Employee</label>
                                <select name="employee_id" class="form-select">
                                    <option value="">All Employees</option>
                                    <?php 
                                    $employee_result->data_seek(0);
                                    while ($emp = $employee_result->fetch_assoc()): ?>
                                        <option value="<?= $emp['employee_id'] ?>" 
                                            <?= ($employee_filter == $emp['employee_id']) ? 'selected' : '' ?>>
                                            <?= $emp['employee_id'] ?> - <?= htmlspecialchars($emp['name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Present" <?= ($status_filter == 'Present') ? 'selected' : '' ?>>Present</option>
                                    <option value="Late" <?= ($status_filter == 'Late') ? 'selected' : '' ?>>Late</option>
                                    <option value="Absent" <?= ($status_filter == 'Absent') ? 'selected' : '' ?>>Absent</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                    <a href="attendance.php" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Total Hours</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $filtered_records = array();
                                    while ($row = $result->fetch_assoc()) {
                                        // Calculate status
                                        $status = 'Present';
                                        if (empty($row["ClockOutTime"])) {
                                            $status = 'Absent';
                                        } elseif (strtotime($row["ClockInTime"]) > strtotime('09:00:00')) {
                                            $status = 'Late';
                                        }
                                        
                                        // Apply status filter if set
                                        if ($status_filter && $status != $status_filter) {
                                            continue;
                                        }
                                        
                                        $filtered_records[] = $row;
                                        
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["EmployeeID"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["Date"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["ClockInTime"]) . "</td>";
                                        echo "<td>" . ($row["ClockOutTime"] ? htmlspecialchars($row["ClockOutTime"]) : "N/A") . "</td>";
                                        echo "<td>" . number_format($row["TotalWorkHours"], 2) . "</td>";
                                        echo "<td><span class='badge bg-" . ($status == 'Present' ? "success" : ($status == 'Late' ? "warning" : "danger")) . "'>" . $status . "</span></td>";
                                        echo "<td>
                                            <button class='btn btn-sm btn-info' data-bs-toggle='modal' data-bs-target='#editModal' 
                                                onclick=\"populateEditModal(" . $row['RecordID'] . ", '" . $row['EmployeeID'] . "', '" . htmlspecialchars($row['Name'], ENT_QUOTES) . "', '" . $row['Date'] . "', '" . $row['ClockInTime'] . "', '" . $row['ClockOutTime'] . "')\">
                                                <i class='fas fa-edit'></i>
                                            </button>
                                            <a href='attendance.php?delete=" . $row['RecordID'] . "' class='btn btn-sm btn-danger' 
                                               onclick=\"return confirm('Are you sure you want to delete this record?')\">
                                                <i class='fas fa-trash'></i> 
                                            </a>
                                        </td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="attendance.php?page=<?= $current_page - 1 ?>&<?= http_build_query($_GET) ?>">Previous</a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                            <a class="page-link" href="attendance.php?page=<?= $i ?>&<?= http_build_query($_GET) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="attendance.php?page=<?= $current_page + 1 ?>&<?= http_build_query($_GET) ?>">Next</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div class="modal fade" id="manualEntryModal" tabindex="-1" aria-labelledby="manualEntryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualEntryModalLabel">Manual Attendance Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="attendance.php">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee</label>
                                    <select name="employee_id" id="employee_id" class="form-select" required onchange="updateEmployeeName()">
                                        <option value="">Select Employee</option>
                                        <?php 
                                        $employee_result->data_seek(0);
                                        while ($emp = $employee_result->fetch_assoc()): ?>
                                            <option value="<?= $emp['employee_id'] ?>" data-name="<?= htmlspecialchars($emp['name']) ?>">
                                                <?= $emp['employee_id'] ?> - <?= htmlspecialchars($emp['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Employee Name</label>
                                    <input type="text" name="name" id="name" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="clockin_time" class="form-label">Clock In Time</label>
                                    <input type="time" name="clockin_time" id="clockin_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="clockout_time" class="form-label">Clock Out Time</label>
                                    <input type="time" name="clockout_time" id="clockout_time" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit_entry" class="btn btn-primary">Add Entry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Attendance Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="attendance.php">
                    <div class="modal-body">
                        <input type="hidden" name="record_id" id="edit_record_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_employee_id" class="form-label">Employee</label>
                                    <select name="employee_id" id="edit_employee_id" class="form-select" required onchange="updateEditEmployeeName()">
                                        <option value="">Select Employee</option>
                                        <?php 
                                        $employee_result->data_seek(0);
                                        while ($emp = $employee_result->fetch_assoc()): ?>
                                            <option value="<?= $emp['employee_id'] ?>" data-name="<?= htmlspecialchars($emp['name']) ?>">
                                                <?= $emp['employee_id'] ?> - <?= htmlspecialchars($emp['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label">Employee Name</label>
                                    <input type="text" name="name" id="edit_name" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_date" class="form-label">Date</label>
                                    <input type="date" name="date" id="edit_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_clockin_time" class="form-label">Clock In Time</label>
                                    <input type="time" name="clockin_time" id="edit_clockin_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_clockout_time" class="form-label">Clock Out Time</label>
                                    <input type="time" name="clockout_time" id="edit_clockout_time" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update" class="btn btn-primary">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function updateEmployeeName() {
        const select = document.getElementById('employee_id');
        const nameInput = document.getElementById('name');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.hasAttribute('data-name')) {
            nameInput.value = selectedOption.getAttribute('data-name');
        } else {
            nameInput.value = '';
        }
    }

    function updateEditEmployeeName() {
        const select = document.getElementById('edit_employee_id');
        const nameInput = document.getElementById('edit_name');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.hasAttribute('data-name')) {
            nameInput.value = selectedOption.getAttribute('data-name');
        } else {
            nameInput.value = '';
        }
    }

    function populateEditModal(recordId, employeeId, name, date, clockIn, clockOut) {
        document.getElementById('edit_record_id').value = recordId;
        document.getElementById('edit_employee_id').value = employeeId;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_date').value = date;
        document.getElementById('edit_clockin_time').value = clockIn;
        document.getElementById('edit_clockout_time').value = clockOut;
    }

    // Set today's date as default for new entries
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').value = today;
    });
    </script>
</body>
</html>
