// Profile Picture Upload Functions
function triggerFileUpload() {
    document.getElementById('profile_pic').click();
}

// Handle file selection and auto-submit
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_pic');
    const form = document.querySelector('.profile-form');
    
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type.toLowerCase())) {
                showErrorMessage('Only JPEG, PNG, and GIF images are allowed.');
                e.target.value = ''; // Clear the input
                return;
            }
            
            // Validate file size (5MB = 5 * 1024 * 1024 bytes)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                showErrorMessage('Profile picture must be smaller than 5MB.');
                e.target.value = ''; // Clear the input
                return;
            }
            
            // Show loading message and submit form
            showSuccessMessage('Uploading profile picture...');
            
            // Auto-submit the form after a brief delay to show the message
            setTimeout(() => {
                form.submit();
            }, 500);
        }
    });
    
    // Show any messages from PHP processing
    const messageElement = document.getElementById('profile-message');
    if (messageElement) {
        const messageType = messageElement.getAttribute('data-type');
        const messageText = messageElement.textContent;
        
        if (messageType === 'success') {
            showSuccessMessage(messageText);
        } else if (messageType === 'error') {
            showErrorMessage(messageText);
        }
    }
});

// Message display functions
function showSuccessMessage(message) {
    showMessage(message, 'success');
}

function showErrorMessage(message) {
    showMessage(message, 'error');
}

function showMessage(message, type) {
    // Remove any existing messages
    const existingMessage = document.querySelector('.floating-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create new message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `floating-message ${type}`;
    messageDiv.textContent = message;
    
    // Add to body
    document.body.appendChild(messageDiv);
    
    // Show message with animation
    setTimeout(() => {
        messageDiv.classList.add('show');
    }, 100);
    
    // Hide message after 4 seconds
    setTimeout(() => {
        messageDiv.classList.remove('show');
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 300);
    }, 4000);
}

// Form validation helpers
function validateFileName(fileName) {
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    const fileExtension = fileName.split('.').pop().toLowerCase();
    return allowedExtensions.includes(fileExtension);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
} 