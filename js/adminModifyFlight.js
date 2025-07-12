// Get booking ID from URL
function getBookingIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('bookingId') || '';
}

// Store booking ID for use throughout the script
const bookingId = getBookingIdFromUrl();
    
// Function to open cancel flight modal
function openCancelFlightModal() {
    document.getElementById('cancelFlightModal').style.display = 'flex';
}

// Function to close modal
function closeModal() {
    document.getElementById('cancelFlightModal').style.display = 'none';
}

// Function to confirm cancellation
function confirmCancelFlight() {
    // Use AJAX to cancel the flight booking
    fetch('cancelFlight.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            booking_id: bookingId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Flight booking has been successfully cancelled and refund processed.');
            // Redirect to booking list page after successful cancellation
            window.location.href = 'adminFlightBooking.php';
        } else {
            alert('Error: ' + (data.message || 'Failed to cancel booking'));
            console.error('Error details:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while cancelling the booking.');
    });
    
    // Close the modal
    closeModal();
}

// Initialize editable fields
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit buttons for editable fields
        const editButtons = document.querySelectorAll('.edit-btn');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
            const fieldContainer = this.closest('.editable-field');
            const span = fieldContainer.querySelector('span');
                
                // If already in edit mode, save the changes
            if (fieldContainer.classList.contains('editing')) {
                const input = fieldContainer.querySelector('.edit-input');
                const newValue = input.value.trim();
                    
                if (newValue) {
                    span.textContent = newValue;
                }
                    
                // Remove input field
                input.remove();
                    
                // Make span visible again
                span.style.display = '';
                
                // Change button back to edit icon
                this.innerHTML = '<i class="fas fa-pen" style="color: #4379EE;"></i>';
                
                // Remove editing class
                fieldContainer.classList.remove('editing');
                    
                // Ensure button is visible
                this.style.display = '';
                } else {
                // Enter edit mode
                const currentValue = span.textContent;
                
                    // Create input field
                const input = document.createElement('input');
                input.type = 'text';
                input.value = currentValue;
                input.className = 'edit-input';
                    
                // Add input before span
                fieldContainer.insertBefore(input, span);
                    
                // Hide span
                span.style.display = 'none';
                    
                // Change button to tick icon
                this.innerHTML = '<i class="fas fa-check" style="color: #4379EE;"></i>';
                
                // Add editing class
                fieldContainer.classList.add('editing');
                
                // Focus input
                input.focus();
                    
                // Handle Enter key
                input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                        button.click(); // Trigger save by clicking the button
                        }
                    });
                    
                // Handle Escape key
                input.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                        // Cancel editing
                        input.remove();
                        span.style.display = '';
                        button.innerHTML = '<i class="fas fa-pen" style="color: #4379EE;"></i>';
                        fieldContainer.classList.remove('editing');
                        button.style.display = ''; // Ensure button is visible
                        }
                    });
                }
            });
        });

    // Handle class and meal selection changes
    const classSelects = document.querySelectorAll('.class-select');
    const mealSelects = document.querySelectorAll('.meal-select');
    
    // Price mapping for different seat classes
    const classPrices = {
        'Economy': 0,
        'Premium Economy': 150,
        'Business': 500,
        'First': 1000
    };
    
    // Price mapping for different meal types
    const mealPrices = {
        'N/A': 0,
        'Single meal': 15,
        'Multi-meal': 30
    };
    
    // Function to recalculate fares when selections change
    function recalculateFares() {
        let additionalAmount = 0;
        let hasChanges = false;
        
        // Check class changes
        classSelects.forEach(select => {
            const originalClass = select.getAttribute('data-original');
            const currentClass = select.value;
            
            if (originalClass !== currentClass) {
                hasChanges = true;
                // Calculate price difference
                additionalAmount += classPrices[currentClass] - classPrices[originalClass];
            }
        });
        
        // Check meal changes
        mealSelects.forEach(select => {
            const originalMeal = select.getAttribute('data-original');
            const currentMeal = select.value;
            
            if (originalMeal !== currentMeal) {
                hasChanges = true;
                // Calculate price difference
                additionalAmount += mealPrices[currentMeal] - mealPrices[originalMeal];
            }
        });
        
        // Get original total and modification fee
        const originalTotal = parseFloat(document.getElementById('original-total').value);
        const modificationFee = parseFloat(document.getElementById('modification-fee').value);
        
        // Calculate new totals
        const newTotal = originalTotal + additionalAmount;
        const finalTotal = hasChanges ? newTotal + modificationFee : newTotal;
        const additionalCharges = hasChanges ? additionalAmount + modificationFee : additionalAmount;
        
        // Update the display
        document.getElementById('current-total').textContent = formatCurrency(originalTotal);
        document.getElementById('new-total').textContent = formatCurrency(newTotal);
        document.getElementById('additional-charges').textContent = formatCurrency(additionalCharges);
        document.getElementById('final-total').textContent = formatCurrency(finalTotal);
    }
    
    // Helper function to format currency
    function formatCurrency(amount) {
        return amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // Add event listeners to selects
    classSelects.forEach(select => {
        select.addEventListener('change', recalculateFares);
    });
    
    mealSelects.forEach(select => {
        select.addEventListener('change', recalculateFares);
    });
    
    // Handle discard button
    const discardBtn = document.querySelector('.discard-btn');
    if (discardBtn) {
        discardBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to discard all changes?')) {
                window.location.reload();
        }
        });
    }
    
    // Handle save changes button
    const saveBtn = document.querySelector('.save-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            // Collect all changes
            const changes = collectChanges();
            
            // Add fare information to changes
            const originalTotal = parseFloat(document.getElementById('original-total').value);
            const finalTotal = parseFloat(document.getElementById('final-total').textContent.replace(/,/g, ''));
            
            changes.original_amount = originalTotal;
            changes.new_amount = finalTotal;
            
            // Send changes to server using AJAX
            fetch('updateFlightBooking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(changes)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Changes saved successfully!');
                    // Refresh the current page instead of redirecting
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save changes'));
                    console.error('Error details:', data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving changes.');
            });
        });
    }
    
    // Add ESC key listener to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            }
        });
});
    
    // Function to collect all changes made to the booking
    function collectChanges() {
        const changes = {
        booking_id: bookingId,
            passengers: []
        };
        
        // Collect passenger data
        const passengerRows = document.querySelectorAll('.traveler-table tbody tr');
        passengerRows.forEach((row, index) => {
            const nameCell = row.querySelector('td:nth-child(1) .editable-field span');
            const ageGroupSelect = row.querySelector('td:nth-child(2) .dropdown-select select');
            const seatCell = row.querySelector('td:nth-child(3)');
            const classSelect = row.querySelector('td:nth-child(4) select');
            const mealSelect = row.querySelector('td:nth-child(5) select');
            const amountCell = row.querySelector('td:nth-child(6)');
            
            changes.passengers.push({
                name: nameCell ? nameCell.textContent.trim() : '',
                age_group: ageGroupSelect ? ageGroupSelect.value : '',
                seat_no: seatCell ? seatCell.textContent.trim() : '',
                class: classSelect ? classSelect.value : '',
                meal_type: mealSelect ? mealSelect.value : '',
                amount: amountCell ? amountCell.textContent.trim().replace('RM ', '') : '',
                original_class: classSelect ? classSelect.getAttribute('data-original') : '',
                original_meal: mealSelect ? mealSelect.getAttribute('data-original') : ''
            });
        });
        
        return changes;
    }