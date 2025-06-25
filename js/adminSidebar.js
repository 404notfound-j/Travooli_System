document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all items
        document.querySelectorAll('.nav-item').forEach(nav => {
            nav.classList.remove('active');
        });
        
        // Add active class to clicked item
        this.classList.add('active');
    });
});

// Logout functionality
document.querySelector('.logout-btn').addEventListener('click', function() {
    if (confirm('Are you sure you want to log out?')) {
        alert('Logging out...');
        // Here you would typically redirect to login page
    }
});

// Notification click
document.querySelector('.notification-btn').addEventListener('click', function() {
    alert('You have 6 new notifications!');
});

// Language selector
document.querySelector('.language-selector').addEventListener('click', function() {
    alert('Language selector clicked - dropdown would appear here');
});

// User profile click
document.querySelector('.user-profile').addEventListener('click', function() {
    alert('User profile menu would appear here');
});

// Mobile sidebar toggle (for responsive design)
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
}

// Add mobile menu button dynamically for small screens
if (window.innerWidth <= 768) {
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.innerHTML = '☰';
    mobileMenuBtn.style.cssText = `
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1000;
        background: #1e3a5f;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
    `;
    mobileMenuBtn.onclick = toggleSidebar;
    document.body.appendChild(mobileMenuBtn);
}