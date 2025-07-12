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
    // userIdToDelete should be set by the page that loads the popup
    if (typeof window.userIdToDelete === 'undefined' || !window.userIdToDelete) {
        alert('No user selected for deletion.');
        closeModal();
        return;
    }
    fetch('delete_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'user_id=' + encodeURIComponent(window.userIdToDelete)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to delete user: ' + (data.error || 'Unknown error'));
            closeModal();
        }
    })
    .catch(() => {
        alert('Failed to delete user.');
        closeModal();
    });
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