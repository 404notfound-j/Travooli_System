<?php
ob_start();
?>

<?php
include 'connection.php';

// Fetch users from user_detail_t
$users = [];
$query = "SELECT user_id, fst_name, lst_name, email_address FROM user_detail_t";
$result = mysqli_query($connection, $query);
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
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
            <th>User ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usersToShow as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['user_id']) ?></td>
            <td><?= htmlspecialchars($user['fst_name']) ?></td>
            <td><?= htmlspecialchars($user['lst_name']) ?></td>
            <td><a href="mailto:<?= htmlspecialchars($user['email_address']) ?>"><?= htmlspecialchars($user['email_address']) ?></a></td>
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






