<?php
include('db_connect.php');

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Check if category has associated programs
    $check_sql = "SELECT COUNT(*) as count FROM cpd_program WHERE category_id = $id";
    $check_result = $conn->query($check_sql);
    $check_row = $check_result->fetch_assoc();
    
    if ($check_row['count'] > 0) {
        $error = "Cannot delete category. It has " . $check_row['count'] . " associated programs. Please reassign or delete those programs first.";
    } else {
        $sql = "DELETE FROM cpd_categories WHERE category_id = $id";
        if ($conn->query($sql) === TRUE) {
            header('Location: cpd_categories.php');
            exit();
        } else {
            $error = "Error deleting category: " . $conn->error;
        }
    }
}

// Handle Add Category
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $target_hours = mysqli_real_escape_string($conn, $_POST['target_hours']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO cpd_categories (category_name, target_hours, description) VALUES ('$category_name', '$target_hours', '$description')";
    
    if ($conn->query($sql) === TRUE) {
        $success = "CPD Category added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Handle Edit Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category'])) {
    if (isset($_POST['category_id'])) {
        $id = $_POST['category_id'];
        $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
        $target_hours = mysqli_real_escape_string($conn, $_POST['target_hours']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $sql = "UPDATE cpd_categories SET category_name='$category_name', target_hours='$target_hours', description='$description' WHERE category_id = $id";
        
        if ($conn->query($sql) === TRUE) {
            $success = "CPD Category updated successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "No category selected for editing.";
    }
}

// Fetch categories with program count and employee progress
$sql = "SELECT c.*, 
        COUNT(DISTINCT p.id) as program_count,
        COUNT(DISTINCT ep.employee_id) as employee_count,
        AVG(ep.completed_hours) as avg_completed_hours
        FROM cpd_categories c 
        LEFT JOIN cpd_program p ON c.category_id = p.category_id 
        LEFT JOIN employee_cpd_progress ep ON c.category_id = ep.category_id
        GROUP BY c.category_id 
        ORDER BY c.category_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CPD Categories Management</title>
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
        .category-card {
            border-left: 4px solid #007bff;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .category-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stats-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        .progress-ring {
            width: 60px;
            height: 60px;
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
                        <h2><i class="fas fa-tags me-2"></i>Manage CPD Categories</h2>
                        <a href="cpd_management.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Programs
                        </a>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if ($success) echo "<div class='alert alert-success alert-dismissible fade show'><i class='fas fa-check-circle me-2'></i>$success<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>"; ?>
                    <?php if ($error) echo "<div class='alert alert-danger alert-dismissible fade show'><i class='fas fa-exclamation-circle me-2'></i>$error<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>"; ?>

                    <!-- Add New Category Button -->
                    <div class="mb-4">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus me-2"></i>Add New Category
                        </button>
                    </div>

                    <!-- Categories Grid -->
                    <div class="row">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card category-card h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-tag me-2"></i>
                                            <?= htmlspecialchars($row['category_name']); ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-muted">
                                            <?= htmlspecialchars($row['description']) ?: 'No description available'; ?>
                                        </p>
                                        
                                        <!-- Stats Row -->
                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <div class="stats-icon bg-success mx-auto mb-2">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <small class="text-muted">Target Hours</small>
                                                <div class="fw-bold"><?= $row['target_hours']; ?>h</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stats-icon bg-info mx-auto mb-2">
                                                    <i class="fas fa-graduation-cap"></i>
                                                </div>
                                                <small class="text-muted">Programs</small>
                                                <div class="fw-bold"><?= $row['program_count']; ?></div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stats-icon bg-warning mx-auto mb-2">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <small class="text-muted">Employees</small>
                                                <div class="fw-bold"><?= $row['employee_count']; ?></div>
                                            </div>
                                        </div>

                                        <!-- Average Progress -->
                                        <?php if ($row['avg_completed_hours'] > 0): ?>
                                            <div class="mb-3">
                                                <small class="text-muted">Average Progress</small>
                                                <div class="progress">
                                                    <?php 
                                                    $progress_percent = min(100, ($row['avg_completed_hours'] / $row['target_hours']) * 100);
                                                    ?>
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: <?= $progress_percent; ?>%">
                                                        <?= number_format($progress_percent, 1); ?>%
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    <?= number_format($row['avg_completed_hours'], 1); ?>h / <?= $row['target_hours']; ?>h
                                                </small>
                                            </div>
                                        <?php endif; ?>

                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCategoryModal" 
                                                onclick="populateEditModal(<?= $row['category_id']; ?>, '<?= htmlspecialchars($row['category_name'], ENT_QUOTES); ?>', <?= $row['target_hours']; ?>, '<?= htmlspecialchars($row['description'], ENT_QUOTES); ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            
                                            <?php if ($row['program_count'] == 0): ?>
                                                <a href="cpd_categories.php?delete=<?= $row['category_id']; ?>" 
                                                   class="btn btn-outline-danger btn-sm" 
                                                   onclick="return confirm('Are you sure you want to delete this category?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary btn-sm" disabled title="Cannot delete - has associated programs">
                                                    <i class="fas fa-lock"></i> Protected
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-footer text-muted">
                                        <small>
                                            <i class="fas fa-calendar-plus me-1"></i>
                                            Created: <?= date('M j, Y', strtotime($row['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Empty State -->
                    <?php if ($result->num_rows == 0): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No CPD Categories Found</h4>
                            <p class="text-muted">Create your first CPD category to get started.</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="fas fa-plus me-2"></i>Add First Category
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">
                        <i class="fas fa-plus me-2"></i>Add New CPD Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="cpd_categories.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" name="category_name" class="form-control" required maxlength="255">
                            <div class="form-text">Enter a descriptive name for this CPD category</div>
                        </div>
                        <div class="mb-3">
                            <label for="target_hours" class="form-label">Target Hours</label>
                            <input type="number" name="target_hours" class="form-control" min="1" max="1000" required>
                            <div class="form-text">Recommended hours employees should complete in this category</div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" maxlength="500"></textarea>
                            <div class="form-text">Brief description of what this category covers (optional)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_category" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Category -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit CPD Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="cpd_categories.php">
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="edit_category_id" value="">
                        
                        <div class="mb-3">
                            <label for="edit_category_name" class="form-label">Category Name</label>
                            <input type="text" name="category_name" class="form-control" id="edit_category_name" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="edit_target_hours" class="form-label">Target Hours</label>
                            <input type="number" name="target_hours" class="form-control" id="edit_target_hours" min="1" max="1000" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea name="description" class="form-control" id="edit_description" rows="3" maxlength="500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_category" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function populateEditModal(id, category_name, target_hours, description) {
        document.getElementById('edit_category_id').value = id;
        document.getElementById('edit_category_name').value = category_name;
        document.getElementById('edit_target_hours').value = target_hours;
        document.getElementById('edit_description').value = description;
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        });
    }, 5000);
    </script>
</body>
</html>
