<?php

include('db.php');

$records_per_page = 10;

$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

$offset = ($current_page - 1) * $records_per_page;

$sql = "SELECT * FROM attendancerecord ORDER BY Date DESC LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($sql);

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT * FROM attendancerecord WHERE 1";

if ($start_date) {
    $sql .= " AND Date >= '$start_date'";
}

$sql .= " ORDER BY Date DESC LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($sql);
）
$employee_sql = "SELECT employee_id, name FROM employees";
$employee_result = $conn->query($employee_sql);


if (isset($_GET['delete'])) {
    $record_id = $_GET['delete'];

    $sql = "DELETE FROM attendancerecord WHERE RecordID = $record_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: attendance.php"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}


if (isset($_POST['submit_entry'])) {
    $employee_id = $_POST['employee_id'];  
    $name = $_POST['name'];
    $date = $_POST['date']; 
    $clockin_time = $_POST['clockin_time'];
    $clockout_time = $_POST['clockout_time'];

    $sql = "INSERT INTO attendancerecord (EmployeeID, Name, Date, ClockInTime, ClockOutTime) 
            VALUES ('$employee_id', '$name', '$date', '$clockin_time', '$clockout_time')";
    if ($conn->query($sql) === TRUE) {
        header("Location: attendance.php"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['update'])) {
    $record_id = $_POST['record_id'];
    $employee_id = $_POST['employee_id'];  
    $name = $_POST['name'];
    $date = $_POST['date']; 
    $clockin_time = $_POST['clockin_time'];
    $clockout_time = $_POST['clockout_time'];

 
    $sql = "UPDATE attendancerecord SET EmployeeID='$employee_id', Name='$name', Date='$date', ClockInTime='$clockin_time', ClockOutTime='$clockout_time' WHERE RecordID=$record_id";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
        header("Location: attendance.php"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

   
    if (isset($_GET['export'])) {
    
        $filename = "attendance_report_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');

        fputcsv($output, ['Employee ID', 'Name', 'Date', 'Check In', 'Check Out', 'Status']);

        while ($row = $result->fetch_assoc()) {
            $status = 'Present';

            if (strtotime($row["ClockInTime"]) > strtotime('09:00:00')) {
                $status = 'Late';
            }

            if (empty($row["ClockOutTime"])) {
                $status = 'Absent';
            }

            fputcsv($output, [
                $row['EmployeeID'],
                $row['Name'],
                $row['Date'],
                $row['ClockInTime'],
                $row['ClockOutTime'],
                $status
            ]);
        }


        fclose($output);
        exit(); 
    }


$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';


$sql = "SELECT * FROM attendancerecord WHERE 1";


if ($start_date) {
    $sql .= " AND Date >= '$start_date'";
}


if ($status) {
    $sql .= " AND Status = '$status'";
}

$sql .= " ORDER BY Date DESC LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Admin Dashboard</title>
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

        /* Adjust table column width */
        .table th, .table td {
            text-align: center;  /* Align content to center */
        }

        /* Style the action buttons */
        .table .btn {
            width: 50px;  /* Set width for action buttons to ensure they align properly */
            text-align: center;
        }

        /* Buttons positioned at the right top */
        .right-buttons {
            display: flex;
            justify-content: flex-end; /* Align buttons to the right */
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
                <h2 class="mb-4">Attendance Management</h2>

                <!-- Buttons (Export and Manual Entry) in Right Top -->
                <div class="right-buttons">
                    <a href="attendance.php?export=true" class="btn btn-success">
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
                            <div class="col-md-3">
                                <label class="form-label">Date Range</label>
                                <input type="date" name="start_date" class="form-control" 
                                    value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Present" <?= (isset($_GET['status']) && $_GET['status'] == 'Present') ? 'selected' : '' ?>>Present</option>
                                    <option value="Late" <?= (isset($_GET['status']) && $_GET['status'] == 'Late') ? 'selected' : '' ?>>Late</option>
                                    <option value="Absent" <?= (isset($_GET['status']) && $_GET['status'] == 'Absent') ? 'selected' : '' ?>>Absent</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
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
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
       
                                        $status = 'Present'; 

                                   
                                        if (strtotime($row["ClockInTime"]) > strtotime('09:00:00')) {
                                            $status = 'Late';
                                        }

                                  
                                        if (empty($row["ClockOutTime"])) {
                                            $status = 'Absent';
                                        }

                                        echo "<tr>";
                                        echo "<td>" . $row["EmployeeID"] . "</td>";
                                        echo "<td>" . $row["Name"] . "</td>";
                                        echo "<td>" . $row["Date"] . "</td>";
                                        echo "<td>" . $row["ClockInTime"] . "</td>";
                                        echo "<td>" . $row["ClockOutTime"] . "</td>";
                                        echo "<td><span class='badge bg-" . ($status == 'Present' ? "success" : ($status == 'Late' ? "warning" : "danger")) . "'>" . $status . "</span></td>";
                                        echo "<td>
                                            <a href='attendance.php?delete=" . $row['RecordID'] . "' class='btn btn-sm btn-danger'>
                                                <i class='fas fa-trash'></i> 
                                            </a>
                                            <button class='btn btn-sm btn-info' data-bs-toggle='modal' data-bs-target='#editModal' 
                                            data-id='" . $row['RecordID'] . "' 
                                            data-name='" . $row['Name'] . "' 
                                            data-clockin='" . $row['ClockInTime'] . "' 
                                            data-clockout='" . $row['ClockOutTime'] . "'>
                                                <i class='fas fa-edit'></i>
                                            </button>
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
                                <?php
                                
                                $sql = "SELECT COUNT(*) AS total_records FROM attendancerecord";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                $total_records = $row['total_records'];

                           
                                $total_pages = ceil($total_records / $records_per_page);

                         
                                echo '<nav aria-label="Page navigation">';
                                echo '<ul class="pagination">';

                       
                                if ($current_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="attendance.php?page=' . ($current_page - 1) . '">Previous</a></li>';
                                }

                         
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '"><a class="page-link" href="attendance.php?page=' . $i . '">' . $i . '</a></li>';
                                }

                       
                                if ($current_page < $total_pages) {
                                    echo '<li class="page-item"><a class="page-link" href="attendance.php?page=' . ($current_page + 1) . '">Next</a></li>';
                                }

                                echo '</ul>';
                                echo '</nav>';
                                ?>

            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Attendance Record</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="attendance.php">
                                                <input type="hidden" name="record_id" id="record_id">
                                                
                                                <label for="name">Name:</label>
                                                <input type="text" name="name" id="name" class="form-control" required><br>

                                                <label for="clockin_time">Clock In Time:</label>
                                                <input type="time" name="clockin_time" id="clockin_time" class="form-control" required><br>

                                                <label for="clockout_time">Clock Out Time:</label>
                                                <input type="time" name="clockout_time" id="clockout_time" class="form-control" required><br>

                                                <button type="submit" name="update" class="btn btn-primary">Update</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script>
   
                    var editButtons = document.querySelectorAll('.btn-info');
                    editButtons.forEach(function(button) {
                        button.addEventListener('click', function() {
                            var recordId = this.getAttribute('data-id');
                            var name = this.getAttribute('data-name');
                            var clockIn = this.getAttribute('data-clockin');
                            var clockOut = this.getAttribute('data-clockout');
                            

                            document.getElementById('record_id').value = recordId;
                            document.getElementById('name').value = name;
                            document.getElementById('clockin_time').value = clockIn;
                            document.getElementById('clockout_time').value = clockOut;
                        });
                    });
                </script>

                

                <!-- Manual Entry Modal -->
                <div class="modal fade" id="manualEntryModal" tabindex="-1" aria-labelledby="manualEntryModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="manualEntryModalLabel">Add Attendance Record</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="attendance.php">
                                    <label for="name">Name:</label>
                                    <input type="text" name="name" class="form-control" required><br>

                                    <label for="date">Date:</label>
                                    <input type="date" name="date" class="form-control" required><br>

                                    <label for="clockin_time">Clock In Time:</label>
                                    <input type="time" name="clockin_time" class="form-control" required><br>

                                    <label for="clockout_time">Clock Out Time:</label>
                                    <input type="time" name="clockout_time" class="form-control" required><br>

                                    <label for="employee_id">Employee ID:</label>
                                    <select name="employee_id" class="form-control" required>
                                        <?php
                                        while ($employee_row = $employee_result->fetch_assoc()) {
                                            echo "<option value='" . $employee_row['employee_id'] . "'>" . $employee_row['employee_id'] . " - " . $employee_row['name'] . "</option>";
                                        }
                                        ?>
                                    </select><br>

                                    <button type="submit" name="submit_entry" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
