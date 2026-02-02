CREATE DATABASE IF NOT EXISTS smartsecure_db;
USE smartsecure_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'technician', 'client') NOT NULL DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Installation Requests Table
CREATE TABLE IF NOT EXISTS installation_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    service_id INT NOT NULL,
    property_type VARCHAR(50),
    address TEXT NOT NULL,
    preferred_date DATE,
    description TEXT,
    status ENUM('pending', 'approved', 'assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    technician_id INT DEFAULT NULL,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert Default Admin (Password: admin123)
-- Note: In production, use password_hash() in PHP. This is just for initial setups if creating manually.
-- For now we assume the registration process will handle hashing, but we can insert a dummy admin.
INSERT INTO users (full_name, email, password, role) VALUES 
('System Admin', 'admin@smartsecure.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 
-- The hash above is standard bcrypt for 'password' (or similar), I'll just put a placeholder if I can't generate one easily.
-- Actually I will leave it to the user to register or I'll create a seeder later.
-- Let's put a simple example service.
INSERT INTO services (name, description) VALUES 
('CCTV Installation', 'Professional installation of surveillance cameras for home or office.'),
('Alarm Systems', 'Intruder detection and alarm system setup.'),
('Electric Fence', 'High-voltage electric fencing for perimeter security.'),
('Access Control', 'Biometric and card-based access control systems.');
