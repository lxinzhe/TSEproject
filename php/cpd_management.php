<?php
include('db_connect.php');

// Handle Delete CPD Program
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete the CPD program from the database
    $sql = "DELETE FROM cpd_program WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header('Location: cpd_management.php');
        exit();
    } else {
        echo "Error deleting program: " . $conn->error;
    }
}

// Handle Add CPD Program
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_cpd'])) {
    $programme_name = mysqli_real_escape_string($conn, $_POST['programme_name']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

    // Insert new CPD program into the database
    $sql = "INSERT INTO cpd_program (programme_name, Day_Start, Day_End) VALUES ('$programme_name', '$start_date', '$end_date')";
    
    if ($conn->query($sql) === TRUE) {
        $success = "CPD Program added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle Edit CPD Program
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_cpd'])) {
    // Check if 'program_id' is set
    if (isset($_POST['program_id'])) {
        $id = $_POST['program_id'];
        $programme_name = mysqli_real_escape_string($conn, $_POST['programme_name']);
        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

        // Update CPD program in the database
        $sql = "UPDATE cpd_program SET programme_name='$programme_name', Day_Start='$start_date', Day_End='$end_date' WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            $success = "CPD Program updated successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        // If 'program_id' is not set
        $error = "No program selected for editing.";
    }
}



// Fetch CPD programs from the database
$sql = "SELECT * FROM cpd_program ORDER BY Day_Start DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPD Management</title>
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
                    <h2>Manage CPD Programs</h2>

                    <!-- Success/Error Messages -->
                    <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
                    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <!-- Add New CPD Program Form -->
                    <div class="mb-3 mt-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCPDModal">Add New CPD Program</button>
                    </div>

                    <!-- CPD Programs Table -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Program Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['programme_name']; ?></td>
                                    <td><?= $row['Day_Start']; ?></td>
                                    <td><?= $row['Day_End']; ?></td>
                                    <td>
                                        <a href="cpd_management.php?edit=<?= $row['id']; ?>" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editCPDModal" onclick="populateEditModal(<?= $row['id']; ?>, '<?= $row['programme_name']; ?>', '<?= $row['Day_Start']; ?>', '<?= $row['Day_End']; ?>')">Edit</a>
                                        <a href="cpd_management.php?delete=<?= $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this CPD program?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding CPD Program -->
    <div class="modal fade" id="addCPDModal" tabindex="-1" aria-labelledby="addCPDModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCPDModalLabel">Add New CPD Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="cpd_management.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="programme_name" class="form-label">Program Name</label>
                            <input type="text" name="programme_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_cpd" class="btn btn-primary">Add Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Editing CPD Program -->
<div class="modal fade" id="editCPDModal" tabindex="-1" aria-labelledby="editCPDModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCPDModalLabel">Edit CPD Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="cpd_management.php" id="editCPDForm">
                <div class="modal-body">
                    <!-- Add hidden input for program_id -->
                    <input type="hidden" name="program_id" id="edit_program_id" value="">
                    
                    <div class="mb-3">
                        <label for="edit_programme_name" class="form-label">Program Name</label>
                        <input type="text" name="programme_name" class="form-control" id="edit_programme_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" id="edit_start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" id="edit_end_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_cpd" class="btn btn-primary">Update Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function populateEditModal(id, programme_name, start_date, end_date) {
    // Set the values to the modal form fields
    document.getElementById('edit_programme_name').value = programme_name;
    document.getElementById('edit_start_date').value = start_date;
    document.getElementById('edit_end_date').value = end_date;
    
    // Set the program ID in the hidden input field
    document.getElementById('edit_program_id').value = id;
    
    console.log('Edit modal populated with ID:', id); // Debug line
}
</script>
</body>
</html>
