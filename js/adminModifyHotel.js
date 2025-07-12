// Function to open cancel hotel modal
window.openCancelHotelModal = function() {
    // Show the modal
    const modal = document.getElementById('cancelHotelModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
};

// Function for smooth scroll down
window.scrollDownToSection = function(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
};

// Function to close cancel hotel modal
function closeModal() {
    const modal = document.getElementById('cancelHotelModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Function to confirm cancel hotel booking
function confirmCancelHotel() {
    // Get the booking ID from URL or data attribute
    const bookingId = document.getElementById('booking-id').textContent.replace(': ', '');
    
    // Send AJAX request to cancel booking
    fetch('updateHotelBooking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `bookingId=${bookingId}&action=cancel`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Booking cancelled successfully!');
            window.location.href = 'adminHotelBooking.php';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while cancelling the booking.');
    });
    
    // Close the modal
    closeModal();
}

// Variables to track changes
let originalData = {
    guestName: '',
    nationality: '',
    email: '',
    phone: ''
};

let currentData = {
    guestName: '',
    nationality: '',
    email: '',
    phone: ''
};

let hasChanges = false;
let activeField = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize original values of editable fields
    const originalValues = {
        'guest-name': document.getElementById('guest-name').textContent,
        'nationality': document.getElementById('nationality').textContent,
        'email': document.getElementById('email').textContent,
        'phone': document.getElementById('phone').textContent
    };
    
    // Store original data
    originalData.guestName = originalValues['guest-name'];
    originalData.nationality = originalValues['nationality'];
    originalData.email = originalValues['email'];
    originalData.phone = originalValues['phone'];
    
    // Initialize current data
    Object.assign(currentData, originalData);
    
    // Handle edit buttons
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // If another field is being edited, save it first
            if (activeField) {
                saveInlineEdit(activeField.id, activeField.input);
            }
            
            const fieldId = this.getAttribute('data-field');
            
            // Skip if this is the email field
            if (fieldId === 'email') {
                return;
            }
            
            const editableField = this.closest('.editable-field');
            const spanElement = document.getElementById(fieldId);
            const currentValue = spanElement.textContent;
            
            // Create input element
            const inputElement = document.createElement('input');
            inputElement.type = fieldId === 'phone' ? 'tel' : 'text';
            inputElement.value = currentValue;
            inputElement.className = 'editable-field-input';
            
            // Replace span with input
            spanElement.parentNode.replaceChild(inputElement, spanElement);
            
            // Change edit button to save button
            this.innerHTML = '<img src="icon/checkmark.svg" alt="Check">';
            this.classList.add('editing');
            
            // Focus on the input
            inputElement.focus();
            
            // Save active field
            activeField = {
                id: fieldId,
                input: inputElement,
                originalValue: currentValue,
                editButton: this
            };
            
            // Add event listeners for input
            inputElement.addEventListener('blur', function(e) {
                // Don't trigger blur if clicking the save button
                if (e.relatedTarget !== activeField.editButton) {
                    saveInlineEdit(fieldId, inputElement);
                }
            });
            
            inputElement.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    saveInlineEdit(fieldId, inputElement);
                } else if (e.key === 'Escape') {
                    cancelInlineEdit(fieldId, inputElement);
                }
            });
            
            // Add click event to the edit button (now a save button)
            this.removeEventListener('click', arguments.callee);
            this.addEventListener('click', function() {
                saveInlineEdit(fieldId, inputElement);
            });
        });
    });
    
    // Hide edit button for email field
    const emailEditButton = document.querySelector('[data-field="email"]');
    if (emailEditButton) {
        emailEditButton.style.display = 'none';
    }
    
    // Discard Changes Button
    const discardBtn = document.getElementById('discard-btn');
    discardBtn.addEventListener('click', function() {
        // If there are changes, confirm discard
        if (hasChanges) {
            if (confirm('Are you sure you want to discard all changes?')) {
                // Restore original values
                document.getElementById('guest-name').textContent = originalData.guestName;
                document.getElementById('nationality').textContent = originalData.nationality;
                document.getElementById('email').textContent = originalData.email;
                document.getElementById('phone').textContent = originalData.phone;
                
                // Reset current data to original
                Object.assign(currentData, originalData);
                
                hasChanges = false;
                updateSaveButtonState();
            }
        } else {
            // If no changes, just go back
            window.location.href = 'adminHotelBooking.php';
        }
    });
    
    // Save Changes Button
    const saveBtn = document.getElementById('save-btn');
    saveBtn.addEventListener('click', function() {
        if (!hasChanges) {
            alert('No changes have been made.');
            return;
        }
        
        // Get the booking ID from the save button's data attribute
        const bookingId = this.getAttribute('data-booking-id');
        
        if (!bookingId) {
            console.error('Booking ID not found');
            alert('Error: Booking ID not found');
            return;
        }
        
        console.log('Saving changes for booking ID:', bookingId);
        console.log('Current data:', currentData);
        
        // Create form data for the request
        const formData = new FormData();
        formData.append('bookingId', bookingId);
        formData.append('guestName', currentData.guestName);
        formData.append('nationality', currentData.nationality);
        formData.append('phone', currentData.phone);
        
        // Send AJAX request to update guest info
        fetch('updateHotelGuest.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                // Update original values to the new values
                Object.assign(originalData, currentData);
                
                // Reset change tracking
                hasChanges = false;
                updateSaveButtonState();
                
                // Show success message
                alert('Guest information updated successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating guest information.');
        });
    });
    
    // Close Modal Button
    const closeModalBtns = document.querySelectorAll('.close-modal');
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Find the parent modal and close it
            const modal = this.closest('.modal');
            if (modal) modal.classList.remove('show');
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.classList.remove('show');
            }
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
    
    // Cancel booking modal functionality
    const backBtn = document.getElementById('back-btn');
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            closeModal();
        });
    }

    const confirmBtn = document.getElementById('confirm-btn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', confirmCancelHotel);
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('cancelHotelModal');
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Add click event listener to document to handle clicks outside active input
    document.addEventListener('click', function(e) {
        if (activeField && 
            !e.target.classList.contains('editable-field-input') && 
            !e.target.classList.contains('edit-btn') && 
            !(e.target.tagName.toLowerCase() === 'img') && 
            !e.target.closest('.edit-btn')) {
            saveInlineEdit(activeField.id, activeField.input);
        }
    });
});

// Function to update save button state
function updateSaveButtonState() {
    const saveBtn = document.getElementById('save-btn');
    if (hasChanges) {
        saveBtn.classList.add('active');
    } else {
        saveBtn.classList.remove('active');
    }
}

// Save the inline edit
function saveInlineEdit(fieldId, inputElement) {
    if (!activeField) return;
    
    const value = inputElement.value.trim();
    
    // Validate input
    if (!value) {
        cancelInlineEdit(fieldId, inputElement);
        return;
    }
    
    // Create new span element
    const spanElement = document.createElement('span');
    spanElement.id = fieldId;
    spanElement.textContent = value;
    
    // Replace input with span
    inputElement.parentNode.replaceChild(spanElement, inputElement);
    
    // Update the current data
    switch(fieldId) {
        case 'guest-name':
            currentData.guestName = value;
            break;
        case 'nationality':
            currentData.nationality = value;
            break;
        case 'phone':
            currentData.phone = value;
            break;
    }
    
    // Reset edit button
    if (activeField.editButton) {
        activeField.editButton.innerHTML = '<img src="icon/edit.svg" alt="Edit">';
        activeField.editButton.classList.remove('editing');
        
        // Reset click event
        const button = activeField.editButton;
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        // Add the original click event back
        newButton.addEventListener('click', function() {
            // If another field is being edited, save it first
            if (activeField) {
                saveInlineEdit(activeField.id, activeField.input);
            }
            
            const fieldId = this.getAttribute('data-field');
            
            // Skip if this is the email field
            if (fieldId === 'email') {
                return;
            }
            
            const editableField = this.closest('.editable-field');
            const spanElement = document.getElementById(fieldId);
            const currentValue = spanElement.textContent;
            
            // Create input element
            const inputElement = document.createElement('input');
            inputElement.type = fieldId === 'phone' ? 'tel' : 'text';
            inputElement.value = currentValue;
            inputElement.className = 'editable-field-input';
            
            // Replace span with input
            spanElement.parentNode.replaceChild(inputElement, spanElement);
            
            // Change edit button to save button
            this.innerHTML = '<img src="icon/checkmark.svg" alt="Check">';
            this.classList.add('editing');
            
            // Focus on the input
            inputElement.focus();
            
            // Save active field
            activeField = {
                id: fieldId,
                input: inputElement,
                originalValue: currentValue,
                editButton: this
            };
            
            // Add event listeners for input
            inputElement.addEventListener('blur', function(e) {
                // Don't trigger blur if clicking the save button
                if (e.relatedTarget !== activeField.editButton) {
                    saveInlineEdit(fieldId, inputElement);
                }
            });
            
            inputElement.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    saveInlineEdit(fieldId, inputElement);
                } else if (e.key === 'Escape') {
                    cancelInlineEdit(fieldId, inputElement);
                }
            });
            
            // Add click event to the edit button (now a save button)
            this.removeEventListener('click', arguments.callee);
            this.addEventListener('click', function() {
                saveInlineEdit(fieldId, inputElement);
            });
        });
    }
    
    // Mark that changes have been made
    hasChanges = !isEqual(originalData, currentData);
    updateSaveButtonState();
    
    // Reset active field
    activeField = null;
}

// Cancel the inline edit
function cancelInlineEdit(fieldId, inputElement) {
    if (!activeField) return;
    
    // Create new span element with original value
    const spanElement = document.createElement('span');
    spanElement.id = fieldId;
    
    // Get the original value based on the field ID
    let originalValue;
    switch(fieldId) {
        case 'guest-name':
            originalValue = currentData.guestName;
            break;
        case 'nationality':
            originalValue = currentData.nationality;
            break;
        case 'phone':
            originalValue = currentData.phone;
            break;
    }
    
    spanElement.textContent = originalValue;
    
    // Replace input with span
    inputElement.parentNode.replaceChild(spanElement, inputElement);
    
    // Reset edit button
    if (activeField.editButton) {
        activeField.editButton.innerHTML = '<img src="icon/edit.svg" alt="Edit">';
        activeField.editButton.classList.remove('editing');
        
        // Reset click event
        const button = activeField.editButton;
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        // Add the original click event back
        newButton.addEventListener('click', function() {
            // If another field is being edited, save it first
            if (activeField) {
                saveInlineEdit(activeField.id, activeField.input);
            }
            
            const fieldId = this.getAttribute('data-field');
            
            // Skip if this is the email field
            if (fieldId === 'email') {
                return;
            }
            
            const editableField = this.closest('.editable-field');
            const spanElement = document.getElementById(fieldId);
            const currentValue = spanElement.textContent;
            
            // Create input element
            const inputElement = document.createElement('input');
            inputElement.type = fieldId === 'phone' ? 'tel' : 'text';
            inputElement.value = currentValue;
            inputElement.className = 'editable-field-input';
            
            // Replace span with input
            spanElement.parentNode.replaceChild(inputElement, spanElement);
            
            // Change edit button to save button
            this.innerHTML = '<img src="icon/checkmark.svg" alt="Check">';
            this.classList.add('editing');
            
            // Focus on the input
            inputElement.focus();
            
            // Save active field
            activeField = {
                id: fieldId,
                input: inputElement,
                originalValue: currentValue,
                editButton: this
            };
            
            // Add event listeners for input
            inputElement.addEventListener('blur', function(e) {
                // Don't trigger blur if clicking the save button
                if (e.relatedTarget !== activeField.editButton) {
                    saveInlineEdit(fieldId, inputElement);
                }
            });
            
            inputElement.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    saveInlineEdit(fieldId, inputElement);
                } else if (e.key === 'Escape') {
                    cancelInlineEdit(fieldId, inputElement);
                }
            });
            
            // Add click event to the edit button (now a save button)
            this.removeEventListener('click', arguments.callee);
            this.addEventListener('click', function() {
                saveInlineEdit(fieldId, inputElement);
            });
        });
    }
    
    // Reset active field
    activeField = null;
}

// Validate email format
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Helper function to compare objects
function isEqual(obj1, obj2) {
    return JSON.stringify(obj1) === JSON.stringify(obj2);
} 