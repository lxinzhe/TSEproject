<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.html");
    exit();
}

include('db_connect.php');

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        $status = 'approved';
        $message = "Leave request approved successfully!";
        $alert_class = "alert-success";
    } else {
        $status = 'rejected';
        $message = "Leave request rejected.";
        $alert_class = "alert-warning";
    }
    
    $sql = "UPDATE leave_requests SET status = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $_SESSION['admin_username'], $request_id);
    $stmt->execute();
    $stmt->close();
}

// Get all leave requests
$sql = "SELECT lr.*, e.name as employee_name 
        FROM leave_requests lr 
        LEFT JOIN employees e ON lr.employee_id = e.employee_id 
        ORDER BY lr.submitted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Requests - Admin Panel</title>
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
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #495057;
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border-bottom: none;
            border-radius: 15px 15px 0 0 !important;
            font-weight: bold;
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .badge {
            font-size: 0.75rem;
            padding: 5px 10px;
        }
        .btn-action {
            margin: 2px;
            padding: 5px 15px;
            font-size: 0.8rem;
        }
        .header-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-pending {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
        }
        .status-approved {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .status-rejected {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <div class="text-center mb-4">
                    <h3>Admin Panel</h3>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="admin.php">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="employees.php">
                        <i class="fas fa-users me-2"></i>Employees
                    </a>
                    <a class="nav-link" href="attendance.php">
                        <i class="fas fa-calendar-check me-2"></i>Attendance
                    </a>
                    <a class="nav-link" href="reports.php">
                        <i class="fas fa-chart-bar me-2"></i>Reports
                    </a>
                    <a class="nav-link" href="cpd_management.php">
                        <i class="fas fa-graduation-cap me-2"></i>CPD Programs
                    </a>
                    <a class="nav-link" href="cpd_records.php">
                        <i class="fas fa-clipboard-list me-2"></i>CPD Records
                    </a>
                    <a class="nav-link active" href="leave_requests.php">
                        <i class="fas fa-calendar-times me-2"></i>Accept Leave Form
                    </a>
                    <a class="nav-link" href="overtime_requests.php">
                        <i class="fas fa-clock me-2"></i>Accept Overtime Form
                    </a>
                    <a class="nav-link" href="admin_logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header Section -->
                <div class="header-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-calendar-times me-2"></i>Leave Requests Management
                            </h2>
                            <p class="text-muted mb-0">Review and approve/reject employee leave requests</p>
                        </div>
                        <div class="text-end">
                            <div class="text-muted">
                                <i class="fas fa-calendar me-1"></i><?= date('l, F j, Y') ?>
                            </div>
                            <div class="text-muted">
                                <i class="fas fa-clock me-1"></i><?= date('g:i A') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($message)): ?>
                    <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Leave Requests Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Leave Requests
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Employee</th>
                                            <th>Leave Type</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Days</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Submitted</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <?php
                                            $start_date = new DateTime($row['start_date']);
                                            $end_date = new DateTime($row['end_date']);
                                            $days = $start_date->diff($end_date)->days + 1;
                                            
                                            $status_class = '';
                                            switch($row['status']) {
                                                case 'pending': $status_class = 'status-pending'; break;
                                                case 'approved': $status_class = 'status-approved'; break;
                                                case 'rejected': $status_class = 'status-rejected'; break;
                                            }
                                            ?>
                                            <tr>
                                                <td><?= $row['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($row['employee_name'] ?? 'Unknown') ?></strong>
                                                    <br><small class="text-muted">ID: <?= $row['employee_id'] ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= ucfirst($row['leave_type']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M j, Y', strtotime($row['start_date'])) ?></td>
                                                <td><?= date('M j, Y', strtotime($row['end_date'])) ?></td>
                                                <td><span class="badge bg-secondary"><?= $days ?> day<?= $days > 1 ? 's' : '' ?></span></td>
                                                <td>
                                                    <div style="max-width: 200px; overflow: hidden;">
                                                        <?= htmlspecialchars($row['reason']) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $status_class ?>">
                                                        <?= ucfirst($row['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= date('M j, Y', strtotime($row['submitted_at'])) ?>
                                                    <br><small class="text-muted"><?= date('g:i A', strtotime($row['submitted_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] == 'pending'): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                                            <button type="submit" name="action" value="approve" 
                                                                    class="btn btn-success btn-action btn-sm"
                                                                    onclick="return confirm('Approve this leave request?')">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        </form>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                                            <button type="submit" name="action" value="reject" 
                                                                    class="btn btn-danger btn-action btn-sm"
                                                                    onclick="return confirm('Reject this leave request?')">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <small class="text-muted">
                                                            <?= ucfirst($row['status']) ?> by <?= $row['reviewed_by'] ?? 'System' ?>
                                                            <br><?= $row['reviewed_at'] ? date('M j, Y', strtotime($row['reviewed_at'])) : '' ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Leave Requests Found</h5>
                                <p class="text-muted">No employee leave requests have been submitted yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?> 