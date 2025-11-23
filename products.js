const API_URL = 'http://localhost/projects/SastoMahango/API/getItemList.php';
let currentItems = [];
let currentIndex = 0;
let isAllLoaded = false;

document.addEventListener('DOMContentLoaded', () => {
    loadItems(20); // Initial load

    document.getElementById('loadMoreBtn').addEventListener('click', () => loadItems(20));
    document.getElementById('loadAllBtn').addEventListener('click', () => loadAllItems());
    
    const searchInput = document.getElementById('marketSearch');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => filterItems());
    }

    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', handleCategoryChange);
    }

    // Check for URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');
    const searchParam = urlParams.get('search');
    
    let shouldLoadAll = false;

    if (categoryParam && categoryFilter) {
        categoryFilter.value = categoryParam;
        shouldLoadAll = true;
    }

    if (searchParam && searchInput) {
        searchInput.value = searchParam;
        shouldLoadAll = true;
    }

    if (shouldLoadAll) {
        if (!isAllLoaded) {
            // Show loading state on buttons if they exist
            const loadAllBtn = document.getElementById('loadAllBtn');
            if (loadAllBtn) loadAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            setTimeout(async () => {
                await loadAllItems();
                filterItems();
            }, 50);
        } else {
            filterItems();
        }
    }
});

async function handleCategoryChange() {
    const category = document.getElementById('categoryFilter').value;
    const warning = document.getElementById('filterWarning');
    
    // If a category is selected and we haven't loaded all items, load them now
    if (category && !isAllLoaded) {
        if (warning) warning.style.display = 'inline-flex';
        
        // Small delay to allow UI to update before heavy operation
        setTimeout(async () => {
            await loadAllItems();
            if (warning) warning.style.display = 'none';
            filterItems();
        }, 50);
    } else {
        filterItems();
    }
}

async function fetchItems(index, count) {
    try {
        const response = await fetch(`${API_URL}?index=${index}&count=${count}&order=DESC`);
        const text = await response.text();
        
        // Check for PHP errors or HTML in response
        if (text.trim().startsWith('<') || text.trim().startsWith('<?php')) {
            console.error('Server returned HTML instead of JSON:', text);
            alert('Error: The server returned an invalid response. Please ensure you are accessing this via "http://localhost/..." and not opening the file directly.');
            return [];
        }

        try {
            const data = JSON.parse(text);
            return data;
        } catch (e) {
            console.error('JSON Parse Error:', e);
            return [];
        }
    } catch (error) {
        console.error('Fetch Error:', error);
        alert('Failed to connect to the server. Make sure XAMPP is running.');
        return [];
    }
}

async function loadItems(count) {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const loadAllBtn = document.getElementById('loadAllBtn');
    
    if (loadMoreBtn) loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

    const newItems = await fetchItems(currentIndex, count);
    
    if (newItems.length > 0) {
        currentItems = [...currentItems, ...newItems];
        currentIndex += newItems.length;
        renderItems(currentItems);
        
        // If we got fewer items than requested, we've likely reached the end
        if (newItems.length < count) {
            isAllLoaded = true;
            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            if (loadAllBtn) loadAllBtn.style.display = 'none';
        }
    } else {
        isAllLoaded = true;
        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
        if (loadAllBtn) loadAllBtn.style.display = 'none';
    }

    if (loadMoreBtn) {
        loadMoreBtn.innerHTML = '<i class="fas fa-chevron-down"></i> Load More';
        if (isAllLoaded) loadMoreBtn.style.display = 'none';
    }
    
    // Re-apply search filter if exists
    filterItems();
}

async function loadAllItems() {
    const loadAllBtn = document.getElementById('loadAllBtn');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    if (loadAllBtn) loadAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading All...';
    
    // Reset and fetch all
    currentIndex = 0;
    currentItems = [];
    const allItems = await fetchItems(0, 'all');
    
    currentItems = allItems;
    currentIndex = allItems.length;
    isAllLoaded = true;
    
    renderItems(currentItems);
    
    if (loadMoreBtn) loadMoreBtn.style.display = 'none';
    if (loadAllBtn) loadAllBtn.style.display = 'none';
    
    // Re-apply search filter if exists
    filterItems();
}

function renderItems(items) {
    const container = document.getElementById('marketListContainer');
    if (!container) return;

    container.innerHTML = items.map(item => {
        // Determine icon based on category (simple mapping)
        let iconClass = 'fa-box';
        const cat = (item.category || '').toLowerCase();
        if (cat.includes('veg')) iconClass = 'fa-carrot';
        else if (cat.includes('fruit')) iconClass = 'fa-apple-alt';
        else if (cat.includes('dairy') || cat.includes('egg')) iconClass = 'fa-egg';
        else if (cat.includes('grain') || cat.includes('rice')) iconClass = 'fa-rice';
        else if (cat.includes('oil')) iconClass = 'fa-oil-can';
        else if (cat.includes('meat') || cat.includes('chicken') || cat.includes('fish')) iconClass = 'fa-drumstick-bite';
        else if (cat.includes('gas') || cat.includes('energy')) iconClass = 'fa-gas-pump';
        
        // Trend Logic
        let trendClass = 'neutral';
        let trendIcon = 'fa-minus';
        let trendText = 'Rs. 0.00';
        
        // API returns 'up', 'down', 'neutral' and 'change'
        if (item.trend === 'up') {
            trendClass = 'up';
            trendIcon = 'fa-arrow-up';
            trendText = `Rs. ${item.change}`;
        } else if (item.trend === 'down') {
            trendClass = 'down';
            trendIcon = 'fa-arrow-down';
            trendText = `Rs. ${item.change}`;
        } else {
            trendText = `Rs. ${item.change || '0.00'}`;
        }
        
        return `
            <div class="market-item reveal" onclick="openItemModal(${item.id})" style="cursor: pointer;">
                <div class="col-icon">
                    <div class="item-icon-box"><i class="fas ${iconClass}"></i></div>
                </div>
                <div class="col-name">
                    <span class="name-text">${item.name}</span>
                    <span class="mobile-label">${item.category}</span>
                </div>
                <div class="col-category">${item.category}</div>
                <div class="col-price">Rs. ${item.price} / ${item.unit}</div>
                <div class="col-trend">
                    <span class="trend-badge ${trendClass}"><i class="fas ${trendIcon}"></i> ${trendText}</span>
                </div>
                <div class="col-updated">${formatDate(item.last_updated)}</div>
            </div>
        `;
    }).join('');
    
    // Trigger animations if any
    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach(reveal => reveal.classList.add('active'));
}

async function openItemModal(id) {
    const item = currentItems.find(i => i.id === id);
    if (!item) return;

    // Update View Count
    try {
        fetch(`http://localhost/projects/SastoMahango/API/itemViewer.php?id=${id}`);
        // Optimistically update local view count
        item.views = (parseInt(item.views) || 0) + 1;
    } catch (e) {
        console.error('Failed to update view count', e);
    }

    // Populate Modal Data
    document.getElementById('modalTitle').textContent = item.name;
    document.getElementById('modalCategory').textContent = item.category;
    document.getElementById('modalPrice').textContent = `Rs. ${item.price} / ${item.unit}`;
    document.getElementById('modalPrevPrice').textContent = `Rs. ${item.previous_price}`;
    document.getElementById('modalUpdated').textContent = formatDate(item.last_updated);
    document.getElementById('modalViews').textContent = item.views;
    document.getElementById('modalContributor').textContent = item.created_by || 'Unknown';

    // Icon
    let iconClass = 'fa-box';
    const cat = (item.category || '').toLowerCase();
    if (cat.includes('veg')) iconClass = 'fa-carrot';
    else if (cat.includes('fruit')) iconClass = 'fa-apple-alt';
    else if (cat.includes('dairy') || cat.includes('egg')) iconClass = 'fa-egg';
    else if (cat.includes('grain') || cat.includes('rice')) iconClass = 'fa-rice';
    else if (cat.includes('oil')) iconClass = 'fa-oil-can';
    else if (cat.includes('meat') || cat.includes('chicken') || cat.includes('fish')) iconClass = 'fa-drumstick-bite';
    else if (cat.includes('gas') || cat.includes('energy')) iconClass = 'fa-gas-pump';
    
    document.getElementById('modalIcon').className = `fas ${iconClass}`;

    // Trend
    const trendEl = document.getElementById('modalTrend');
    let trendHtml = '';
    if (item.trend === 'up') {
        trendHtml = `<span class="trend-badge up"><i class="fas fa-arrow-up"></i> Rs. ${item.change}</span>`;
    } else if (item.trend === 'down') {
        trendHtml = `<span class="trend-badge down"><i class="fas fa-arrow-down"></i> Rs. ${item.change}</span>`;
    } else {
        trendHtml = `<span class="trend-badge neutral"><i class="fas fa-minus"></i> Rs. ${item.change || '0.00'}</span>`;
    }
    trendEl.innerHTML = trendHtml;

    // Tags
    const tagsContainer = document.getElementById('modalTags');
    if (item.tags && item.tags.length > 0) {
        tagsContainer.innerHTML = item.tags.map(tag => `<span class="tag-pill">#${tag}</span>`).join('');
    } else {
        tagsContainer.innerHTML = '';
    }

    // Show Modal
    const modal = document.getElementById('itemModal');
    modal.style.display = 'flex';
    // Small delay for animation
    setTimeout(() => modal.classList.add('active'), 10);
}

function closeModal() {
    const modal = document.getElementById('itemModal');
    modal.classList.remove('active');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('itemModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Close modal when pressing Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('itemModal');
        if (modal && modal.style.display === 'flex') {
            closeModal();
        }
    }
});

function filterItems() {
    const searchInput = document.getElementById('marketSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const category = categoryFilter ? categoryFilter.value.toLowerCase() : '';

    const filtered = currentItems.filter(item => {
        const matchesSearch = item.name.toLowerCase().includes(searchTerm) || 
                              item.category.toLowerCase().includes(searchTerm);
        const matchesCategory = category === '' || item.category.toLowerCase() === category;
        
        return matchesSearch && matchesCategory;
    });
    
    renderItems(filtered);
    
    const container = document.getElementById('marketListContainer');
    
    // Logic for "Not Found" message
    if (filtered.length === 0) {
        let message = `
            <div class="no-results" style="text-align: center; padding: 2rem; color: #666;">
                <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; color: #ccc;"></i>
                <p>No items found matching your criteria</p>
            </div>
        `;
        
        if (!isAllLoaded) {
            message += `
                <div style="text-align: center; margin-top: 1rem;">
                    <p style="color: #e67e22; margin-bottom: 0.5rem;">
                        <i class="fas fa-info-circle"></i> Not all items are loaded yet.
                    </p>
                    <button onclick="loadAllItems()" class="pagination-btn" style="display: inline-block; width: auto; padding: 0.5rem 1.5rem;">
                        Load All Items to Search
                    </button>
                </div>
            `;
        }
        
        container.innerHTML = message;
    }
}

function formatDate(dateString) {
    if (!dateString) return 'Recently';
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff/60)} mins ago`;
    if (diff < 86400) return `${Math.floor(diff/3600)} hours ago`;
    return date.toLocaleDateString();
}
