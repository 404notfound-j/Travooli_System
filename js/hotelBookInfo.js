document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.hotel-book-info-form');
    const nationalitySelect = document.getElementById('nationality');
    const otherNationalityInput = document.getElementById('other_nationality');
    
    // Handle nationality dropdown change
    if (nationalitySelect && otherNationalityInput) {
        // Initially hide the other nationality field
        otherNationalityInput.style.display = 'none';
        
        nationalitySelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                otherNationalityInput.style.display = 'block';
                otherNationalityInput.required = true;
                otherNationalityInput.focus();
            } else {
                otherNationalityInput.style.display = 'none';
                otherNationalityInput.required = false;
                otherNationalityInput.value = '';
            }
        });
    }
    
    if (form) {
        form.addEventListener('submit', function(e) {
            let valid = true;
            form.querySelectorAll('input[required]:not([style*="display: none"]), select[required]').forEach(function(input) {
                if (!input.value) {
                    input.style.border = '1.5px solid #e74c3c';
                    valid = false;
                } else {
                    input.style.border = '';
                }
            });
            
            // Special validation for "Other" nationality
            if (nationalitySelect && nationalitySelect.value === 'Other' && otherNationalityInput) {
                if (!otherNationalityInput.value.trim()) {
                    otherNationalityInput.style.border = '1.5px solid #e74c3c';
                    valid = false;
                } else {
                    otherNationalityInput.style.border = '';
                }
            }
            
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
});
