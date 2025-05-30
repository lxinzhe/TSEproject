
<?php
require_once "db_connect.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ot_date = $_POST['otDate'] ?? '';
    $start_time = $_POST['startTime'] ?? '';
    $end_time = $_POST['endTime'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $department = $_POST['department'] ?? '';

    if (empty($ot_date) || empty($start_time) || empty($end_time) || empty($department)) {
        $message = "Please fill in all required fields.";
        $message_type = "error";
    } else {
        $start = new DateTime($start_time);
        $end = new DateTime($end_time);
        $interval = $start->diff($end);
        $total_hours = $interval->h + ($interval->i / 60);
        
        if ($total_hours <= 0) {
            $message = "The end time must be after the start time.";
            $message_type = "error";
        } else {
            $link = $conn;

            $sql = "INSERT INTO overtime_requests (ot_date, start_time, end_time, total_hours, reason, department) VALUES (?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssdss", $ot_date, $start_time, $end_time, $total_hours, $reason, $department);
                
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Overtime application submitted successfully.";
                    $message_type = "success";
                    
                    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . "?status=success");
                    exit;
                } else {
                    $message = "Submission failed, please try again later.";
                    $message_type = "error";
                }

                mysqli_stmt_close($stmt);
            }

            $link->close();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['status']) && $_GET['status'] === 'success') {
    $message = "Overtime application submitted successfully.";
    $message_type = "success";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Overtime Request Form</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      margin: 0;
      background-color: #f8f7fc;
      color: #333;
    }

    header {
      background-color: #6a0dad;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
      font-size: 1.5rem;
    }

    .container {
      display: flex;
      height: calc(100vh - 70px);
    }

    .sidebar {
      width: 220px;
      background-color: #ffffff;
      border-right: 1px solid #ddd;
      padding: 1rem;
    }

    .sidebar nav a {
      display: block;
      padding: 0.75rem;
      margin-bottom: 0.5rem;
      color: #6a0dad;
      text-decoration: none;
      border-radius: 5px;
      transition: background 0.2s;
    }

    .sidebar nav a:hover {
      background-color: #f0e6ff;
    }

    .logout {
      margin-top: 186%;
      text-align: left;
    }

    .logout a {
      color: #6a0dad;
      text-decoration: none;
      padding: 0.75rem;
      border-radius: 5px;
      transition: background 0.2s;
    }

    .logout a:hover {
      background-color: #f0e6ff;
    }

    .main {
      flex-grow: 1;
      padding: 2rem;
    }

    .card {
      background-color: #fff;
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      margin-bottom: 2rem;
    }

    .card h2 {
      color: #6a0dad;
      margin-top: 0;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
    }

    /* Form styles */
    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: #555;
    }

    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    .form-control:focus {
      border-color: #6a0dad;
      outline: none;
      box-shadow: 0 0 0 3px rgba(106, 13, 173, 0.1);
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .submit-btn {
      width: 100%;
      background-color: #6a0dad;
      color: white;
      border: none;
      padding: 0.85rem;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.2s;
      margin-top: 1rem;
    }

    .submit-btn:hover {
      background-color: #5a0bb2;
    }

    /* Alert styles */
    .alert {
      padding: 1rem;
      border-radius: 5px;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background-color: #e5f7ed;
      color: #26744e;
      border: 1px solid #b7e2cc;
    }

    .alert-error {
      background-color: #fbeaea;
      color: #a42626;
      border: 1px solid #f5c9c9;
    }

    /* Textarea height */
    textarea.form-control {
      min-height: 120px;
      resize: vertical;
    }
  </style>
</head>
<body>

<header>
  <h1>🎮 GameHub Attendance</h1>
  <div>Welcome, Employee</div>
</header>

<div class="container">
  <aside class="sidebar">
    <nav>
      <a href="/TSE PROJECT/UserMainPage.html">Dashboard</a>
      <a href="/TSE PROJECT/ViewRecords.html">View Records</a>
      <a href="employee_leave_form.php">Leave Form</a>
      <a href="employee_OT_form.php">OverTime Form</a>
      <a href="/TSE PROJECT/CPD.html">CPD Programme</a>
    </nav>
      <div class="logout"><a href="php/Userlogin.php">Logout</a></div>
  </aside>

  <main class="main">
    <div class="card">
      <h2>Overtime Request Form</h2>
      
      <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_type === 'success' ? 'alert-success' : 'alert-error'; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
          <label for="department">Department</label>
          <select 
            id="department" 
            name="department" 
            class="form-control" 
            required
          >
            <option value="">Select Department</option>
            <option value="IT">IT</option>
            <option value="HR">HR</option>
            <option value="Finance">Finance</option>
            <option value="Operations">Operations</option>
            <option value="Sales">Sales</option>
            <option value="Marketing">Marketing</option>
          </select>
        </div>

        <div class="form-group">
          <label for="otDate">Overtime Date</label>
          <input 
            type="date" 
            id="otDate" 
            name="otDate" 
            class="form-control" 
            required
          >
        </div>

        <div class="grid-2">
          <div class="form-group">
            <label for="startTime">Start Time</label>
            <input 
              type="time" 
              id="startTime" 
              name="startTime" 
              class="form-control" 
              required
            >
          </div>
          <div class="form-group">
            <label for="endTime">End Time</label>
            <input 
              type="time" 
              id="endTime" 
              name="endTime" 
              class="form-control" 
              required
            >
          </div>
        </div>

        <div class="form-group">
          <label for="reason">Reason for Overtime</label>
          <textarea 
            id="reason" 
            name="reason" 
            class="form-control" 
            placeholder="Enter your reason for overtime request"
          ></textarea>
        </div>

        <div class="form-group">
          <button 
            type="submit" 
            class="submit-btn"
          >
            Submit Overtime Request
          </button>
        </div>
      </form>
    </div>
  </main>
</div>

<script>
  document.querySelector('form').addEventListener('submit', function(e) {
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    
    if (startTime && endTime && startTime >= endTime) {
      e.preventDefault();
      alert('End time must be after start time');
    }
  });
</script>

</body>
</html>
