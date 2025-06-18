-- ===================================================
-- Database Updates for Admin Approval Workflow
-- ===================================================

-- 1. Update leave_requests table to support admin approval workflow
ALTER TABLE `leave_requests` 
ADD COLUMN `employee_id` varchar(20) DEFAULT NULL AFTER `id`,
ADD COLUMN `status` enum('pending','approved','rejected') DEFAULT 'pending' AFTER `reason`,
ADD COLUMN `reviewed_by` varchar(50) DEFAULT NULL AFTER `status`,
ADD COLUMN `reviewed_at` timestamp NULL DEFAULT NULL AFTER `reviewed_by`;

-- Add foreign key constraint for employee_id (optional)
-- ALTER TABLE `leave_requests` 
-- ADD CONSTRAINT `fk_leave_employee` 
-- FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) 
-- ON DELETE SET NULL ON UPDATE CASCADE;

-- 2. Update overtime_requests table to support admin approval workflow
ALTER TABLE `overtime_requests` 
ADD COLUMN `employee_id` varchar(20) DEFAULT NULL AFTER `id`,
ADD COLUMN `status` enum('pending','approved','rejected') DEFAULT 'pending' AFTER `reason`,
ADD COLUMN `reviewed_by` varchar(50) DEFAULT NULL AFTER `status`,
ADD COLUMN `reviewed_at` timestamp NULL DEFAULT NULL AFTER `reviewed_by`;

-- Add foreign key constraint for employee_id (optional)
-- ALTER TABLE `overtime_requests` 
-- ADD CONSTRAINT `fk_overtime_employee` 
-- FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) 
-- ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Update existing records with default values (if any exist)
UPDATE `leave_requests` SET `status` = 'pending' WHERE `status` IS NULL;
UPDATE `overtime_requests` SET `status` = 'pending' WHERE `status` IS NULL;

-- 4. Create indexes for better performance
CREATE INDEX `idx_leave_status` ON `leave_requests` (`status`);
CREATE INDEX `idx_leave_employee` ON `leave_requests` (`employee_id`);
CREATE INDEX `idx_overtime_status` ON `overtime_requests` (`status`);
CREATE INDEX `idx_overtime_employee` ON `overtime_requests` (`employee_id`);

-- ===================================================
-- Sample Data for Testing (Optional)
-- ===================================================

-- Insert sample leave request for testing
-- INSERT INTO `leave_requests` (`employee_id`, `leave_type`, `start_date`, `end_date`, `reason`, `status`) 
-- VALUES ('1', 'sick', '2025-01-15', '2025-01-16', 'Medical appointment', 'pending');

-- Insert sample overtime request for testing
-- INSERT INTO `overtime_requests` (`employee_id`, `ot_date`, `start_time`, `end_time`, `total_hours`, `reason`, `department`, `status`) 
-- VALUES ('1', '2025-01-10', '18:00:00', '21:00:00', 3.00, 'Project deadline', 'IT', 'pending');

-- ===================================================
-- View Sample Queries
-- ===================================================

-- View all pending leave requests
-- SELECT lr.*, e.name as employee_name 
-- FROM leave_requests lr 
-- LEFT JOIN employees e ON lr.employee_id = e.employee_id 
-- WHERE lr.status = 'pending'
-- ORDER BY lr.submitted_at DESC;

-- View all pending overtime requests
-- SELECT or.*, e.name as employee_name 
-- FROM overtime_requests or 
-- LEFT JOIN employees e ON or.employee_id = e.employee_id 
-- WHERE or.status = 'pending'
-- ORDER BY or.created_at DESC;

-- Count requests by status
-- SELECT status, COUNT(*) as count FROM leave_requests GROUP BY status;
-- SELECT status, COUNT(*) as count FROM overtime_requests GROUP BY status; 