// Navigation menu active state management and mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    const nav = document.querySelector('.nav');
    const navItems = document.querySelectorAll('.nav-item');
    const body = document.body;
    
    // Profile dropdown elements
    const profileContainer = document.getElementById('profile-container');
    const profileDropdown = document.getElementById('profile-dropdown');
    
    // Get current page name from URL
    const currentPage = window.location.pathname.split('/').pop();
    
    // Mobile menu toggle function
    function toggleMobileMenu() {
        hamburger.classList.toggle('active');
        nav.classList.toggle('active');
        body.classList.toggle('nav-open');
        
        // Update ARIA attributes for accessibility
        const isExpanded = nav.classList.contains('active');
        hamburger.setAttribute('aria-expanded', isExpanded);
    }
    
    // Close mobile menu function
    function closeMobileMenu() {
        hamburger.classList.remove('active');
        nav.classList.remove('active');
        body.classList.remove('nav-open');
        hamburger.setAttribute('aria-expanded', 'false');
    }
    
    // Hamburger menu click event
    if (hamburger) {
        hamburger.addEventListener('click', toggleMobileMenu);
        
        // Add ARIA attributes for accessibility
        hamburger.setAttribute('aria-label', 'Toggle navigation menu');
        hamburger.setAttribute('aria-expanded', 'false');
    }
    
    // Close menu when clicking on navigation items (except buttons)
    navItems.forEach(item => {
        const link = item.querySelector('a');
        const isButton = link && link.classList.contains('btn');
        
        if (!isButton) {
            item.addEventListener('click', () => {
                // Only close menu on mobile
                if (window.innerWidth <= 768) {
                    closeMobileMenu();
                }
            });
        }
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideNav = nav.contains(event.target);
        const isClickOnHamburger = hamburger.contains(event.target);
        
        if (!isClickInsideNav && !isClickOnHamburger && nav.classList.contains('active')) {
            closeMobileMenu();
        }
    });
    
    // Close menu on window resize if screen becomes larger
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && nav.classList.contains('active')) {
            closeMobileMenu();
        }
    });
    
    // Escape key to close mobile menu
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && nav.classList.contains('active')) {
            closeMobileMenu();
        }
        if (event.key === 'Escape' && profileDropdown && profileDropdown.classList.contains('active')) {
            closeProfileDropdown();
        }
    });
    
    // Profile dropdown functionality
    function toggleProfileDropdown() {
        if (profileContainer && profileDropdown) {
            profileContainer.classList.toggle('active');
            profileDropdown.classList.toggle('active');
        }
    }
    
    function closeProfileDropdown() {
        if (profileContainer && profileDropdown) {
            profileContainer.classList.remove('active');
            profileDropdown.classList.remove('active');
        }
    }
    
    // Profile container click event
    if (profileContainer) {
        profileContainer.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleProfileDropdown();
        });
    }
    
    // Close profile dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (profileDropdown && profileDropdown.classList.contains('active')) {
            const isClickInsideProfile = profileContainer.contains(event.target) || profileDropdown.contains(event.target);
            if (!isClickInsideProfile) {
                closeProfileDropdown();
            }
        }
    });
    
    // Close profile dropdown when clicking mobile menu items
    if (profileDropdown) {
        const profileDropdownItems = profileDropdown.querySelectorAll('a');
        profileDropdownItems.forEach(item => {
            item.addEventListener('click', () => {
                closeProfileDropdown();
                // Also close mobile menu if open
                if (window.innerWidth <= 768) {
                    closeMobileMenu();
                }
            });
        });
    }
    
    // Remove active class from all nav items first
    navItems.forEach(navItem => {
        navItem.classList.remove('active');
    });
    
    // Set active state based on current page
    navItems.forEach(item => {
        const link = item.querySelector('a');
        if (link) {
            const href = link.getAttribute('href');
            const isButton = link.classList.contains('btn');
            
            // Skip adding active class to button-styled links (like Sign up)
            if (isButton) {
                return;
            }
            
            // Check if current page matches the nav item
            if (currentPage === 'signIn.php' && href === 'signIn.php') {
                item.classList.add('active');
            } else if (currentPage === 'U_dashboard.php' && href === 'U_dashboard.php') {
                item.classList.add('active');
            } else if ((currentPage === '' || currentPage === 'index.php') && href === '#flights') {
                item.classList.add('active');
            } else if (currentPage === href) {
                item.classList.add('active');
            }
        }
    });
    
    // Smooth scrolling for anchor links
    navItems.forEach(item => {
        const link = item.querySelector('a');
        if (link && link.getAttribute('href').startsWith('#')) {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Close mobile menu after navigation
                    if (window.innerWidth <= 768) {
                        closeMobileMenu();
                    }
                }
            });
        }
    });
    
    // Add loading state management
    window.addEventListener('beforeunload', function() {
        // Close mobile menu before page unload
        closeMobileMenu();
    });
});
