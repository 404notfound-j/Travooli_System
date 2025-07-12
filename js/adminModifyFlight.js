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
    
    // Store class prices from database
    const classPriceMap = {};
    
    // Function to fetch class price from server
    async function fetchClassPrice(classId, flightId) {
        try {
            const response = await fetch(`getClassPrice.php?class_id=${classId}&flight_id=${flightId}`);
            const data = await response.json();
            if (data.success && data.price) {
                return parseFloat(data.price);
            }
            return 0;
        } catch (error) {
            console.error('Error fetching class price:', error);
            return 0;
        }
    }

    // Function to fetch meal price from server
    async function fetchMealPrice(mealType) {
        try {
            const response = await fetch(`getMealPrice.php?meal_type=${encodeURIComponent(mealType)}`);
            const data = await response.json();
            if (data.success && data.price) {
                return parseFloat(data.price);
            }
            return 0;
        } catch (error) {
            console.error('Error fetching meal price:', error);
            return 0;
        }
    }

    // Store meal prices from database
    const mealPriceMap = {};
    
    // Initialize meal prices - will be replaced with fetched values
    mealPriceMap['N/A'] = 0;
    mealPriceMap['Single meal'] = 20;
    mealPriceMap['Multi-meal'] = 50;
    
    // Function to recalculate fares when selections change
    async function recalculateFares() {
        let newTotalAmount = 0;
        let hasChanges = false;
        
        // Process each passenger row
        for (let i = 0; i < classSelects.length; i++) {
            const classSelect = classSelects[i];
            const mealSelect = mealSelects[i];
            
            // Get current class ID and check if it changed
            const currentClassId = classSelect.value;
            const originalClassId = classSelect.getAttribute('data-original-id');
            const classChanged = currentClassId !== originalClassId;
            
            // Get current meal type and check if it changed
            const currentMeal = mealSelect.value;
            const originalMeal = mealSelect.getAttribute('data-original');
            const mealChanged = currentMeal !== originalMeal;
            
            // If either class or meal changed, mark as changed
            if (classChanged || mealChanged) {
                hasChanges = true;
            }
            
            // Get row and seat information for flight ID
            const row = classSelect.closest('tr');
            const seatCell = row.querySelector('td:nth-child(3)');
            const seatText = seatCell.textContent.trim();
            const flightIdMatch = seatText.match(/\(([^)]+)\)/);
            const flightId = flightIdMatch ? flightIdMatch[1] : '';
            
            // Get class price
            let classPrice = 0;
            if (flightId) {
                // If we don't have the price cached, fetch it
                if (!classPriceMap[currentClassId]) {
                    classPriceMap[currentClassId] = await fetchClassPrice(currentClassId, flightId);
                }
                classPrice = classPriceMap[currentClassId] || 0;
            }
            
            // Get meal price
            let mealPrice = mealPriceMap[currentMeal] || 0;
            if (!mealPriceMap[currentMeal] && currentMeal) {
                mealPrice = await fetchMealPrice(currentMeal);
                mealPriceMap[currentMeal] = mealPrice;
            }
            
            // Add to new total amount
            newTotalAmount += classPrice + mealPrice;
        }
        
        // Get modification fee
        const modificationFee = parseFloat(document.getElementById('modification-fee').value);
        
        // Calculate final totals
        const additionalCharges = hasChanges ? modificationFee : 0; // Apply RM50 fee only if changes were made
        const finalTotal = newTotalAmount + additionalCharges; // Final total is new total + additional charges
        
        // Update the display
        document.getElementById('current-total').textContent = formatCurrency(parseFloat(document.getElementById('original-total').value));
        document.getElementById('new-total').textContent = formatCurrency(newTotalAmount);
        document.getElementById('additional-charges').textContent = formatCurrency(additionalCharges);
        document.getElementById('final-total').textContent = formatCurrency(finalTotal);
    }
    
    // Helper function to format currency
    function formatCurrency(amount) {
        return amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // Add event listeners to selects
    classSelects.forEach(select => {
        select.addEventListener('change', () => recalculateFares());
    });
    
    mealSelects.forEach(select => {
        select.addEventListener('change', () => recalculateFares());
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
            // Check if any changes were made
            let hasChanges = false;
            classSelects.forEach(select => {
                if (select.value !== select.getAttribute('data-original-id')) {
                    hasChanges = true;
                }
            });
            
            mealSelects.forEach(select => {
                if (select.value !== select.getAttribute('data-original')) {
                    hasChanges = true;
                }
            });
            
            // If no changes were made, alert the user
            if (!hasChanges) {
                alert('No changes detected. Please make changes before saving.');
                return;
            }
            
            // Confirm with the user before saving
            if (!confirm('Are you sure you want to save these changes? This will update the booking details and may affect the fare.')) {
                return;
            }
            
            // Collect all changes
            const changes = collectChanges();
            
            // Add fare information to changes
            const originalTotal = parseFloat(document.getElementById('original-total').value);
            const finalTotal = parseFloat(document.getElementById('final-total').textContent.replace(/,/g, ''));
            
            // The actual new amount to save is the original amount plus the calculated changes
            const actualNewAmount = originalTotal + finalTotal;
            
            changes.original_amount = originalTotal;
            changes.new_amount = actualNewAmount;
            
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
        
        // Get totals for payment update
        const newTotal = parseFloat(document.getElementById('new-total').textContent.replace(/,/g, ''));
        const additionalCharges = parseFloat(document.getElementById('additional-charges').textContent.replace(/,/g, ''));
        const finalTotal = parseFloat(document.getElementById('final-total').textContent.replace(/,/g, ''));
        
        changes.new_amount = newTotal;
        changes.additional_charges = additionalCharges;
        changes.final_total = finalTotal;
        
        // Collect passenger data
        const passengerRows = document.querySelectorAll('.traveler-table tbody tr');
        passengerRows.forEach((row, index) => {
            const nameCell = row.querySelector('td:nth-child(1) .editable-field span');
            const ageGroupSelect = row.querySelector('td:nth-child(2) .dropdown-select select');
            const seatCell = row.querySelector('td:nth-child(3)');
            const classSelect = row.querySelector('td:nth-child(4) select');
            const mealSelect = row.querySelector('td:nth-child(5) select');
            const amountCell = row.querySelector('td:nth-child(6)');
            
            // Extract flight ID from seat number
            const seatText = seatCell ? seatCell.textContent.trim() : '';
            const flightIdMatch = seatText.match(/\(([^)]+)\)/);
            const flightId = flightIdMatch ? flightIdMatch[1] : '';
            
            changes.passengers.push({
                name: nameCell ? nameCell.textContent.trim() : '',
                age_group: ageGroupSelect ? ageGroupSelect.value : '',
                seat_no: seatCell ? seatCell.textContent.trim() : '',
                class_id: classSelect ? classSelect.value : '',
                flight_id: flightId,
                meal_type: mealSelect ? mealSelect.value : '',
                amount: amountCell ? amountCell.textContent.trim().replace('RM ', '').replace(',', '') : '',
                original_class_id: classSelect ? classSelect.getAttribute('data-original-id') : '',
                original_meal: mealSelect ? mealSelect.getAttribute('data-original') : ''
            });
        });
        
        return changes;
    }