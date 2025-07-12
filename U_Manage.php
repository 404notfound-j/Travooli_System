<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
} else {
    // Show a friendly error for admin
    if ($isAdmin && isset($_GET['user_id'])) {
        echo '<div style="color:red;text-align:center;margin:2em;">User not found or has been deleted.</div>';
        exit();
    } else {
        header("Location: signIn.php");
        exit();
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
    <script src="js/profile.js"></script>
</head>
<body>
<h1 class="page-title">User Management</h1>
<div class="user-list-label">Users List</div>

<div id="deleteUserModalContainer"></div>

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
                <button class="modify-btn" onclick="window.location.href='admin_edit_user.php?user_id=<?= htmlspecialchars($user['user_id']) ?>'">Modify</button>
                <button class="delete-btn" onclick="openDeleteUserPopup('<?= htmlspecialchars($user['user_id']) ?>', '<?= htmlspecialchars($user['fst_name']) ?>', '<?= htmlspecialchars($user['lst_name']) ?>')">Delete</button>
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

<!-- Delete Account Modal (hidden by default) -->
<div class="modal-overlay" id="deleteAccountModal" style="display:none;">
    <div class="modal-dialog">
        <div class="title-row">
            <h2 class="modal-title">Are you sure to delete this user?</h2>
        </div>
        <div class="modal-description">
            This action will permanently delete all data for this user. This cannot be undone.
        </div>
        <div class="modal-actions">
            <div class="button-row">
                <button class="btn btn-secondary" onclick="closeModal()">Back</button>
            </div>
        </div>
    </div>
</div>
<script>
let userIdToDelete = null;
function openDeleteUserPopup(userId, fstName, lstName) {
    // Load the popup HTML via AJAX
    fetch('dlt_acc_popup.php')
        .then(response => response.text())
        .then(html => {
            document.getElementById('deleteUserModalContainer').innerHTML = html;
            document.getElementById('deleteAccountModal').style.display = 'flex';

            // Fill in the user info in the modal
            document.getElementById('del-user-id').textContent = userId;
            document.getElementById('del-fst-name').textContent = fstName;
            document.getElementById('del-lst-name').textContent = lstName;

            // Attach the userId to the confirm button
            window.confirmDelete = function() {
                fetch('delete_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'user_id=' + encodeURIComponent(userId)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Failed to delete user: ' + (data.error || 'Unknown error'));
                        closeModal();
                    }
                })
                .catch(() => {
                    alert('Failed to delete user.');
                    closeModal();
                });
            };

            window.userIdToDelete = userId;
        });
}

function closeModal() {
    document.getElementById('deleteUserModalContainer').innerHTML = '';
}
</script>

<?php
    // Capture the content and store it in a variable
    $pageContent = ob_get_clean();

    // Include the admin sidebar layout
    include 'adminSidebar.php';
?>

</body>
</html>






