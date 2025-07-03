<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Management System</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,500,600,700,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/adminSIdebar.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <h1>Travooli ✈</h1>
            <span>Management System</span>
        </div>
        
        <nav class="nav-menu">
            <a href="#" class="nav-item active">Dashboard</a>
            <a href="#" class="nav-item">Flight</a>
            <a href="#" class="nav-item">Booking</a>
            <a href="#" class="nav-item">Report</a>
            <a href="#" class="nav-item">Payment</a>
            <a href="#" class="nav-item">User Management</a>
            
            <div class="pages-section">
                <div class="section-title">PAGES</div>
                <a href="#" class="nav-item">Calendar</a>
                <a href="#" class="nav-item">To-Do</a>
                <a href="#" class="nav-item">Contact</a>
                <a href="#" class="nav-item">Receipt & Ticket</a>
                <a href="#" class="nav-item">Team</a>
            </div>
        </nav>
        
        <div class="footer">
            <button class="logout-btn">Log Out</button>
            <div class="copyright">
                TSI Management System©2024.<br>
                All Rights Reserved.
            </div>
        </div>
    </div>
    <div class="main-content">
        <header class="header">
            <div class="user-profile">
                <div class="user-avatar">MR</div>
                <div class="user-info">
                    <h4>Moni Roy</h4>
                    <span>Admin</span>
                </div>
                <svg class="dropdown-arrow" viewBox="0 0 12 12">
                    <path d="M6 8L2 4h8L6 8z"/>
                </svg>
            </div>
        </header>
    </div>
    <script src="js/adminSIdebar.js"></script>
</body>
</html>