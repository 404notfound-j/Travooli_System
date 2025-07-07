// Function to toggle details panel
function toggleDetails(bookingId) {
    const row = document.getElementById(`booking-${bookingId}`);
    if (!row) return;
    
    const detailsRow = row.nextElementSibling;
    if (!detailsRow || !detailsRow.classList.contains('details-row')) {
        console.error('No details row found for this booking');
        return;
    }
    
    // Toggle arrow icon
    const arrowIcon = row.querySelector('.arrow-icon');
    if (arrowIcon) {
        arrowIcon.classList.toggle('fa-chevron-down');
        arrowIcon.classList.toggle('fa-chevron-up');
    }
    
    // Toggle row highlight
    row.classList.toggle('active');
    
    // Toggle display of details panel
    const detailsPanel = detailsRow.querySelector('.details-panel');
    if (detailsPanel) {
        if (detailsPanel.style.display === 'block') {
            detailsPanel.style.display = 'none';
            detailsRow.classList.remove('active');
        } else {
            // Close any other open panels first
            const allDetailsPanels = document.querySelectorAll('.details-panel');
            allDetailsPanels.forEach(panel => {
                if (panel !== detailsPanel) {
                    panel.style.display = 'none';
                    const parentRow = panel.closest('.details-row');
                    if (parentRow) parentRow.classList.remove('active');
                    
                    // Reset arrow icon for other rows
                    const parentBookingRow = parentRow ? parentRow.previousElementSibling : null;
                    if (parentBookingRow && parentBookingRow.classList.contains('booking-row')) {
                        const otherArrowIcon = parentBookingRow.querySelector('.arrow-icon');
                        if (otherArrowIcon) {
                            otherArrowIcon.classList.remove('fa-chevron-up');
                            otherArrowIcon.classList.add('fa-chevron-down');
                        }
                        parentBookingRow.classList.remove('active');
                    }
                }
            });
            
            detailsPanel.style.display = 'block';
            detailsRow.classList.add('active');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all details panels to be hidden on page load
    const detailsPanels = document.querySelectorAll('.details-panel');
    detailsPanels.forEach(panel => {
        panel.style.display = 'none';
    });
    
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Handle tab content switching
            const tabName = this.getAttribute('data-tab');
            // For now, we only have the flight tab functionality
        });
    });
    
    // Admin profile dropdown functionality
    const adminProfileContainer = document.getElementById('admin-profile-container');
    const adminProfileDropdown = document.getElementById('admin-profile-dropdown');
    
    if (adminProfileContainer && adminProfileDropdown) {
        adminProfileContainer.addEventListener('click', function(e) {
            e.stopPropagation();
            adminProfileDropdown.classList.toggle('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!adminProfileContainer.contains(e.target)) {
                adminProfileDropdown.classList.remove('show');
            }
        });
    }
    
    // Pagination functionality
    const prevBtn = document.querySelector('.pagination-btn.prev');
    const nextBtn = document.querySelector('.pagination-btn.next');
    
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', function() {
            // Handle previous page logic
            console.log('Go to previous page');
        });
        
        nextBtn.addEventListener('click', function() {
            // Handle next page logic
            console.log('Go to next page');
        });
    }
}); 