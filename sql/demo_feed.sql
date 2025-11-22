-- Demo Data for SastoMahango

USE SastoMahango;

-- 1. Feed 10 Demo Contributors
-- Passwords are set to a placeholder hash (e.g., for 'password123') or plain text depending on your auth system. 
-- Assuming you might hash them later or use a default dev password.
INSERT INTO contributors (full_name, email, password, phone, last_login) VALUES
('Aarav Sharma', 'aarav.sharma@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9801111111', NOW()),
('Sita Adhikari', 'sita.adhikari@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9802222222', NOW()),
('Ramesh Gupta', 'ramesh.gupta@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9803333333', NOW()),
('Gita Paudel', 'gita.paudel@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9804444444', NOW()),
('Hari Bahadur', 'hari.bahadur@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9805555555', NOW()),
('Maya Sherpa', 'maya.sherpa@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9806666666', NOW()),
('Binod Chaudhary', 'binod.c@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9807777777', NOW()),
('Rita Thapa', 'rita.thapa@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9808888888', NOW()),
('Suresh Karki', 'suresh.karki@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9809999999', NOW()),
('Anita Gurung', 'anita.gurung@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9800000000', NOW());

-- 2. Feed 50 Demo Items
INSERT INTO items (name, category, unit, price, previous_price, icon, created_by, tags, status, views) VALUES
-- Vegetables
('Red Onion (Indian)', 'Vegetables', 'kg', 85.00, 80.00, 'fa-carrot', 1, 'onion,indian,daily', 'active', 150),
('Potato (Red)', 'Vegetables', 'kg', 65.00, 67.00, 'fa-carrot', 1, 'potato,red,daily', 'active', 120),
('Tomato (Local)', 'Vegetables', 'kg', 45.00, 40.00, 'fa-carrot', 2, 'tomato,local,daily', 'active', 200),
('Green Chili', 'Vegetables', 'kg', 120.00, 105.00, 'fa-pepper-hot', 3, 'spicy,chili', 'active', 80),
('Cauliflower (Local)', 'Vegetables', 'kg', 90.00, 95.00, 'fa-leaf', 1, 'cauliflower,fresh', 'active', 95),
('Cabbage', 'Vegetables', 'kg', 35.00, 35.00, 'fa-leaf', 2, 'cabbage,green', 'active', 60),
('Spinach (Saag)', 'Vegetables', 'bunch', 40.00, 45.00, 'fa-leaf', 4, 'green,leafy', 'active', 110),
('Ginger', 'Vegetables', 'kg', 220.00, 195.00, 'fa-mortar-pestle', 1, 'spice,ginger', 'active', 75),
('Garlic (Dry)', 'Vegetables', 'kg', 280.00, 270.00, 'fa-mortar-pestle', 2, 'spice,garlic', 'active', 85),
('Pumpkin', 'Vegetables', 'kg', 50.00, 50.00, 'fa-carrot', 3, 'yellow,vegetable', 'active', 40),

-- Fruits
('Apple (Fuji)', 'Fruits', 'kg', 320.00, 310.00, 'fa-apple-alt', 5, 'apple,imported,sweet', 'active', 300),
('Banana (Dozen)', 'Fruits', 'doz', 120.00, 120.00, 'fa-apple-alt', 5, 'banana,energy', 'active', 250),
('Orange (Local)', 'Fruits', 'kg', 110.00, 100.00, 'fa-lemon', 6, 'citrus,vitamin c', 'active', 180),
('Pomegranate', 'Fruits', 'kg', 350.00, 360.00, 'fa-apple-alt', 5, 'red,healthy', 'active', 90),
('Grapes (Black)', 'Fruits', 'kg', 280.00, 250.00, 'fa-apple-alt', 6, 'grapes,sweet', 'active', 130),
('Papaya', 'Fruits', 'kg', 80.00, 75.00, 'fa-apple-alt', 5, 'tropical,digestive', 'active', 70),
('Watermelon', 'Fruits', 'kg', 45.00, 50.00, 'fa-apple-alt', 6, 'summer,water', 'active', 220),
('Mango (Maldaha)', 'Fruits', 'kg', 150.00, 140.00, 'fa-apple-alt', 5, 'king of fruits,summer', 'pending', 50),
('Lemon (Local)', 'Fruits', 'pc', 15.00, 15.00, 'fa-lemon', 6, 'sour,vitamin c', 'active', 160),
('Pineapple', 'Fruits', 'pc', 180.00, 180.00, 'fa-apple-alt', 5, 'tropical,sweet', 'active', 85),

-- Grains & Cereals
('Basmati Rice (Premium)', 'Grains', '25kg', 2100.00, 2050.00, 'fa-rice', 7, 'rice,premium,staple', 'active', 400),
('Sona Mansuli Rice', 'Grains', '25kg', 1650.00, 1600.00, 'fa-rice', 7, 'rice,staple', 'active', 350),
('Wheat Flour (Atta)', 'Grains', 'kg', 65.00, 62.00, 'fa-wheat', 8, 'flour,roti', 'active', 300),
('Lentils (Masoor)', 'Grains', 'kg', 160.00, 165.00, 'fa-seedling', 7, 'dal,protein', 'active', 280),
('Chickpeas (Chana)', 'Grains', 'kg', 140.00, 135.00, 'fa-seedling', 8, 'protein,legume', 'active', 150),
('Beaten Rice (Chiura)', 'Grains', 'kg', 90.00, 90.00, 'fa-rice', 7, 'snack,staple', 'active', 120),
('Corn (Maize)', 'Grains', 'kg', 45.00, 40.00, 'fa-wheat', 8, 'corn,feed', 'active', 60),

-- Essentials (Oil, Salt, Sugar)
('Sunflower Oil', 'Essentials', 'liter', 240.00, 250.00, 'fa-oil-can', 9, 'oil,cooking', 'active', 500),
('Mustard Oil', 'Essentials', 'liter', 280.00, 280.00, 'fa-wine-bottle', 9, 'oil,traditional', 'active', 450),
('Sugar', 'Essentials', 'kg', 95.00, 93.00, 'fa-cube', 10, 'sweet,tea', 'active', 600),
('Salt (Aayo Nun)', 'Essentials', 'kg', 25.00, 25.00, 'fa-cube', 9, 'salt,iodine', 'active', 700),
('Ghee (Pure)', 'Essentials', 'liter', 1100.00, 1050.00, 'fa-wine-bottle', 10, 'dairy,fat', 'active', 180),

-- Meat & Dairy
('Chicken (Broiler)', 'Meat', 'kg', 380.00, 400.00, 'fa-drumstick-bite', 3, 'meat,protein', 'active', 800),
('Mutton (Khasi)', 'Meat', 'kg', 1300.00, 1250.00, 'fa-drumstick-bite', 3, 'meat,expensive', 'active', 400),
('Fish (Rohu)', 'Meat', 'kg', 450.00, 465.00, 'fa-fish', 4, 'fish,fresh', 'active', 250),
('Large Eggs (Crate)', 'Dairy', 'crate', 450.00, 450.00, 'fa-egg', 4, 'protein,breakfast', 'active', 550),
('Milk (Standard)', 'Dairy', 'liter', 110.00, 100.00, 'fa-wine-bottle', 3, 'dairy,calcium', 'active', 600),
('Paneer', 'Dairy', 'kg', 850.00, 830.00, 'fa-cheese', 4, 'dairy,veg protein', 'active', 300),
('Curd (Yogurt)', 'Dairy', 'liter', 140.00, 130.00, 'fa-wine-bottle', 3, 'dairy,probiotic', 'active', 200),

-- Construction
('Cement (OPC)', 'Construction', 'sack', 750.00, 760.00, 'fa-building', 1, 'build,strong', 'active', 150),
('Cement (PPC)', 'Construction', 'sack', 650.00, 650.00, 'fa-building', 1, 'build,standard', 'active', 100),
('Steel Rods (TMT)', 'Construction', 'kg', 105.00, 110.00, 'fa-hard-hat', 2, 'iron,strong', 'active', 120),
('Bricks (No. 1)', 'Construction', 'pc', 18.00, 18.00, 'fa-building', 1, 'red brick', 'active', 90),
('Sand', 'Construction', 'truck', 28000.00, 27500.00, 'fa-truck', 2, 'river sand', 'active', 60),

-- Energy
('LPG Gas', 'Energy', 'cyl', 1895.00, 1895.00, 'fa-gas-pump', 5, 'cooking gas', 'active', 900),
('Petrol', 'Energy', 'liter', 172.00, 172.00, 'fa-gas-pump', 5, 'fuel,vehicle', 'active', 1200),
('Diesel', 'Energy', 'liter', 160.00, 158.00, 'fa-gas-pump', 6, 'fuel,heavy', 'active', 800),

-- Tech (Just a few to show variety)
('iPhone 15 Pro', 'Tech', 'pc', 185000.00, 190000.00, 'fa-mobile-alt', 8, 'apple,phone,luxury', 'active', 500),
('Samsung S24 Ultra', 'Tech', 'pc', 175000.00, 175000.00, 'fa-mobile-alt', 8, 'android,flagship', 'active', 450);
