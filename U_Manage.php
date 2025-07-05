<?php
ob_start();
?>

<?php
// Example user data (simulate database)
$users = [];
for ($i = 1; $i <= 50; $i++) {
    $users[] = [
        'username' => "user$i",
        'first_name' => "First$i",
        'last_name' => "Last$i",
        'email' => "user$i@example.com",
        'last_login' => "25 Jan 2025 - 08:30"
    ];
}

// Pagination logic
$perPage = 10;
$totalUsers = count($users);
$totalPages = ceil($totalUsers / $perPage);
$page = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$start = ($page - 1) * $perPage;
$usersToShow = array_slice($users, $start, $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="css/U_Manage.css">
</head>
<body>
<h1 class="page-title">User Management</h1>
<div class="user-list-label">Users List</div>

<table class="user-table">
    <thead>
        <tr>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Last Login</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usersToShow as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['first_name']) ?></td>
            <td><?= htmlspecialchars($user['last_name']) ?></td>
            <td><a href="mailto:<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></a></td>
            <td><?= htmlspecialchars($user['last_login']) ?></td>
            <td>
                <button class="modify-btn">Modify</button>
                <button class="delete-btn">Delete</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination-wrapper">
    <div class="pagination">
        <span>
            Showing <?= $start + 1 ?>-<?=
                min($start + $perPage, $totalUsers)
            ?> of <?= $totalUsers ?>
        </span>
        <div class="pagination-arrows">
            <a href="?page=<?= $page - 1 ?>" class="arrow <?= $page == 1 ? 'disabled' : '' ?>">&lt;</a>
            <a href="?page=<?= $page + 1 ?>" class="arrow <?= $page == $totalPages ? 'disabled' : '' ?>">&gt;</a>
        </div>
    </div>
</div>

<?php
    // Capture the content and store it in a variable
    $pageContent = ob_get_clean();

    // Include the admin sidebar layout
    include 'adminSidebar.php';
?>

</body>
</html>






