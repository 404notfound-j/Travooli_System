// Admin Cancel Flight Booking JavaScript

// Function to show the modal
function showModal() {
    const modal = document.getElementById('cancelFlightModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('cancelFlightModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // If opened from another page, redirect back
        if (document.referrer && document.referrer.includes('adminModifyFlight.php')) {
            window.location.href = document.referrer;
        }
    }
}

// Function to handle flight cancellation confirmation
function confirmCancelFlight() {
    // Get booking ID from URL if available
    const urlParams = new URLSearchParams(window.location.search);
    const bookingId = urlParams.get('bookingId');
    
    // Add your flight cancellation logic here
    console.log('Flight booking cancellation confirmed for ID:', bookingId);
    
    // In a real implementation, you would:
    // 1. Make an API call to cancel the flight booking
    // 2. Update the database status
    // 3. Redirect the user or show a success message
    
    // For now, just show an alert and redirect
    alert('Flight booking has been cancelled successfully. You can restore it from the booking list if needed.');
    
    // Redirect to the flight bookings list page
    window.location.href = 'adminFlightBooking.php?cancelled=true&bookingId=' + bookingId;
}

// Close modal when clicking outside of it
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cancelFlightModal');
    const modalDialog = document.querySelector('.modal-dialog');
    
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });
    }
    
    // Prevent modal from closing when clicking inside the dialog
    if (modalDialog) {
        modalDialog.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
    
    // Close modal on Escape key press
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
    
    // Auto-show modal on page load only if the current page is adminCancelFlight.php
    const isStandaloneCancelPage = window.location.pathname.includes('adminCancelFlight.php');
    if (isStandaloneCancelPage) {
        showModal();
    }
}); 