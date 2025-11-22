CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    previous_price DECIMAL(10, 2) DEFAULT 0.00,
    unit VARCHAR(50) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-box',
    image_url VARCHAR(255) NULL,
    location VARCHAR(255) NULL,
    trend ENUM('up', 'down', 'neutral') DEFAULT 'neutral',
    price_change DECIMAL(10, 2) DEFAULT 0.00,
    tags TEXT NULL,
    status ENUM('active', 'pending', 'rejected') DEFAULT 'pending',
    updated_by INT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
