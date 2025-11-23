# SastoMahango API Documentation

This directory contains the backend API endpoints for the SastoMahango application. These PHP scripts handle authentication, data retrieval, and database interactions.

---

## 1. Authentication APIs

### `validateUser.php`
**Function:**  
Handles user login requests. It verifies the provided username and password against the database. If valid, it starts a PHP session.

**Working Mechanism:**
1.  Accepts a POST request with JSON data containing `username` and `password`.
2.  Sanitizes the input.
3.  Queries the `contributors` table for the username.
4.  Verifies the password using `password_verify()` (supports Bcrypt hashes).
5.  If successful:
    *   Regenerates the session ID for security.
    *   Sets `$_SESSION['user_id']` and `$_SESSION['user_name']`.
    *   Returns a JSON success response.
6.  If failed, returns a JSON error response.

**Endpoint:** `POST /API/validateUser.php`
**Input:**
```json
{
  "username": "user1",
  "password": "password123"
}
```
**Output (Success):**
```json
{
  "success": true,
  "message": "Login successful"
}
```

---

### `getLoggedDetails.php`
**Function:**  
Checks if a user is currently logged in and returns their session details. Used by the frontend to persist login state and display user information.

**Working Mechanism:**
1.  Starts the session.
2.  Checks if `$_SESSION['user_id']` is set.
3.  If set, returns the user's ID and name.
4.  If not set, returns `loggedIn: false`.

**Endpoint:** `GET /API/getLoggedDetails.php`
**Output (Logged In):**
```json
{
  "loggedIn": true,
  "user_id": 1,
  "user_name": "John Doe"
}
```

---

### `logout.php`
**Function:**  
Logs the user out by destroying the current session.

**Working Mechanism:**
1.  Starts the session.
2.  Unsets all session variables.
3.  Destroys the session.
4.  Returns a success message.

**Endpoint:** `POST /API/logout.php` (or GET)

---

### `getContributors.php`
**Function:**  
Retrieves a list of all contributors registered on the platform.

**Working Mechanism:**
1.  Queries the `contributors` table.
2.  Selects `id`, `full_name`, and `email`.
3.  Returns a JSON array of contributor objects.

**Endpoint:** `GET /API/getContributors.php`
**Output:**
```json
[
  {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  ...
]
```

---

## 2. Item Management APIs

### `getItemList.php`
**Function:**  
Retrieves a list of items from the database with support for pagination, sorting, and fetching specific ranges.

**Working Mechanism:**
1.  Accepts GET parameters for `index` (start position), `count` (number of items), and `order` (direction).
2.  Calculates the SQL `LIMIT` and `OFFSET` based on the parameters.
3.  Fetches items from the `items` table, joining with `contributors` to get the creator's name.
4.  Sorts results by Views (Descending) and ID (Ascending).
5.  Returns a JSON array of item objects.

**Endpoint:** `GET /API/getItemList.php`
**Parameters:**
*   `index`: Starting index (integer) or 'last'. Default: 0.
*   `count`: Number of items to fetch (integer) or 'all'. Default: 10.
*   `order`: 1 for forward, -1 for backward. Default: 1.

**Example:** `/API/getItemList.php?index=0&count=5&order=1`

---

### `itemViewer.php`
**Function:**  
Increments the view count for a specific item.

**Working Mechanism:**
1.  Accepts an `id` parameter via GET.
2.  Updates the `items` table, incrementing the `views` column by 1 for the matching ID.
3.  Explicitly prevents the `last_updated` timestamp from changing during this operation.
4.  Returns success or failure status.

**Endpoint:** `GET /API/itemViewer.php`
**Parameters:**
*   `id`: The ID of the item to view.

---

### `updateItem.php`
**Function:**  
Updates an existing item's details in the database. It handles price history tracking and records who modified the item.

**Working Mechanism:**
1.  **Authentication Check:** Verifies if the user is logged in via PHP Session. Returns error if not.
2.  **Input Parsing:** Accepts a JSON payload with item details (`id`, `name`, `price`, etc.).
3.  **Price History:**
    *   Fetches the current price from the database.
    *   If the price has changed, the old price is moved to `previous_price`.
4.  **Database Update:**
    *   Updates item fields (name, category, unit, price, icon, tags).
    *   Sets `last_updated` to the current timestamp.
    *   Sets `modified_by` to the logged-in user's ID.
5.  Returns a JSON success or failure message.

**Endpoint:** `POST /API/updateItem.php`
**Input:**
```json
{
  "id": 1,
  "name": "Organic Apple",
  "category": "Fruits",
  "unit": "kg",
  "price": 120.50,
  "icon": "fa-apple-alt",
  "tags": "fresh, organic"
}
```
**Output (Success):**
```json
{
  "success": true,
  "message": "Item updated successfully"
}
```

---

### `uploadItem.php`
**Function:**  
*Under Construction* - Intended to handle the uploading of new items to the database.
**Current State:** Empty file.

---

## 3. Utility & Development

### `conn.php`
**Function:**  
Establishes the connection to the MySQL database.

**Working Mechanism:**
1.  Defines database credentials (`localhost`, `root`, ``, `SastoMahango`).
2.  Creates a `mysqli` connection object.
3.  Sets the character set to `utf8mb4`.
4.  Included by other API files to access the `$conn` variable.

---

### `debug_login.php`
**Function:**  
A development tool used to debug login issues.

**Working Mechanism:**
1.  Accepts a username and password via GET parameters.
2.  Prints detailed information about the user record in the database.
3.  Displays the stored hash and the result of `password_verify()`.
4.  **Note:** Should be removed or restricted in a production environment.
