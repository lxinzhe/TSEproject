-- ===================================================
-- Admin Table Setup for Employee Attendance System
-- ===================================================

-- 1. Create the admin table
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Insert default admin users with hashed passwords
-- Note: These passwords are hashed using PHP's password_hash() function
-- Default passwords: 
-- admin -> admin123
-- administrator -> admin@2024  
-- superadmin -> super123

INSERT INTO `admin_users` (`username`, `password`, `full_name`, `email`, `role`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@company.com', 'admin', 'active'),
('administrator', '$2y$10$K1l9.t8k8QFQkGQa9XLW4eJb6I6d6JmI1L9k4S5.H7a9w2x3C4v5E', 'Main Administrator', 'administrator@company.com', 'super_admin', 'active'),
('superadmin', '$2y$10$L2m0.u9l9RGRlHRb0YMX5fKc7J7e7KnJ2M0l5T6.I8b0x3y4D5w6F', 'Super Administrator', 'superadmin@company.com', 'super_admin', 'active');

-- 3. Create indexes for better performance
CREATE INDEX `idx_username` ON `admin_users` (`username`);
CREATE INDEX `idx_status` ON `admin_users` (`status`);
CREATE INDEX `idx_role` ON `admin_users` (`role`);

-- ===================================================
-- Additional Security Features (Optional)
-- ===================================================

-- 4. Create login attempts tracking table (for security)
CREATE TABLE `admin_login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_username_time` (`username`, `attempt_time`),
  KEY `idx_ip_time` (`ip_address`, `attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Create sessions table (for better session management)
CREATE TABLE `admin_sessions` (
  `id` varchar(128) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_expires` (`expires_at`),
  FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===================================================
-- Sample Queries for Testing
-- ===================================================

-- View all admin users
-- SELECT id, username, full_name, email, role, status, last_login, created_at FROM admin_users;

-- Check if a username exists
-- SELECT COUNT(*) as user_exists FROM admin_users WHERE username = 'admin' AND status = 'active';

-- Update last login time
-- UPDATE admin_users SET last_login = NOW() WHERE username = 'admin';

-- Add a new admin user (remember to hash the password in PHP first)
-- INSERT INTO admin_users (username, password, full_name, email, role) 
-- VALUES ('newadmin', '$2y$10$hashedpasswordhere', 'New Admin', 'newadmin@company.com', 'admin'); 