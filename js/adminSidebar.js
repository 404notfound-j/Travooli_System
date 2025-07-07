document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // Only prevent default for items without valid href or with href="#"
            const href = this.getAttribute('href');
            if (!href || href === '#' || href === '') {
                e.preventDefault();
            }
            
            // Remove active class from all items
            document.querySelectorAll('.nav-item').forEach(nav => {
                nav.classList.remove('active');
            });
            
            // Add active class to clicked item
            this.classList.add('active');
        });
    });

    // Admin profile dropdown functionality
    const adminProfileContainer = document.getElementById('admin-profile-container');
    const adminProfileDropdown = document.getElementById('admin-profile-dropdown');
    
    function toggleAdminDropdown() {
        if (adminProfileContainer && adminProfileDropdown) {
            adminProfileContainer.classList.toggle('active');
            adminProfileDropdown.classList.toggle('active');
        }
    }
    
    function closeAdminDropdown() {
        if (adminProfileContainer && adminProfileDropdown) {
            adminProfileContainer.classList.remove('active');
            adminProfileDropdown.classList.remove('active');
    }
    }
    
    // Admin profile container click event
    if (adminProfileContainer) {
        adminProfileContainer.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleAdminDropdown();
});
    }
    
    // Close admin dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (adminProfileDropdown && adminProfileDropdown.classList.contains('active')) {
            const isClickInsideProfile = adminProfileContainer.contains(event.target) || adminProfileDropdown.contains(event.target);
            if (!isClickInsideProfile) {
                closeAdminDropdown();
            }
        }
});

    // Close admin dropdown when clicking dropdown items
    if (adminProfileDropdown) {
        const adminDropdownItems = adminProfileDropdown.querySelectorAll('a');
        adminDropdownItems.forEach(item => {
            item.addEventListener('click', () => {
                closeAdminDropdown();
            });
        });
    }
    
    // Escape key to close admin dropdown
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && adminProfileDropdown && adminProfileDropdown.classList.contains('active')) {
            closeAdminDropdown();
        }
    });



// Mobile sidebar toggle (for responsive design)
function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('open');
        }
}

// Add mobile menu button dynamically for small screens
if (window.innerWidth <= 768) {
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.innerHTML = '☰';
    mobileMenuBtn.style.cssText = `
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1000;
            background: #031E2F;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    `;
    mobileMenuBtn.onclick = toggleSidebar;
        mobileMenuBtn.setAttribute('aria-label', 'Toggle sidebar menu');
    document.body.appendChild(mobileMenuBtn);
}
    
    // Window resize handler
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        }
    });
});

