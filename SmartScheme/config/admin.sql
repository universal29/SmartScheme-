ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user', 'admin') DEFAULT 'user';

-- Insert an admin user (password is 'password')
INSERT INTO users (full_name, email, mobile, password, is_verified, role) 
VALUES ('System Admin', 'admin@example.com', '9999999999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin')
ON DUPLICATE KEY UPDATE role='admin';
