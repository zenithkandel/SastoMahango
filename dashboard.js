// Mock Data (Same as market.html)
const marketItems = [
    { id: 1, name: "Red Onion (Indian)", category: "Vegetables", price: 85.00, unit: "kg", trend: "down", change: 5.00, icon: "fa-carrot" },
    { id: 2, name: "Apple (Fuji)", category: "Fruits", price: 320.00, unit: "kg", trend: "up", change: 10.00, icon: "fa-apple-alt" },
    { id: 3, name: "Large Eggs (Crate)", category: "Dairy", price: 450.00, unit: "crate", trend: "neutral", change: 0.00, icon: "fa-egg" },
    { id: 4, name: "Basmati Rice (Premium)", category: "Grains", price: 2100.00, unit: "25kg", trend: "up", change: 50.00, icon: "fa-rice" },
    { id: 5, name: "Sunflower Oil", category: "Essentials", price: 240.00, unit: "liter", trend: "down", change: 10.00, icon: "fa-oil-can" },
    { id: 6, name: "Green Chili", category: "Vegetables", price: 120.00, unit: "kg", trend: "up", change: 15.00, icon: "fa-pepper-hot" },
    { id: 7, name: "Lemon (Local)", category: "Fruits", price: 15.00, unit: "pc", trend: "neutral", change: 0.00, icon: "fa-lemon" },
    { id: 8, name: "Chicken (Broiler)", category: "Meat", price: 380.00, unit: "kg", trend: "down", change: 20.00, icon: "fa-drumstick-bite" },
    { id: 9, name: "Sugar", category: "Essentials", price: 95.00, unit: "kg", trend: "up", change: 2.00, icon: "fa-cube" },
    { id: 10, name: "Spinach (Saag)", category: "Vegetables", price: 40.00, unit: "bunch", trend: "down", change: 5.00, icon: "fa-leaf" },
    { id: 11, name: "LPG Gas", category: "Energy", price: 1895.00, unit: "cyl", trend: "neutral", change: 0.00, icon: "fa-gas-pump" },
    { id: 12, name: "Wheat Flour (Atta)", category: "Grains", price: 65.00, unit: "kg", trend: "up", change: 3.00, icon: "fa-wheat" },
    { id: 13, name: "Fish (Rohu)", category: "Meat", price: 450.00, unit: "kg", trend: "down", change: 15.00, icon: "fa-fish" },
    { id: 14, name: "Ginger", category: "Vegetables", price: 220.00, unit: "kg", trend: "up", change: 25.00, icon: "fa-mortar-pestle" },
    { id: 15, name: "Mustard Oil", category: "Essentials", price: 280.00, unit: "liter", trend: "neutral", change: 0.00, icon: "fa-wine-bottle" },
    { id: 16, name: "Lentils (Masoor)", category: "Grains", price: 160.00, unit: "kg", trend: "down", change: 5.00, icon: "fa-seedling" },
    { id: 17, name: "Paneer", category: "Dairy", price: 850.00, unit: "kg", trend: "up", change: 20.00, icon: "fa-cheese" },
    { id: 18, name: "Potato (Red)", category: "Vegetables", price: 65.00, unit: "kg", trend: "down", change: 2.00, icon: "fa-carrot" },
    { id: 19, name: "Banana (Dozen)", category: "Fruits", price: 120.00, unit: "doz", trend: "neutral", change: 0.00, icon: "fa-apple-alt" },
    { id: 20, name: "Cement (OPC)", category: "Construction", price: 750.00, unit: "sack", trend: "down", change: 10.00, icon: "fa-building" }
];

// DOM Elements
const itemsGrid = document.getElementById('itemsGrid');
const searchInput = document.getElementById('dashboardSearch');
const editModal = document.getElementById('editModal');
const closeModalBtn = document.getElementById('closeModal');
const cancelEditBtn = document.getElementById('cancelEdit');
const editForm = document.getElementById('editForm');

// Add Item Elements
const addModal = document.getElementById('addModal');
const addNewBtn = document.getElementById('addNewBtn');
const closeAddModalBtn = document.getElementById('closeAddModal');
const cancelAddBtn = document.getElementById('cancelAdd');
const addForm = document.getElementById('addForm');

// Render Items
function renderItems(items) {
    itemsGrid.innerHTML = '';
    items.forEach(item => {
        const card = document.createElement('div');
        card.className = 'item-card reveal';
        card.onclick = () => openEditModal(item);

        let trendIcon = 'fa-minus';
        let trendClass = 'neutral';
        if (item.trend === 'up') { trendIcon = 'fa-arrow-up'; trendClass = 'up'; }
        if (item.trend === 'down') { trendIcon = 'fa-arrow-down'; trendClass = 'down'; }

        card.innerHTML = `
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas ${item.icon}"></i>
                </div>
                <span class="card-badge">${item.unit}</span>
            </div>
            <h3 class="card-title">${item.name}</h3>
            <p class="card-category">${item.category}</p>
            <div class="card-price-section">
                <div>
                    <span class="price-label">Current Price</span>
                    <span class="current-price">Rs. ${item.price.toFixed(2)}</span>
                </div>
                <div class="trend-indicator ${trendClass}">
                    <i class="fas ${trendIcon}"></i> Rs. ${item.change.toFixed(2)}
                </div>
            </div>
        `;
        itemsGrid.appendChild(card);
    });
    
    // Re-trigger animations if needed
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('active');
            });
        });
        document.querySelectorAll('.item-card').forEach(el => observer.observe(el));
    }
}

// Initial Render
renderItems(marketItems);

// Search Functionality
searchInput.addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    const filtered = marketItems.filter(item => 
        item.name.toLowerCase().includes(term) || 
        item.category.toLowerCase().includes(term)
    );
    renderItems(filtered);
});

// Modal Logic
function openEditModal(item) {
    document.getElementById('editItemId').value = item.id;
    document.getElementById('editItemName').value = item.name;
    document.getElementById('editItemCategory').value = item.category;
    document.getElementById('editItemUnit').value = item.unit;
    document.getElementById('editItemPrice').value = item.price;
    document.getElementById('editItemPrevPrice').value = (item.price - (item.trend === 'up' ? item.change : -item.change)).toFixed(2);
    
    editModal.classList.add('active');
}

function closeModal() {
    editModal.classList.remove('active');
}

closeModalBtn.addEventListener('click', closeModal);
cancelEditBtn.addEventListener('click', closeModal);

// Close on outside click
editModal.addEventListener('click', (e) => {
    if (e.target === editModal) closeModal();
});

// Handle Form Submit
editForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const id = parseInt(document.getElementById('editItemId').value);
    const newPrice = parseFloat(document.getElementById('editItemPrice').value);
    const prevPrice = parseFloat(document.getElementById('editItemPrevPrice').value);
    
    // Update Data
    const itemIndex = marketItems.findIndex(i => i.id === id);
    if (itemIndex > -1) {
        const item = marketItems[itemIndex];
        item.price = newPrice;
        
        // Calculate new trend
        const diff = newPrice - prevPrice;
        item.change = Math.abs(diff);
        if (diff > 0) item.trend = 'up';
        else if (diff < 0) item.trend = 'down';
        else item.trend = 'neutral';
        
        // Re-render
        renderItems(marketItems);
        closeModal();
        
        // Show simple alert (in real app, show toast)
        alert(`Updated ${item.name} successfully!`);
    }
});

// Add Item Logic
function openAddModal() {
    addForm.reset();
    addModal.classList.add('active');
}

function closeAddModal() {
    addModal.classList.remove('active');
}

addNewBtn.addEventListener('click', openAddModal);
closeAddModalBtn.addEventListener('click', closeAddModal);
cancelAddBtn.addEventListener('click', closeAddModal);

// Close on outside click
addModal.addEventListener('click', (e) => {
    if (e.target === addModal) closeAddModal();
});

// Handle Add Form Submit
addForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const name = document.getElementById('addItemName').value;
    const category = document.getElementById('addItemCategory').value;
    const unit = document.getElementById('addItemUnit').value;
    const price = parseFloat(document.getElementById('addItemPrice').value);
    const icon = document.getElementById('addItemIcon').value || 'fa-box';
    
    const newItem = {
        id: marketItems.length + 1, // Simple ID generation
        name: name,
        category: category,
        price: price,
        unit: unit,
        trend: 'neutral',
        change: 0.00,
        icon: icon
    };
    
    marketItems.unshift(newItem); // Add to top
    renderItems(marketItems);
    closeAddModal();
    
    alert(`Added ${name} successfully!`);
});
