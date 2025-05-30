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
    
    // Scroll animation for testimonials
    const testimonialSection = document.querySelector('.testimonials');
    const testimonialHeader = document.querySelector('.testimonial-header');
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    
    // Check if elements exist to prevent errors
    if (!testimonialSection || !testimonialHeader || testimonialCards.length === 0) {
        return;
    }
    
    // Function to check if an element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8
        );
    }
    
    // Function to check if element is out of viewport (for removing animation)
    function isOutOfViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top > (window.innerHeight || document.documentElement.clientHeight)
        );
    }
    
    // Function to animate testimonials when they come into view
    function animateOnScroll() {
        // Add animation when scrolling down to the section
        if (isInViewport(testimonialSection)) {
            testimonialHeader.classList.add('animate');
            
            testimonialCards.forEach(card => {
                card.classList.add('animate');
            });
        } 
        // Remove animation when scrolling back up
        else if (isOutOfViewport(testimonialSection)) {
            testimonialHeader.classList.remove('animate');
            
            testimonialCards.forEach(card => {
                card.classList.remove('animate');
            });
        }
    }
    
    // Initial check in case the section is already in view
    animateOnScroll();
    
    // Add scroll event listener
    window.addEventListener('scroll', animateOnScroll);
}); 