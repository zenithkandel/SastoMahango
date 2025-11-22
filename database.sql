CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-box',
    previous_price DECIMAL(10, 2) DEFAULT 0.00,
    created_by INT,
    modified_by INT,
    tags TEXT,
    status ENUM('active', 'pending', 'rejected') DEFAULT 'pending',
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
