document.addEventListener('DOMContentLoaded', function() {
    // Get booking ID from URL or from the global variable set in PHP
    const urlParams = new URLSearchParams(window.location.search);
    const bookingId = urlParams.get('bookingId') || window.bookingId || '';
    
    // Initialize edit buttons functionality
    initEditButtons();
    
    // Function to initialize edit buttons
    function initEditButtons() {
        const editButtons = document.querySelectorAll('.edit-btn');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const editableField = this.closest('.editable-field');
                const fieldText = editableField.querySelector('span');
                const currentValue = fieldText.textContent.trim();
                
                // If already in edit mode, save the changes
                if (editableField.classList.contains('editing')) {
                    const inputField = editableField.querySelector('input');
                    const newValue = inputField.value.trim();
                    
                    // Update the text with the new value
                    fieldText.textContent = newValue;
                    
                    // Remove the input field
                    inputField.remove();
                    
                    // Toggle editing class
                    editableField.classList.remove('editing');
                    button.classList.remove('editing');
                    
                    // Change button icon back to edit
                    button.innerHTML = '<img src="icon/edit.svg" alt="Edit">';
                } else {
                    // Create input field
                    const inputField = document.createElement('input');
                    inputField.type = 'text';
                    inputField.value = currentValue;
                    inputField.className = 'edit-input';
                    
                    // Add input field before the span
                    editableField.insertBefore(inputField, fieldText);
                    
                    // Focus on the input
                    inputField.focus();
                    
                    // Toggle editing class
                    editableField.classList.add('editing');
                    button.classList.add('editing');
                    
                    // Change button icon to save
                    button.innerHTML = '<i class="fas fa-check" style="color: #4379EE;"></i>';
                    
                    // Handle Enter key to save
                    inputField.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            button.click();
                        }
                    });
                    
                    // Handle Escape key to cancel
                    inputField.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            e.preventDefault();
                            // Restore original value
                            inputField.remove();
                            editableField.classList.remove('editing');
                            button.classList.remove('editing');
                            button.innerHTML = '<img src="icon/edit.svg" alt="Edit">';
                        }
                    });
                }
            });
        });
    }
    
    // Function to open cancel flight modal
    window.openCancelFlightModal = function() {
        // Show in-page cancel flight modal if it exists
        const modal = document.getElementById('cancelFlightModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        } else {
            // Fallback to standalone page if modal markup is not present
            const urlParams = new URLSearchParams(window.location.search);
            const bookingId = urlParams.get('bookingId') || window.bookingId || '';
            window.location.href = `adminCancelFlight.php?bookingId=${bookingId}`;
        }
    };
    
    // Save changes button functionality
    const saveBtn = document.querySelector('.save-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            // Collect all changes
            const changes = collectChanges();
            
            // Here you would typically make an AJAX call to your server to save the changes
            console.log('Changes to save:', changes);
            alert('Changes would be saved here.');
            
            // Redirect back to booking list after saving
            // window.location.href = 'adminFlightBooking.php';
        });
    }
    
    // Discard changes button functionality
    const discardBtn = document.querySelector('.discard-btn');
    if (discardBtn) {
        discardBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to discard all changes?')) {
                // Redirect back to booking list without saving
                window.location.href = 'adminFlightBooking.php';
            }
        });
    }
    
    // Function to collect all changes made to the booking
    function collectChanges() {
        const changes = {
            bookingId: bookingId,
            passengers: []
        };
        
        // Collect passenger data
        const passengerRows = document.querySelectorAll('.traveler-table tbody tr');
        passengerRows.forEach((row, index) => {
            const nameCell = row.querySelector('td:nth-child(1) .editable-field span');
            const ageGroupCell = row.querySelector('td:nth-child(2) .editable-field span');
            const seatCell = row.querySelector('td:nth-child(3)');
            const classSelect = row.querySelector('td:nth-child(4) select');
            const mealSelect = row.querySelector('td:nth-child(5) select');
            const amountCell = row.querySelector('td:nth-child(6)');
            
            changes.passengers.push({
                name: nameCell ? nameCell.textContent.trim() : '',
                ageGroup: ageGroupCell ? ageGroupCell.textContent.trim() : '',
                seat: seatCell ? seatCell.textContent.trim() : '',
                class: classSelect ? classSelect.value : '',
                mealType: mealSelect ? mealSelect.value : '',
                amount: amountCell ? amountCell.textContent.trim() : ''
            });
        });
        
        return changes;
    }
});

// ==== In-page Cancel Flight Popup Handling ====
function closeModal() {
    const modal = document.getElementById('cancelFlightModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function confirmCancelFlight() {
    const urlParams = new URLSearchParams(window.location.search);
    const bookingId = urlParams.get('bookingId') || window.bookingId || '';

    // TODO: Replace with real cancellation logic / API call
    console.log('Flight booking cancellation confirmed for ID:', bookingId);
    alert('Flight booking has been cancelled successfully.');

    // Redirect to booking list page
    window.location.href = 'adminFlightBooking.php?cancelled=true&bookingId=' + bookingId;
}

// Attach outside-click and ESC-close behaviour
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cancelFlightModal');
    const modalDialog = modal ? modal.querySelector('.modal-dialog') : null;

    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });
    }

    if (modalDialog) {
        modalDialog.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }

    // Close on ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
}); 