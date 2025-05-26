<?php

include('db_connect.php');


$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
 
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

   
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    $sql = "INSERT INTO employees (employee_id, name, password) VALUES ('$employee_id', '$name', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        $success = "Employee added successfully!";
    } else {
        $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}


if (isset($_POST['edit_employee'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE employees SET name='$name', password='$hashed_password' WHERE employee_id='$employee_id'";
    } else {
        $sql = "UPDATE employees SET name='$name' WHERE employee_id='$employee_id'";
    }

    if (mysqli_query($conn, $sql)) {
        $success = "Employee updated successfully!";
    } else {
        $error = "Error updating employee: " . mysqli_error($conn);
    }
}

if (isset($_GET['delete'])) {
    $employee_id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Delete employee record from the database
    $sql_delete = "DELETE FROM employees WHERE employee_id='$employee_id'";
    
    if (mysqli_query($conn, $sql_delete)) {
        $success = "Employee deleted successfully!";
    } else {
        $error = "Error deleting employee: " . mysqli_error($conn);
    }
}


$sql = "SELECT * FROM employees";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management - Admin Dashboard</title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Employee Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        <i class="fas fa-plus me-2"></i>Add New Employee
                    </button>
                </div>

                <!-- Employee List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Display employee data dynamically
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row['employee_id'] . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td>
                                                <button class='btn btn-sm btn-info' data-bs-toggle='modal' data-bs-target='#editEmployeeModal' 
                                                        data-id='" . $row['employee_id'] . "' 
                                                        data-name='" . $row['name'] . "' 
                                                        data-password='" . $row['password'] . "'>
                                                    <i class='fas fa-edit'></i> Edit
                                                </button>
                                                <a href='employees.php?delete=" . $row['employee_id'] . "' class='btn btn-sm btn-danger'>
                                                    <i class='fas fa-trash'></i> Delete
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
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Success or Error Message -->
                    <?php if($success) { echo "<div class='alert alert-success'>$success</div>"; } ?>
                    <?php if($error) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                    
                    <!-- Employee Form -->
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="add_employee" class="btn btn-primary">Add Employee</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Success or Error Message -->
                    <?php if($success) { echo "<div class='alert alert-success'>$success</div>"; } ?>
                    <?php if($error) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

                    <!-- Employee Form for Edit -->
                    <form method="POST">
                        <input type="hidden" name="employee_id" id="edit_employee_id">
                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" id="edit_employee_id_display" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="edit_password">
                        </div>
                        <button type="submit" name="edit_employee" class="btn btn-primary">Update Employee</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Modal Populating -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        $('#editEmployeeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var employeeId = button.data('id'); // Extract info from data-* attributes
            var employeeName = button.data('name');
            var employeePassword = button.data('password');


            var modal = $(this);
            modal.find('.modal-body #edit_employee_id').val(employeeId);
            modal.find('.modal-body #edit_employee_id_display').val(employeeId);
            modal.find('.modal-body #edit_name').val(employeeName);
            modal.find('.modal-body #edit_password').val(employeePassword); // If needed
        });
    </script>
</body>
</html>
