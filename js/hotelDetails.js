document.addEventListener('DOMContentLoaded', function() {
    // Wait for SVG objects to load
    setTimeout(function() {
        // Handle heart icon
        document.querySelectorAll('.hotel-details-action-buttons .hotel-card-fav-btn').forEach(function(btn) {
            const heartIcon = btn.querySelector('.heart-icon');
            if (heartIcon && heartIcon.contentDocument) {
                const heartPath = heartIcon.contentDocument.querySelector('path');
                
                // Make the heart SVG and its container clickable
                if (heartPath) {
                    // Make heart path clickable
                    heartPath.style.cursor = 'pointer';
                    
                    // Make the SVG document clickable
                    heartIcon.contentDocument.documentElement.style.cursor = 'pointer';
                    heartIcon.contentDocument.documentElement.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleHeartSelection(btn, heartPath);
                    });
                    
                    // Make heart path clickable
                    heartPath.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleHeartSelection(btn, heartPath);
                    });
                }
                
                // Keep the button clickable too
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleHeartSelection(btn, heartPath);
                });
            }
        });
        
        // Handle share icon
        document.querySelectorAll('.hotel-details-action-buttons .hotel-card-share-btn').forEach(function(btn) {
            const shareIcon = btn.querySelector('.share-icon');
            if (shareIcon && shareIcon.contentDocument) {
                const sharePath = shareIcon.contentDocument.querySelector('path');
                
                if (sharePath) {
                    // Make share path clickable
                    sharePath.style.cursor = 'pointer';
                    
                    // Make the SVG document clickable
                    shareIcon.contentDocument.documentElement.style.cursor = 'pointer';
                }
                
                // Share button click handler
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Implement share functionality here
                    alert('Share functionality will be implemented');
                });
                
                // Make share icon clickable
                if (shareIcon.contentDocument.documentElement) {
                    shareIcon.contentDocument.documentElement.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        // Implement share functionality here
                        alert('Share functionality will be implemented');
                    });
                }
            }
        });
    }, 100); // Small delay to ensure SVGs are loaded
    
    // Function to toggle heart selection
    function toggleHeartSelection(btn, heartPath) {
        btn.classList.toggle('selected');
        if (heartPath) {
            if (btn.classList.contains('selected')) {
                heartPath.setAttribute('fill', '#605DEC'); // Use #605DEC when selected
            } else {
                heartPath.setAttribute('fill', 'none'); // Use transparent when not selected
            }
        }
    }
}); 