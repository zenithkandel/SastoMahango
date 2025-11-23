# Admin API Documentation

This folder contains APIs for administrative tasks, specifically for managing contributors and items.

## Base URL
`/API/admin/`

## Authentication
All endpoints require an active Admin Session. If the session is invalid, the API returns:
```json
{
  "success": false,
  "message": "Unauthorized access. Please login as admin.",
  "redirect": "../login.html"
}
```

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

### 2. Add Contributor
Creates a new contributor account.

- **URL:** `addContributor.php`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
    "full_name": "Jane Doe",
    "email": "jane@example.com",
    "password": "securePassword123",
    "phone": "9800000000"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Contributor added successfully",
    "id": 2
  }
  ```

### 3. Update Contributor
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

### 4. Delete Contributor
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

### 5. Get Items
Retrieves a list of all items in the database.

- **URL:** `getItems.php`
- **Method:** `GET`
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 101,
        "name": "Tomato Big",
        "category": "Vegetables",
        "price": "85.00",
        "unit": "kg",
        "icon": "fa-carrot",
        "status": "active",
        "views": 120,
        "last_updated": "2023-11-23 10:00:00"
      }
    ]
  }
  ```

### 6. Add Item
Directly adds a new item to the database (Admin only).

- **URL:** `addItem.php`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
    "name": "Cauliflower",
    "category": "Vegetables",
    "price": 60,
    "unit": "kg",
    "icon": "fa-leaf",
    "status": "active"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Item added successfully",
    "id": 102
  }
  ```

### 7. Update Item
Updates an existing item. Automatically archives the old price to `previous_price`.

- **URL:** `updateItem.php`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
    "id": 101,
    "name": "Tomato Big",
    "category": "Vegetables",
    "price": 90,
    "unit": "kg",
    "icon": "fa-carrot",
    "status": "active"
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Item updated successfully"
  }
  ```

### 8. Delete Item
Permanently removes an item from the database.

- **URL:** `deleteItem.php`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
    "id": 101
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Item deleted successfully"
  }
  ```

### 9. Get Update Requests
Retrieves a list of pending item update requests (both modifications and new items) submitted by contributors.

- **URL:** `getUpdateRequests.php`
- **Method:** `GET`
- **Response:**
  ```json
  {
    "success": true,
    "data": [
      {
        "id": 5,
        "targetID": 101,
        "type": "update",
        "name": "Tomato Big",
        "price": 95.00,
        "contributor_name": "John Doe",
        "current_item": {
            "name": "Tomato Big",
            "price": 90.00
        },
        "created_at": "2023-11-23 12:00:00"
      },
      {
        "id": 6,
        "targetID": 0,
        "type": "create",
        "name": "New Apple",
        "price": 200.00,
        "contributor_name": "Jane Doe",
        "current_item": null,
        "created_at": "2023-11-23 12:30:00"
      }
    ]
  }
  ```

### 10. Approve Update Request
Approves a pending request. If it's an update, it modifies the existing item. If it's a new item request, it creates the item in the database.

- **URL:** `approveUpdate.php`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
    "id": 5
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Request approved and item updated successfully" 
    // OR "Request approved and new item created successfully"
  }
  ```

### 11. Reject Update Request
Rejects a pending request, removes it from the queue, and logs the rejection to `admin.log`.

- **URL:** `rejectUpdate.php`
- **Method:** `POST`
- **Body (JSON):**
  ```json
  {
    "id": 5
  }
  ```
- **Response:**
  ```json
  {
    "success": true,
    "message": "Request rejected successfully"
  }
  ```

### 12. Get Pending Counts
Retrieves the counts of pending update requests and new item requests for notification badges.

- **URL:** `getPendingCounts.php`
- **Method:** `GET`
- **Response:**
  ```json
  {
    "success": true,
    "counts": {
      "updates": 3,
      "new_items": 5
    }
  }
  ```
