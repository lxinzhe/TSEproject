<?php
include('db_connect.php');

// Handle Delete CPD Record
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete the CPD record from the database
    $sql = "DELETE FROM cpd_record WHERE RecordId = $id";
    if ($conn->query($sql) === TRUE) {
        header('Location: cpd_records.php');
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Handle Add CPD Record
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_cpd_record'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $programme_name = mysqli_real_escape_string($conn, $_POST['programme_name']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $clock_in_time = mysqli_real_escape_string($conn, $_POST['clock_in_time']);
    $clock_out_time = mysqli_real_escape_string($conn, $_POST['clock_out_time']);

    // Get employee name based on employee_id
    $name_sql = "SELECT name FROM employees WHERE employee_id = '$employee_id'";
    $name_result = $conn->query($name_sql);
    $employee_name = "";
    if ($name_result->num_rows > 0) {
        $name_row = $name_result->fetch_assoc();
        $employee_name = $name_row['name'];
    }

    // Insert new CPD record into the database
    $sql = "INSERT INTO cpd_record (Employee_ID, Name, Date, programme_name, ClockInTime, ClockOutTime) 
            VALUES ('$employee_id', '$employee_name', '$date', '$programme_name', '$clock_in_time', '$clock_out_time')";
    
    if ($conn->query($sql) === TRUE) {
        $success = "CPD Record added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle Edit CPD Record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_cpd_record'])) {
    if (isset($_POST['record_id'])) {
        $record_id = $_POST['record_id'];
        $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
        $programme_name = mysqli_real_escape_string($conn, $_POST['programme_name']);
        $date = mysqli_real_escape_string($conn, $_POST['date']);
        $clock_in_time = mysqli_real_escape_string($conn, $_POST['clock_in_time']);
        $clock_out_time = mysqli_real_escape_string($conn, $_POST['clock_out_time']);

        // Get employee name based on employee_id
        $name_sql = "SELECT name FROM employees WHERE employee_id = '$employee_id'";
        $name_result = $conn->query($name_sql);
        $employee_name = "";
        if ($name_result->num_rows > 0) {
            $name_row = $name_result->fetch_assoc();
            $employee_name = $name_row['name'];
        }

        // Update CPD record in the database
        $sql = "UPDATE cpd_record SET 
                Employee_ID='$employee_id', 
                Name='$employee_name', 
                Date='$date', 
                programme_name='$programme_name', 
                ClockInTime='$clock_in_time', 
                ClockOutTime='$clock_out_time' 
                WHERE RecordId = $record_id";
        
        if ($conn->query($sql) === TRUE) {
            $success = "CPD Record updated successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "No record selected for editing.";
    }
}

// Fetch CPD records from the database
$sql = "SELECT * FROM cpd_record ORDER BY Date DESC";
$result = $conn->query($sql);

// Fetch employees for dropdown
$employees_sql = "SELECT employee_id, name FROM employees";
$employees_result = $conn->query($employees_sql);

// Fetch CPD programs for dropdown
$programs_sql = "SELECT programme_name FROM cpd_program";
$programs_result = $conn->query($programs_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPD Records Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
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
                    <a class="nav-link active" href="employees.php"><i class="fas fa-users me-2"></i>Employees</a>
                    <a class="nav-link" href="attendance.php"><i class="fas fa-calendar-check me-2"></i>Attendance</a>
                    <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-2"></i>Reports</a>
                    <a class="nav-link" href="cpd_management.php"><i class="fas fa-graduation-cap me-2"></i>CPD Programs</a>
                    <a class="nav-link" href="cpd_records.php"><i class="fas fa-clipboard-list me-2"></i>CPD Records</a>
                    <a class="nav-link" href="login.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="container mt-4">
                    <h2>Manage CPD Records</h2>

                    <!-- Success/Error Messages -->
                    <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <!-- Add New CPD Record Button -->
                    <div class="mb-3 mt-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCPDRecordModal">Add New CPD Record</button>
                    </div>

                    <!-- CPD Records Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Record ID</th>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Program Name</th>
                                    <th>Date</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['RecordId']; ?></td>
                                        <td><?= $row['Employee_ID']; ?></td>
                                        <td><?= $row['Name']; ?></td>
                                        <td><?= htmlspecialchars($row['programme_name']); ?></td>
                                        <td><?= $row['Date']; ?></td>
                                        <td><?= $row['ClockInTime']; ?></td>
                                        <td><?= $row['ClockOutTime'] ?: 'N/A'; ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCPDRecordModal" 
                                                onclick="populateEditModal(<?= $row['RecordId']; ?>, '<?= $row['Employee_ID']; ?>', '<?= htmlspecialchars($row['programme_name'], ENT_QUOTES); ?>', '<?= $row['Date']; ?>', '<?= $row['ClockInTime']; ?>', '<?= $row['ClockOutTime']; ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="cpd_records.php?delete=<?= $row['RecordId']; ?>" class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Are you sure you want to delete this CPD record?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding CPD Record -->
    <div class="modal fade" id="addCPDRecordModal" tabindex="-1" aria-labelledby="addCPDRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCPDRecordModalLabel">Add New CPD Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="cpd_records.php">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">Employee</label>
                                    <select name="employee_id" class="form-select" required>
                                        <option value="">Select Employee</option>
                                        <?php 
                                        $employees_result->data_seek(0); // Reset pointer
                                        while ($emp = $employees_result->fetch_assoc()): ?>
                                            <option value="<?= $emp['employee_id']; ?>"><?= $emp['employee_id']; ?> - <?= $emp['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="programme_name" class="form-label">CPD Program</label>
                                    <select name="programme_name" class="form-select" required>
                                        <option value="">Select Program</option>
                                        <?php 
                                        $programs_result->data_seek(0); // Reset pointer
                                        while ($prog = $programs_result->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($prog['programme_name']); ?>"><?= htmlspecialchars($prog['programme_name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="clock_in_time" class="form-label">Clock In Time</label>
                                    <input type="time" name="clock_in_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="clock_out_time" class="form-label">Clock Out Time</label>
                                    <input type="time" name="clock_out_time" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_cpd_record" class="btn btn-primary">Add Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Editing CPD Record -->
    <div class="modal fade" id="editCPDRecordModal" tabindex="-1" aria-labelledby="editCPDRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCPDRecordModalLabel">Edit CPD Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="cpd_records.php" id="editCPDRecordForm">
                    <div class="modal-body">
                        <!-- Hidden input for record_id -->
                        <input type="hidden" name="record_id" id="edit_record_id" value="">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_employee_id" class="form-label">Employee</label>
                                    <select name="employee_id" id="edit_employee_id" class="form-select" required>
                                        <option value="">Select Employee</option>
                                        <?php 
                                        $employees_result->data_seek(0); // Reset pointer
                                        while ($emp = $employees_result->fetch_assoc()): ?>
                                            <option value="<?= $emp['employee_id']; ?>"><?= $emp['employee_id']; ?> - <?= $emp['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_programme_name" class="form-label">CPD Program</label>
                                    <select name="programme_name" id="edit_programme_name" class="form-select" required>
                                        <option value="">Select Program</option>
                                        <?php 
                                        $programs_result->data_seek(0); // Reset pointer
                                        while ($prog = $programs_result->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($prog['programme_name']); ?>"><?= htmlspecialchars($prog['programme_name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
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
                                    <label for="edit_clock_in_time" class="form-label">Clock In Time</label>
                                    <input type="time" name="clock_in_time" id="edit_clock_in_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_clock_out_time" class="form-label">Clock Out Time</label>
                                    <input type="time" name="clock_out_time" id="edit_clock_out_time" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_cpd_record" class="btn btn-primary">Update Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function populateEditModal(recordId, employeeId, programmeName, date, clockInTime, clockOutTime) {
        // Set the values to the modal form fields
        document.getElementById('edit_record_id').value = recordId;
        document.getElementById('edit_employee_id').value = employeeId;
        document.getElementById('edit_programme_name').value = programmeName;
        document.getElementById('edit_date').value = date;
        document.getElementById('edit_clock_in_time').value = clockInTime;
        document.getElementById('edit_clock_out_time').value = clockOutTime;
        
        console.log('Edit modal populated with Record ID:', recordId); // Debug line
    }
    </script>
</body>
</html>