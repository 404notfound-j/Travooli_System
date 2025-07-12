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

// Function to confirm hotel cancellation
function confirmCancelHotel() {
    // Get booking ID from button data attribute
    const confirmBtn = document.querySelector('.btn-primary.btn-danger');
    let bookingId = confirmBtn ? confirmBtn.getAttribute('data-booking-id') : null;

    // If no booking ID, try to get it from URL
    if (!bookingId) {
        const urlParams = new URLSearchParams(window.location.search);
        const urlBookingId = urlParams.get('bookingId');
        
        if (!urlBookingId) {
            alert('Booking ID not found.');
            return;
        } else {
            bookingId = urlBookingId;
        }
    }

    // Create form data for AJAX request
    const formData = new FormData();
    formData.append('bookingId', bookingId);
    formData.append('action', 'cancel');

    // Show loading state
    if (confirmBtn) {
        confirmBtn.textContent = 'Processing...';
        confirmBtn.disabled = true;
    }

    // Send AJAX request to cancel booking
    fetch('updateHotelBooking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Booking has been cancelled and refund processed successfully.');
            // Redirect to booking list
            window.location.href = 'adminHotelBooking.php';
        } else {
            // Show error message
            alert('Error: ' + data.message);
            
            // Reset button state
            if (confirmBtn) {
                confirmBtn.textContent = 'Confirm';
                confirmBtn.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while cancelling the booking. Please try again.');
        
        // Reset button state
        if (confirmBtn) {
            confirmBtn.textContent = 'Confirm';
            confirmBtn.disabled = false;
        }
    });
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