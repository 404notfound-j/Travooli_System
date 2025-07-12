<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact</title>
  <link rel="stylesheet" href="css/adminTeam.css">
  <link rel="stylesheet" href="css/adminSidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php
ob_start();
?>
  <div class="team-section">
    <div class="team-header">
      <h2>Team</h2>
    </div>

    <div class="team-container">
      <div class="team-card">
      <img src="https://www.pngmart.com/files/23/Profile-PNG-Photo.png" alt="Profile Picture">
        <h3>Chua Siong Zheng</h3>
        <p class="role">Leader</p>
        <p class="email">edwinchua922@gmail.com</p>
      </div>

      <div class="team-card">
      <img src="https://www.pngmart.com/files/23/Profile-PNG-Photo.png" alt="Profile Picture">
        <h3>Choo Zhi Hong</h3>
        <p class="role">Member</p>
        <p class="email">peterzhihong@gmail.com</p>
      </div>

      <div class="team-card">
      <img src="https://www.pngmart.com/files/23/Profile-PNG-Photo.png" alt="Profile Picture">
        <h3>Joanne Lee Jia Tian</h3>
        <p class="role">Member</p>
        <p class="email">joannejietian@gmail.com</p>
      </div>

      <div class="team-card">
      <img src="https://www.pngmart.com/files/23/Profile-PNG-Photo.png" alt="Profile Picture">
        <h3>Cyndie Wong Xin Wei</h3>
        <p class="role">Member</p>
        <p class="email">weicyndie@gmail.com</p>
      </div>

      <div class="team-card">
      <img src="https://www.pngmart.com/files/23/Profile-PNG-Photo.png" alt="Profile Picture">
        <h3>Chan Kah Wun</h3>
        <p class="role">Member</p>
        <p class="email">kahwunchan05@gmail.com</p>
      </div>

      <div class="team-card">
      <img src="https://www.pngmart.com/files/23/Profile-PNG-Photo.png" alt="Profile Picture">
        <h3>Chung Wei Bin</h3>
        <p class="role">Member</p>
        <p class="email">aorus0217@gmail.com</p>
      </div>
    </div>
  </div>
  <?php
$pageContent = ob_get_clean();
include 'adminSidebar.php';
?>
</body>
</html>
