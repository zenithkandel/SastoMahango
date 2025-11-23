# SastoMahango 🛒

SastoMahango is a comprehensive market price monitoring and tracking system. It allows users to view current market prices of essential goods, while enabling a community of contributors to update prices and add new items. Administrators oversee the system, verifying all changes to ensure data accuracy.

## 🚀 Features

### 🌍 Public Access
- **Live Market Rates**: View real-time prices of vegetables, fruits, grains, and other essentials.
- **Price Trends**: Visual indicators (⬆️/⬇️) showing if prices have increased or decreased compared to the previous update.
- **Search & Filter**: Instantly find items by name or category.
- **Dark Mode**: Fully supported dark theme for better visibility.

### 🤝 Contributors
- **Dashboard**: Personalized dashboard to manage contributions.
- **Update Items**: Propose price changes or updates to item details.
- **Add Items**: Submit requests for new items to be added to the market list.
- **Status Tracking**: See the status of submitted items (Pending/Approved).

### 🛡️ Administration
- **Admin Dashboard**: Centralized hub for system management.
- **Verification System**:
  - **Update Requests**: Review, approve, or reject price/detail changes from contributors.
  - **New Item Requests**: Verify and approve new items before they go live.
- **User Management**: Manage contributor accounts.
- **Item Management**: Direct CRUD operations on the inventory.
- **Audit Logs**: Track who modified what and when.

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3 (Custom Properties), JavaScript (ES6+).
- **Backend**: PHP (RESTful API architecture).
- **Database**: MySQL.
- **Icons**: FontAwesome.

## 📂 Project Structure

```
SastoMahango/
├── admin/                  # Admin-specific pages (Verification views)
├── API/                    # Backend Logic
│   ├── admin/              # Admin-protected endpoints
│   ├── conn.php            # Database connection
│   └── ...                 # Public/Contributor endpoints
├── css/                    # Global Styles
├── sql/                    # Database schemas
├── admin-dashboard.html    # Main Admin Interface
├── dashboard.html          # Contributor Interface
├── index.html              # Landing Page
├── market.html             # Public Market View
└── ...
```

## ⚙️ Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/zenithkandel/SastoMahango.git
    cd SastoMahango
    ```

2.  **Server Setup**
    - Ensure you have a PHP environment set up (e.g., XAMPP, WAMP, LAMP).
    - Move the project folder to your web server's root directory (e.g., `htdocs` or `www`).

3.  **Database Configuration**
    - Open `phpMyAdmin` or your preferred SQL client.
    - Create a new database named `sastomahango` (or your preferred name).
    - Import the schema from `sql/database.sql`.
    - (Optional) Import `sql/demo_feed.sql` for dummy data.

4.  **Connect Database**
    - Open `API/conn.php`.
    - Update the credentials if necessary:
      ```php
      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "sastomahango";
      ```

5.  **Run the Application**
    - Open your browser and navigate to: `http://localhost/SastoMahango/`

## 📖 API Documentation

Detailed documentation for the backend APIs can be found in the [API Directory](API/admin/README.md).

## 👥 User Roles

- **Guest**: Can view market prices.
- **Contributor**: Can suggest edits and add items.
- **Admin**: Has full control over the system and data integrity.

## 📄 License

This project is open-source and available for educational and personal use.
