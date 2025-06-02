// Delete Account Popup JavaScript

// Function to show the modal
function showModal() {
    const modal = document.getElementById('deleteAccountModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('deleteAccountModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Function to handle delete confirmation
function confirmDelete() {
    // Add your delete account logic here
    alert('Account deactivation confirmed. This is where you would implement the actual deactivation logic.');
    
    // For now, just close the modal
    closeModal();
    
    // In a real implementation, you would:
    // 1. Make an API call to deactivate the account
    // 2. Handle the response
    // 3. Redirect the user or show a success message
    // 4. Clear user session data
}

// Close modal when clicking outside of it
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteAccountModal');
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
});

// Auto-show modal on page load (for testing purposes)
// Remove this in production or when integrating with other pages
document.addEventListener('DOMContentLoaded', function() {
    showModal();
}); 