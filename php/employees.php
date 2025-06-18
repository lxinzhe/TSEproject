<?php
include('db_connect.php');

$success = "";
$error = "";

// Handle Add Employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if employee ID already exists
    $check_sql = "SELECT employee_id FROM employees WHERE employee_id = '$employee_id'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Employee ID already exists. Please use a different ID.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO employees (employee_id, name, password) VALUES ('$employee_id', '$name', '$hashed_password')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Employee added successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle Edit Employee
if (isset($_POST['edit_employee'])) {
    $original_id = mysqli_real_escape_string($conn, $_POST['original_employee_id']);
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    // Check if new employee ID exists (if changed)
    if ($original_id != $employee_id) {
        $check_sql = "SELECT employee_id FROM employees WHERE employee_id = '$employee_id' AND employee_id != '$original_id'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Employee ID already exists. Please use a different ID.";
        } else {
            // Update with new employee ID (password remains unchanged)
            $sql = "UPDATE employees SET employee_id='$employee_id', name='$name' WHERE employee_id='$original_id'";
        }
    } else {
        // Update without changing employee ID (password remains unchanged)
        $sql = "UPDATE employees SET name='$name' WHERE employee_id='$original_id'";
    }
    
    if (!isset($error) && isset($sql) && mysqli_query($conn, $sql)) {
        $success = "Employee updated successfully!";
    } elseif (!isset($error)) {
        $error = "Error updating employee: " . mysqli_error($conn);
    }
}

// Handle Delete Employee
if (isset($_GET['delete'])) {
    $employee_id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Check if employee has attendance records
    $check_attendance = "SELECT COUNT(*) as count FROM attendancerecord WHERE EmployeeID = '$employee_id'";
    $attendance_result = mysqli_query($conn, $check_attendance);
    $attendance_data = mysqli_fetch_assoc($attendance_result);
    
    if ($attendance_data['count'] > 0) {
        $error = "Cannot delete employee. This employee has attendance records. Please remove attendance records first or contact system administrator.";
    } else {
        $sql_delete = "DELETE FROM employees WHERE employee_id='$employee_id'";
        
        if (mysqli_query($conn, $sql_delete)) {
            $success = "Employee deleted successfully!";
        } else {
            $error = "Error deleting employee: " . mysqli_error($conn);
        }
    }
}

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_condition = "";
if ($search) {
    $search_condition = "WHERE employee_id LIKE '%$search%' OR name LIKE '%$search%'";
}

// Pagination
$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// Get total records for pagination
$count_sql = "SELECT COUNT(*) as total FROM employees $search_condition";
$count_result = mysqli_query($conn, $count_sql);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get employees with pagination
$sql = "SELECT * FROM employees $search_condition ORDER BY name ASC LIMIT $records_per_page OFFSET $offset";
$result = mysqli_query($conn, $sql);

// Get some statistics
$stats_sql = "SELECT COUNT(*) as total_employees FROM employees";
$stats_result = mysqli_query($conn, $stats_sql);
$total_employees = mysqli_fetch_assoc($stats_result)['total_employees'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management - Admin Dashboard</title>
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
        .stats-card {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .stats-card h3 {
            margin: 0;
            font-size: 2.5em;
        }
        .search-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
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
                    <a class="nav-link active" href="employees.php"><i class="fas fa-users me-2"></i>Employees</a>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Employee Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        <i class="fas fa-plus me-2"></i>Add New Employee
                    </button>
                </div>

                <!-- Success/Error Messages -->
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Card -->
                <div class="row mb-4">
                    <div class="col-md-6 mx-auto">
                        <div class="stats-card">
                            <h3><?= $total_employees ?></h3>
                            <p><i class="fas fa-users me-2"></i>Total Employees</p>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="search-container">
                    <form method="GET" action="employees.php" class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by Employee ID or Name..." 
                                       value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="employees.php" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Employee List -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Employee List</h5>
                        <span class="badge bg-primary"><?= $total_records ?> employees</span>
                    </div>
                    <div class="card-body">
                        <?php if ($total_records > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>Date Added</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($row['employee_id']) ?></strong></td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                                <td>
                                                    <button class='btn btn-sm btn-info me-1' 
                                                            data-bs-toggle='modal' 
                                                            data-bs-target='#editEmployeeModal' 
                                                            onclick="populateEditModal('<?= $row['employee_id'] ?>', '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>')">
                                                        <i class='fas fa-edit'></i> Edit
                                                    </button>
                                                    <a href='employees.php?delete=<?= $row['employee_id'] ?>' 
                                                       class='btn btn-sm btn-danger'
                                                       onclick="return confirm('Are you sure you want to delete this employee?\n\nEmployee: <?= htmlspecialchars($row['name']) ?>\nID: <?= $row['employee_id'] ?>\n\nThis action cannot be undone.')">
                                                        <i class='fas fa-trash'></i> Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Employee pagination">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($current_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="employees.php?page=<?= $current_page - 1 ?>&search=<?= urlencode($search) ?>">
                                                    <i class="fas fa-chevron-left"></i> Previous
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                                <a class="page-link" href="employees.php?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($current_page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="employees.php?page=<?= $current_page + 1 ?>&search=<?= urlencode($search) ?>">
                                                    Next <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No employees found</h5>
                                <p class="text-muted">
                                    <?= $search ? "No employees match your search criteria." : "Start by adding your first employee." ?>
                                </p>
                            </div>
                        <?php endif; ?>
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
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="employees.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                            <input type="text" name="employee_id" class="form-control" required 
                                   placeholder="Enter unique employee ID">
                            <div class="form-text">Must be unique and cannot be changed later</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required 
                                   placeholder="Enter employee's full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required 
                                   placeholder="Enter default password">
                            <div class="form-text">Employee can change this after first login</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_employee" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="employees.php">
                    <div class="modal-body">
                        <input type="hidden" name="original_employee_id" id="edit_original_id">
                        <div class="mb-3">
                            <label class="form-label">Employee ID <span class="text-danger">*</span></label>
                            <input type="text" name="employee_id" class="form-control" id="edit_employee_id" required>
                            <div class="form-text">Changing this ID will update all related records</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="edit_name" required>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Employee password cannot be changed from this interface for security reasons.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_employee" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function populateEditModal(employeeId, employeeName) {
            document.getElementById('edit_original_id').value = employeeId;
            document.getElementById('edit_employee_id').value = employeeId;
            document.getElementById('edit_name').value = employeeName;
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>
