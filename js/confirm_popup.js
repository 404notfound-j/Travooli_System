// Confirmation Popup JavaScript

// Get URL parameters
function getUrlParams() {
    const params = {};
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    
    for (const [key, value] of urlParams.entries()) {
        params[key] = value;
    }
    
    return params;
}

// Function to show the modal
function showModal() {
    const modal = document.getElementById('confirmModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('confirmModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // If we're in an iframe, notify the parent to close us
        if (window.parent && window.parent !== window) {
            window.parent.postMessage('closeModal', '*');
        }
    }
}

// Function to handle confirmation based on action type
function confirmAction(actionType) {
    // Get URL parameters including any booking ID
    const params = getUrlParams();
    
    switch(actionType) {
        case 'cancelHotel':
            // Handle hotel cancellation
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ 
                    action: 'cancelHotel', 
                    confirmed: true,
                    bookingId: params.bookingId || null
                }, '*');
            }
            alert('Hotel booking has been cancelled.');
            break;
            
        case 'cancelFlight':
            // Handle flight cancellation
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ 
                    action: 'cancelFlight', 
                    confirmed: true,
                    bookingId: params.bookingId || null
                }, '*');
            }
            alert('Flight booking has been cancelled.');
            break;
            
        case 'deleteAccount':
            // Handle account deletion
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ 
                    action: 'deleteAccount', 
                    confirmed: true 
                }, '*');
            }
            alert('Account has been deactivated.');
            break;
            
        default:
            // Generic confirmation
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ 
                    action: 'generic', 
                    confirmed: true,
                    params: params
                }, '*');
            }
            alert('Action confirmed.');
    }
    
    // Close the modal after confirmation
    closeModal();
}

// Close modal when clicking outside of it
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('confirmModal');
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