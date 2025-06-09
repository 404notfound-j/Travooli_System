document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.hotel-book-info-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let valid = true;
            form.querySelectorAll('input[required], select[required]').forEach(function(input) {
                if (!input.value) {
                    input.style.border = '1.5px solid #e74c3c';
                    valid = false;
                } else {
                    input.style.border = '';
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
});
