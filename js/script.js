// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to search inputs for focus effects
    const searchInputs = document.querySelectorAll('.input-group input');
    
    searchInputs.forEach(input => {
        // Add focus effect
        input.addEventListener('focus', function() {
            this.parentElement.style.borderBottom = '2px solid #605DEC';
        });
        
        // Remove focus effect
        input.addEventListener('blur', function() {
            this.parentElement.style.borderBottom = 'none';
        });
    });
    
    // Add event listener for search button
    const searchBtn = document.querySelector('.search-btn');
    
    searchBtn.addEventListener('click', function() {
        // In a real application, this would handle the search functionality
        alert('Search functionality would be implemented here in a real application.');
    });
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}); 