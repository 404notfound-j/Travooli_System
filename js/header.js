// Navigation menu active state management
document.addEventListener('DOMContentLoaded', function() {
    // Get all nav items
    const navItems = document.querySelectorAll('.nav-item');
    
    // Get current page name from URL
    const currentPage = window.location.pathname.split('/').pop();
    
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
            } else if ((currentPage === '' || currentPage === 'index.php') && href === '#flights') {
                item.classList.add('active');
            } else if (currentPage === href) {
                item.classList.add('active');
            }
        }
    });
    
    // Click event listeners not needed since navigation is handled by page detection
});
