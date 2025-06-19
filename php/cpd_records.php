<?php
include('db_connect.php');

// Handle Delete CPD Record
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Get record details before deletion for progress recalculation
    $record_sql = "SELECT Employee_ID, programme_name, duration_hours FROM cpd_record WHERE RecordId = $id";
    $record_result = $conn->query($record_sql);
    
    if ($record_result->num_rows > 0) {
        $record_data = $record_result->fetch_assoc();
        
        // Delete the CPD record
        $sql = "DELETE FROM cpd_record WHERE RecordId = $id";
        if ($conn->query($sql) === TRUE) {
            // Manually update progress if needed (in case trigger doesn't handle deletes)
            if ($record_data['duration_hours'] > 0) {
                $prog_sql = "SELECT category_id FROM cpd_program WHERE programme_name = '" . 
                           mysqli_real_escape_string($conn, $record_data['programme_name']) . "'";
                $prog_result = $conn->query($prog_sql);
                
                if ($prog_result->num_rows > 0) {
                    $prog_data = $prog_result->fetch_assoc();
                    $update_progress = "UPDATE employee_cpd_progress 
                                       SET completed_hours = GREATEST(0, completed_hours - " . $record_data['duration_hours'] . ") 
                                       WHERE employee_id = '" . $record_data['Employee_ID'] . "' 
                                       AND category_id = " . $prog_data['category_id'];
                    $conn->query($update_progress);
                }
            }
            
            header('Location: cpd_records.php?success=Record deleted successfully');
            exit();
        } else {
            header('Location: cpd_records.php?error=' . urlencode("Error deleting record: " . $conn->error));
            exit();
        }
    }
}

// Handle success/error messages
$success = "";
$error = "";

// Check for URL parameters
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Handle search and filtering
$search_employee = isset($_GET['search_employee']) ? $_GET['search_employee'] : '';
$search_program = isset($_GET['search_program']) ? $_GET['search_program'] : '';
$search_date_from = isset($_GET['search_date_from']) ? $_GET['search_date_from'] : '';
$search_date_to = isset($_GET['search_date_to']) ? $_GET['search_date_to'] : '';

// Build WHERE clause for filtering
$where_conditions = [];
if (!empty($search_employee)) {
    $search_employee_escaped = mysqli_real_escape_string($conn, $search_employee);
    $where_conditions[] = "(r.Employee_ID LIKE '%$search_employee_escaped%' OR r.Name LIKE '%$search_employee_escaped%')";
}
if (!empty($search_program)) {
    $search_program_escaped = mysqli_real_escape_string($conn, $search_program);
    $where_conditions[] = "r.programme_name LIKE '%$search_program_escaped%'";
}
if (!empty($search_date_from)) {
    $where_conditions[] = "r.Date >= '$search_date_from'";
}
if (!empty($search_date_to)) {
    $where_conditions[] = "r.Date <= '$search_date_to'";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Fetch CPD records with category information
$sql = "SELECT r.*, p.category_id, c.category_name, c.target_hours 
        FROM cpd_record r
        LEFT JOIN cpd_program p ON r.programme_name = p.programme_name
        LEFT JOIN cpd_categories c ON p.category_id = c.category_id
        $where_clause
        ORDER BY r.Date DESC, r.ClockInTime DESC";
$result = $conn->query($sql);

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_records,
    COUNT(DISTINCT Employee_ID) as total_employees,
    COUNT(DISTINCT programme_name) as total_programs,
    SUM(CASE WHEN ClockOutTime IS NOT NULL THEN duration_hours ELSE 0 END) as total_hours
    FROM cpd_record r $where_clause";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
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
        .nav-link:hover, .nav-link.active {
            background-color: #495057;
            color: white;
        }
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
        }
        .search-card {
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }
        .status-badge {
            font-size: 0.8em;
        }
        .duration-display {
            font-weight: bold;
            color: #198754;
        }
        .category-badge {
            font-size: 0.75em;
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
                    <a class="nav-link" href="reports.php"><i class="fas fa-chart-bar me-2"></i>Reports</a>
                    <a class="nav-link" href="cpd_management.php"><i class="fas fa-graduation-cap me-2"></i>Event Programs</a>
                    <a class="nav-link" href="cpd_records.php"><i class="fas fa-clipboard-list me-2"></i>Event Records</a>
                    <a class="nav-link" href="cpd_categories.php"><i class="fas fa-tags me-2"></i>Event Categories</a>
                    <a class="nav-link" href="leave_requests.php"><i class="fas fa-calendar-times me-2"></i>Accept Leave Form</a>
                    <a class="nav-link" href="overtime_requests.php"><i class="fas fa-clock me-2"></i>Accept Overtime Form</a>
                    <a class="nav-link" href="admin_logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="container mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-clipboard-list me-2"></i>Manage Event Records</h2>
                        <div>
                            <a href="cpd_management.php" class="btn btn-outline-secondary">
                                <i class="fas fa-graduation-cap me-2"></i>Manage Programs
                            </a>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                    <h4><?= number_format($stats['total_records']); ?></h4>
                                    <small>Total Records</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h4><?= number_format($stats['total_employees']); ?></h4>
                                    <small>Employees Participating</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-graduation-cap fa-2x mb-2"></i>
                                    <h4><?= number_format($stats['total_programs']); ?></h4>
                                    <small>Different Programs</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h4><?= number_format($stats['total_hours'], 1); ?></h4>
                                    <small>Total Hours Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if ($success): ?>
                        <div class='alert alert-success alert-dismissible fade show'>
                            <i class='fas fa-check-circle me-2'></i><?= htmlspecialchars($success); ?>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class='alert alert-danger alert-dismissible fade show'>
                            <i class='fas fa-exclamation-circle me-2'></i><?= htmlspecialchars($error); ?>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                    <?php endif; ?>

                    <!-- Search and Filter Card -->
                    <div class="card search-card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-search me-2"></i>Search & Filter Records</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="cpd_records.php">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="search_employee" class="form-label">Employee</label>
                                        <input type="text" name="search_employee" id="search_employee" 
                                               class="form-control" placeholder="Name or ID" 
                                               value="<?= htmlspecialchars($search_employee); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="search_program" class="form-label">Program</label>
                                        <input type="text" name="search_program" id="search_program" 
                                               class="form-control" placeholder="Program name" 
                                               value="<?= htmlspecialchars($search_program); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="search_date_from" class="form-label">Date From</label>
                                        <input type="date" name="search_date_from" id="search_date_from" 
                                               class="form-control" value="<?= htmlspecialchars($search_date_from); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="search_date_to" class="form-label">Date To</label>
                                        <input type="date" name="search_date_to" id="search_date_to" 
                                               class="form-control" value="<?= htmlspecialchars($search_date_to); ?>">
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                    <a href="cpd_records.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- CPD Records Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Event Records (<?= $result->num_rows; ?> records)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Employee</th>
                                            <th>Program</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Clock In</th>
                                            <th>Clock Out</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><span class="badge bg-secondary"><?= $row['RecordId']; ?></span></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($row['Name']); ?></strong><br>
                                                        <small class="text-muted">ID: <?= $row['Employee_ID']; ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($row['programme_name']); ?>">
                                                            <?= htmlspecialchars($row['programme_name']); ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($row['category_name']): ?>
                                                            <span class="badge bg-primary category-badge"><?= htmlspecialchars($row['category_name']); ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary category-badge">Uncategorized</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('M j, Y', strtotime($row['Date'])); ?></td>
                                                    <td><?= date('g:i A', strtotime($row['ClockInTime'])); ?></td>
                                                    <td>
                                                        <?php if ($row['ClockOutTime']): ?>
                                                            <?= date('g:i A', strtotime($row['ClockOutTime'])); ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Not set</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($row['duration_hours'] > 0): ?>
                                                            <span class="duration-display"><?= number_format($row['duration_hours'], 2); ?>h</span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($row['ClockOutTime']): ?>
                                                            <span class="badge bg-success status-badge">Completed</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning status-badge">In Progress</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="cpd_records.php?delete=<?= $row['RecordId']; ?>" class="btn btn-outline-danger btn-sm" 
                                                           onclick="return confirm('Are you sure you want to delete this CPD record? This action cannot be undone.');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center py-4">
                                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No CPD Records Found</h5>
                                                    <p class="text-muted">No records match your search criteria.</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        });
    }, 5000);

    // Enhanced search functionality
    document.getElementById('search_employee').addEventListener('input', function() {
        if (this.value.length >= 3 || this.value.length === 0) {
            // Auto-submit search after 3 characters or when cleared
            setTimeout(() => {
                if (this.value === document.getElementById('search_employee').value) {
                    // Value hasn't changed in 500ms, submit search
                    this.form.submit();
                }
            }, 500);
        }
    });

    // Initialize tooltips for truncated text
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    </script>
</body>
</html>
