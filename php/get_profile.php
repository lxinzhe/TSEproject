<?php
session_start();
include('db_connect.php');
header('Content-Type: application/json');

if (!isset($_SESSION['employee_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit;
}
$employee_id = $_SESSION['employee_id'];
$sql = "SELECT avatar, first_name, last_name, employee_id, email, created_at FROM employees WHERE employee_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $employee_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    // 统计考勤率
    $att_sql = "SELECT COUNT(*) as total_days, SUM(CASE WHEN ClockInTime IS NOT NULL THEN 1 ELSE 0 END) as present_days FROM attendancerecord WHERE EmployeeID = ?";
    $att_stmt = $conn->prepare($att_sql);
    $att_stmt->bind_param('s', $employee_id);
    $att_stmt->execute();
    $att_result = $att_stmt->get_result();
    $att = $att_result->fetch_assoc();
    $attendance_rate = ($att['total_days'] > 0) ? round(($att['present_days'] / $att['total_days']) * 100) : 0;
    // 统计CPD小时数
    $cpd_sql = "SELECT SUM(TotalWorkHours) as cpd_hours FROM cpd_record WHERE Employee_ID = ?";
    $cpd_stmt = $conn->prepare($cpd_sql);
    $cpd_stmt->bind_param('s', $employee_id);
    $cpd_stmt->execute();
    $cpd_result = $cpd_stmt->get_result();
    $cpd = $cpd_result->fetch_assoc();
    $cpd_hours = $cpd['cpd_hours'] ? round($cpd['cpd_hours']) : 0;
    // 统计剩余假期（假设有leave_balance字段，或可用年假天数减去已请假天数）
    $leave_sql = "SELECT COUNT(*) as leave_count FROM leave_requests WHERE employee_id = ?";
    $leave_stmt = $conn->prepare($leave_sql);
    $leave_stmt->bind_param('s', $employee_id);
    $leave_stmt->execute();
    $leave_result = $leave_stmt->get_result();
    $leave = $leave_result->fetch_assoc();
    $default_leave = 12; // 假设每年12天
    $leave_balance = $default_leave - $leave['leave_count'];
    $row['attendance_rate'] = $attendance_rate;
    $row['cpd_hours'] = $cpd_hours;
    $row['leave_balance'] = $leave_balance;
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Profile not found"]);
} 