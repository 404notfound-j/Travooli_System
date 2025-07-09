document.addEventListener('DOMContentLoaded', function() {
    // Function to set active navigation item based on current page
    function setActiveNavItem() {
        const currentPage = window.location.pathname.split('/').pop() || '';
        const navItems = document.querySelectorAll('.nav-item');
        
        // Remove active class from all items first
        navItems.forEach(item => item.classList.remove('active'));
        
        // Map pages to navigation items (only for actual working pages)
        const pageMapping = {
            'A_dashboard.php': 'Dashboard',
            'salesReport.php': 'Report',
            'recordTable.php': function(href) {
                // Handle recordTable.php with different parameters
                const urlParams = new URLSearchParams(window.location.search);
                const section = urlParams.get('section');
                if (section === 'payment') return 'Payment';
                if (section === 'refund') return 'Refund';
                return null;
            },  
            'U_Manage.php': 'User Management'
        };
        
        // Find and activate the matching nav item
        navItems.forEach(item => {
            const href = item.getAttribute('href');
            if (!href || href === '#') return;
            
            const linkPage = href.split('/').pop().split('?')[0];
            const itemText = item.textContent.trim();
            
            // Check direct page match
            if (linkPage === currentPage) {
                // Special handling for recordTable.php
                if (currentPage === 'recordTable.php') {
                    const expectedText = pageMapping[currentPage](href);
                    if (expectedText && itemText === expectedText) {
                        item.classList.add('active');
                    }
                } else if (pageMapping[currentPage] && itemText === pageMapping[currentPage]) {
                    item.classList.add('active');
                }
            }
        });
    }
    
    // Set active item on page load
    setActiveNavItem();
    
    // Handle navigation item clicks (only prevent default for placeholder links)
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (!href || href === '#' || href === '') {
                e.preventDefault();
            }
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

