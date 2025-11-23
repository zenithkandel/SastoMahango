# Admin API Documentation

This folder contains APIs for administrative tasks, specifically for managing contributors.

## Base URL
`/API/admin/`

## Endpoints

### 1. Get Contributors
Retrieves a list of all contributors.

- **URL:** `getContributors.php`
- **Method:** `GET`
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 1,
        "full_name": "John Doe",
        "email": "john@example.com",
        "phone": "1234567890",
        "last_login": "2023-10-27 10:00:00"
      }
    ]
  }
  ```

### 2. Update Contributor
Updates an existing contributor's details.

- **URL:** `updateContributor.php`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
    "id": 1,
    "full_name": "John Doe Updated",
    "email": "john.updated@example.com",
    "phone": "9876543210"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Contributor updated successfully"
  }
  ```

### 3. Delete Contributor
Deletes a contributor from the system.

- **URL:** `deleteContributor.php`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
    "id": 1
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Contributor deleted successfully"
  }
  ```
