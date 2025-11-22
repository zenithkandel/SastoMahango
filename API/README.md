# SastoMahango API Guide

This API allows you to fetch market item data with flexible pagination, sorting, and filtering options.

## Base URL
```
http://localhost/projects/SastoMahango/API/getItemList.php
```

## Method
**GET**

## Parameters

| Parameter | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| **`index`** | `Integer` or `String` | `0` | The starting point for fetching items. <br> - Use an integer (e.g., `0`, `5`) for a specific index (0-based). <br> - Use `"last"` to target the very last item in the database. |
| **`count`** | `Integer` or `String` | `10` | The number of items to retrieve. <br> - Use an integer (e.g., `10`) to get a specific number of items. <br> - Use `"all"` to retrieve **every** item in the database. |
| **`order`** | `Integer` | `1` | The direction of fetching. <br> - `1`: **Forward** (Start at `index` and go down). <br> - `-1`: **Backward** (End at `index` and go up). |

---

## Usage Examples

### 1. Get Top 10 Popular Items (Default)
Fetches the first 10 items, sorted by popularity (Views).
- **URL:** `.../getItemList.php?index=0&count=10&order=1`
- **Simplified:** `.../getItemList.php`

### 2. Get All Items
Retrieves the entire dataset.
- **URL:** `.../getItemList.php?count=all`

### 3. Get Next 5 Items (Pagination)
Useful for "Load More" functionality. If you already have 10 items, start at index 10.
- **URL:** `.../getItemList.php?index=10&count=5&order=1`

### 4. Get Last 3 Items
Fetches the 3 least popular items (or bottom of the list).
- **URL:** `.../getItemList.php?index=last&count=3&order=-1`

### 5. Get Specific Range (Backward)
Fetches 4 items ending at index 9 (i.e., indices 6, 7, 8, 9).
- **URL:** `.../getItemList.php?index=9&count=4&order=-1`

---

## JavaScript Example

```javascript
// Fetch top 10 items
fetch('http://localhost/projects/SastoMahango/API/getItemList.php?count=10')
    .then(response => response.json())
    .then(data => {
        console.log(data);
    })
    .catch(error => console.error('Error:', error));
```

## Response Format (JSON)

The API returns an array of item objects.

```json
[
    {
        "id": 1,
        "name": "Red Onion (Indian)",
        "category": "Vegetables",
        "unit": "kg",
        "price": 85,
        "previous_price": 80,
        "change": 5,
        "trend": "up",
        "icon": "fa-carrot",
        "created_by": "Aarav Sharma",
        "tags": [
            "onion",
            "indian",
            "daily"
        ],
        "status": "active",
        "views": 150,
        "last_updated": "2023-10-27 10:30:00"
    },
    ...
]
```

---

# Item View Updater API

This API increments the view count for a specific item.

## Base URL
```
http://localhost/projects/SastoMahango/API/itemViewer.php
```

## Method
**GET**

## Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| **`id`** | `Integer` | **Yes** | The unique ID of the item to update. |

## Usage Example

### Increment View Count for Item ID 5
- **URL:** `.../itemViewer.php?id=5`

## Response Format (JSON)

```json
{
    "success": true,
    "message": "View count updated"
}
```
