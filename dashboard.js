// State Management
let currentItems = [];
let currentIndex = 0;
const BATCH_SIZE = 20;
const API_URL = 'API/getItemList.php';

// --- Auth Check ---
async function checkAuth() {
    try {
        const response = await fetch('API/getLoggedDetails.php');
        const data = await response.json();

        if (!data.isLoggedIn) {
            window.location.href = 'login.html';
        } else {
            // Update User Profile UI
            const userNameEl = document.querySelector('.user-name');
            const userAvatarEl = document.querySelector('.user-avatar');
            
            if (userNameEl) userNameEl.textContent = data.user_name;
            if (userAvatarEl) {
                userAvatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(data.user_name)}&background=3b82f6&color=fff`;
            }
        }
    } catch (error) {
        console.error('Auth check failed:', error);
        // Optional: Redirect on error or allow to stay (safer to redirect if strict)
        // window.location.href = 'login.html';
    }
}
checkAuth();

// DOM Elements
const itemsGrid = document.getElementById('itemsGrid');
const searchInput = document.getElementById('dashboardSearch');
const editModal = document.getElementById('editModal');
const closeModalBtn = document.getElementById('closeModal');
const cancelEditBtn = document.getElementById('cancelEdit');
const editForm = document.getElementById('editForm');
const logoutBtn = document.querySelector('.btn-logout');

// Logout Logic
if (logoutBtn) {
    logoutBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        try {
            const response = await fetch('API/logout.php', { method: 'POST' });
            const data = await response.json();
            if (data.success) {
                sessionStorage.removeItem('isLoggedIn');
                window.location.href = data.redirect;
            }
        } catch (error) {
            console.error('Logout failed:', error);
            window.location.href = 'login.html';
        }
    });
}

// Add Item Elements
const addModal = document.getElementById('addModal');
const addNewBtn = document.getElementById('addNewBtn');
const closeAddModalBtn = document.getElementById('closeAddModal');
const cancelAddBtn = document.getElementById('cancelAdd');
const addForm = document.getElementById('addForm');

// Pagination Buttons
const loadMoreBtn = document.getElementById('loadMoreBtn');
const loadAllBtn = document.getElementById('loadAllBtn');
const paginationContainer = document.querySelector('.pagination-container');

// --- API Functions ---

async function fetchItems(index, count, order = 1) {
    try {
        const url = `${API_URL}?index=${index}&count=${count}&order=${order}`;
        const response = await fetch(url);
        if (!response.ok) throw new Error('Network response was not ok');
        
        const text = await response.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON received:', text.substring(0, 200));
            if (text.trim().startsWith('<?php') || text.trim().startsWith('<')) {
                alert("⚠️ Error: PHP is not executing.\n\nPlease make sure you are accessing this site via 'http://localhost/projects/SastoMahango/' and NOT by opening the file directly or using VS Code Live Server.");
            }
            throw new Error('Server response was not valid JSON');
        }
    } catch (error) {
        console.error('Error fetching items:', error);
        return [];
    }
}

async function loadItems(isAppend = false, loadAll = false) {
    // Show loading state (optional UI enhancement)
    if (!isAppend) itemsGrid.innerHTML = '<p style="text-align:center; width:100%;">Loading...</p>';
    
    let data = [];
    if (loadAll) {
        data = await fetchItems(0, 'all', 1);
        currentIndex = data.length; // Update index to end
        // Hide buttons since we loaded everything
        hidePagination();
    } else {
        data = await fetchItems(currentIndex, BATCH_SIZE, 1);
        currentIndex += data.length;
        
        // Hide buttons if no more data returned
        if (data.length < BATCH_SIZE) {
            hidePagination();
        } else {
            showPagination();
        }
    }

    if (isAppend) {
        currentItems = [...currentItems, ...data];
    } else {
        currentItems = data;
    }

    renderItems(currentItems);
}

function hidePagination() {
    if (paginationContainer) paginationContainer.style.display = 'none';
}

function showPagination() {
    if (paginationContainer) paginationContainer.style.display = 'flex';
}

// --- Render Logic ---

function renderItems(items) {
    itemsGrid.innerHTML = '';
    
    if (items.length === 0) {
        itemsGrid.innerHTML = '<p style="text-align:center; width:100%; color:var(--text-secondary);">No items found.</p>';
        return;
    }

    items.forEach(item => {
        const card = document.createElement('div');
        card.className = 'item-card reveal';
        card.onclick = () => openEditModal(item);

        let trendIcon = 'fa-minus';
        let trendClass = 'neutral';
        if (item.trend === 'up') { trendIcon = 'fa-arrow-up'; trendClass = 'up'; }
        if (item.trend === 'down') { trendIcon = 'fa-arrow-down'; trendClass = 'down'; }

        card.innerHTML = `
            <div class="card-main-content">
                <div class="card-icon-section">
                    <div class="card-icon-wrapper">
                        <i class="fas ${item.icon || 'fa-box'}"></i>
                        <span class="status-dot status-${item.status}" title="Status: ${item.status}"></span>
                    </div>
                </div>
                
                <div class="card-info-section">
                    <div class="card-header-line">
                        <h3 class="card-title">${item.name}</h3>
                        <span class="card-id-badge">#${item.id}</span>
                    </div>
                    <p class="card-category">${item.category}</p>
                    
                    <div class="card-price-compact">
                        <span class="price-currency">Rs.</span>
                        <span class="price-amount">${parseFloat(item.price).toFixed(2)}</span>
                        <span class="price-unit">/${item.unit}</span>
                        <span class="trend-pill ${trendClass} small-trend">
                            <i class="fas ${trendIcon}"></i> ${parseFloat(item.change).toFixed(2)}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-meta-footer">
                <div class="meta-item" title="Total Views">
                    <i class="fas fa-eye"></i> <span>${item.views}</span>
                </div>
                <div class="meta-item" title="Created By">
                    <i class="fas fa-user-circle"></i> <span>${item.created_by || 'Unknown'}</span>
                </div>
                <div class="meta-item" title="Last Updated">
                    <i class="fas fa-clock"></i> <span>${formatDateShort(item.last_updated)}</span>
                </div>
            </div>
        `;
        itemsGrid.appendChild(card);
    });
    
    // Re-trigger animations
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('active');
            });
        });
        document.querySelectorAll('.item-card').forEach(el => observer.observe(el));
    }
}

// --- Event Listeners ---

// Initial Load
loadItems(false);

// Load More Button
loadMoreBtn.addEventListener('click', () => {
    loadItems(true, false);
});

// Load All Button
loadAllBtn.addEventListener('click', () => {
    loadItems(false, true);
});

// Search Functionality (Client-side filtering of loaded items)
searchInput.addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    const filtered = currentItems.filter(item => 
        item.name.toLowerCase().includes(term) || 
        item.category.toLowerCase().includes(term)
    );
    renderItems(filtered);
});

// Modal Logic
const editItemIconInput = document.getElementById('editItemIcon');
const editItemIconPreview = document.getElementById('editItemIconPreview');

function openEditModal(item) {
    document.getElementById('editItemId').value = item.id;
    document.getElementById('editItemName').value = item.name;
    document.getElementById('editItemCategory').value = item.category;
    document.getElementById('editItemUnit').value = item.unit;
    document.getElementById('editItemPrice').value = item.price;
    
    // Icon
    const iconClass = item.icon || 'fa-box';
    editItemIconInput.value = iconClass;
    updateIconPreview(iconClass);

    // Tags
    const tags = item.tags ? (Array.isArray(item.tags) ? item.tags.join(', ') : item.tags) : '';
    document.getElementById('editItemTags').value = tags;
    
    editModal.classList.add('active');
}

function updateIconPreview(iconClass) {
    // Remove 'fa-' prefix if user typed it, but we need it for the class
    // Actually, let's assume user types 'fa-carrot' or just 'carrot'
    // But FontAwesome usually needs 'fa-carrot'.
    // Let's just use what they type, but ensure 'fas' is there in the container or i tag.
    // The preview container has <i class="fas fa-box"></i>
    
    const iTag = editItemIconPreview.querySelector('i');
    iTag.className = `fas ${iconClass}`;
}

editItemIconInput.addEventListener('input', (e) => {
    updateIconPreview(e.target.value);
});

function closeModal() {
    editModal.classList.remove('active');
}

closeModalBtn.addEventListener('click', closeModal);
cancelEditBtn.addEventListener('click', closeModal);

// Close on outside click
editModal.addEventListener('click', (e) => {
    if (e.target === editModal) closeModal();
});

// Handle Form Submit (Mock Update for now)
editForm.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Update functionality requires backend implementation.');
    closeModal();
});

// Add Item Logic
const addItemIconInput = document.getElementById('addItemIcon');
const addItemIconPreview = document.getElementById('addItemIconPreview');

function updateAddIconPreview(iconClass) {
    const iTag = addItemIconPreview.querySelector('i');
    // Default to fa-box if empty
    if (!iconClass) iconClass = 'fa-box';
    iTag.className = `fas ${iconClass}`;
}

addItemIconInput.addEventListener('input', (e) => {
    updateAddIconPreview(e.target.value);
});

function openAddModal() {
    addForm.reset();
    updateAddIconPreview('fa-box'); // Reset preview
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

// Handle Add Form Submit (Mock Add for now)
addForm.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Add functionality requires backend implementation.');
    closeAddModal();
});

// Close modals on Esc key press
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal();
        closeAddModal();
    }
});

function formatDateShort(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
}
