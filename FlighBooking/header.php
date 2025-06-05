<style>
/* Reset and Base Styles for Header */
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

ul {
    list-style: none;
}

a {
    text-decoration: none;
    color: inherit;
}

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    padding: 12px 20px;
    height: 48px;
    font-family: 'Nunito Sans', sans-serif;
}

.btn-primary {
    background-color: #605DEC;
    color: #FAFAFA;
}

.btn-primary:hover {
    background-color: #1513A0;
    box-shadow: 0 4px 8px rgba(96, 93, 236, 0.3);
}

/* Header Styles */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 24px;
    background-color: #031E2F;
    width: 100%;
    height: 70px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.logo-container {
    display: flex;
    align-items: center;
}

.logo {
    max-height: 42px;
    width: auto;
}

.nav-list {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 10px;
    color: #A4B0C7;
    font-family: 'Nunito Sans', sans-serif;
    font-size: 16px;
    font-weight: 400;
    transition: color 0.2s ease-in-out;
    position: relative;
}

.nav-item:hover {
    color: #605DEC;
}

.nav-item.active {
    color: #605DEC;
}

.nav-item.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #605DEC;
}

.nav-item .btn {
    height: 40px;
    padding: 8px 16px;
}

/* Responsive Styles for Header */
@media (max-width: 768px) {
    .header {
        flex-direction: column;
        padding: 16px;
        height: auto;
    }
    .nav-list {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travooli</title>
</head>
<body>
<header class="header">
    <div class="logo-container">
        <img src="../Images/Travooli logo.svg" alt="Travooli" class="logo">
    </div>
    <nav class="nav">
        <ul class="nav-list">
            <li class="nav-item active"><a href="#flights">Flights</a></li>
            <li class="nav-item"><a href="#hotels">Hotels</a></li>
            <li class="nav-item"><a href="#notifications">Notifications</a></li>
            <li class="nav-item"><a href="#tickets">My Tickets</a></li>
            <li class="nav-item"><a href="#reservations">My Reservations</a></li>
            <li class="nav-item"><a href="#signin">Sign in</a></li>
            <li class="nav-item"><button class="btn btn-primary">Sign up</button></li>
        </ul>
    </nav>
</header>

<script src="js/header.js"></script>
</body>
</html>
