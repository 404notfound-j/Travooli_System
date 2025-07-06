// Admin Cancel Hotel Booking JavaScript

// Function to show the modal
function showModal() {
    const modal = document.getElementById('cancelHotelModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('cancelHotelModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';

        // If opened from another page, redirect back
        if (document.referrer && document.referrer.includes('adminModifyHotel.php')) {
            window.location.href = document.referrer;
        }
    }
}

// Function to handle hotel cancellation confirmation
function confirmCancelHotel() {
    // Get booking ID from URL if available
    const urlParams = new URLSearchParams(window.location.search);
    const bookingId = urlParams.get('bookingId');

    // Add your hotel cancellation logic here
    console.log('Hotel booking cancellation confirmed for ID:', bookingId);

    // In a real implementation, you would:
    // 1. Make an API call to cancel the hotel booking
    // 2. Update the database status
    // 3. Redirect the user or show a success message

    // For now, just show an alert and redirect
    alert('Hotel booking has been cancelled successfully. You can restore it from the booking list if needed.');

    // Redirect to the hotel bookings list page
    window.location.href = 'adminHotelBooking.php?cancelled=true&bookingId=' + bookingId;
}

// Close modal when clicking outside of it and bind events when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cancelHotelModal');
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

    // Auto-show modal on page load
    showModal();
}); 