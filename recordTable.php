<?php
    ob_start();
?>

<?php
    $section = isset($_GET['section']) && $_GET['section'] === 'refund' ? 'refund' : 'payment';
    $type = isset($_GET['type']) && $_GET['type'] === 'hotel' ? 'hotel' : 'flight';

    // date file will be included
    if ($section === 'payment' && $type === 'flight') {
        include 'f_Payment.php';
    } elseif ($section === 'payment' && $type === 'hotel') {
        include 'h_Payment.php';
    } elseif ($section === 'refund' && $type === 'flight') {
        include 'f_Refund.php';
    } elseif ($section === 'refund' && $type === 'hotel') {
        include 'h_Refund.php';
    }

    // Set column headers dynamically
    $id_label = ($section === 'payment')
        ? ($type === 'flight' ? 'Flight ID' : 'Hotel ID')
        : 'Refund ID';
    $method_label = ($section === 'payment') ? 'Payment Method' : 'Refund Method';
    $status_label = ($section === 'payment') ? 'Status' : 'Refund Status';
    $date_label = ($section === 'payment') ? 'Payment Date' : 'Refund Date';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Records</title>
    <link rel="stylesheet" href="css/recordTable.css">
</head>
<body>
    <h1 class="page-title"><?= ucfirst($section) ?></h1>
    <div class="tabs"> 
        <?php if ($section === 'payment'): ?>
            <a href="recordTable.php?section=payment&type=flight" class="tab<?php echo ($section === 'payment' && $type === 'flight') ? ' active' : ''; ?>">Flight Payments</a>
            <a href="recordTable.php?section=payment&type=hotel" class="tab<?php echo ($section === 'payment' && $type === 'hotel') ? ' active' : ''; ?>">Hotel Payments</a>
        <?php else: ?>
            <a href="recordTable.php?section=refund&type=flight" class="tab<?php echo ($section === 'refund' && $type === 'flight') ? ' active' : ''; ?>">Flight Refunds</a>
            <a href="recordTable.php?section=refund&type=hotel" class="tab<?php echo ($section === 'refund' && $type === 'hotel') ? ' active' : ''; ?>">Hotel Refunds</a>
        <?php endif; ?>
    </div>

    <table class="payment-table">
        <thead>
            <tr>
                <th><?= htmlspecialchars($id_label)?></th>
                <th>Booking References</th>
                <th>Username</th>
                <th><?= htmlspecialchars($method_label)?></th>
                <th>Amount</th>
                <th><?= htmlspecialchars($status_label)?></th>
                <th><?= htmlspecialchars($date_label)?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($recordsToShow)): ?>
                <?php foreach ($recordsToShow as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']) ?></td>
                        <td><?php echo htmlspecialchars($row['booking_reference']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['method']); ?></td>
                        <td><?php echo htmlspecialchars($row['amount']); ?></td>
                        <td>
                            <?php
                                if ($row['status'] === 'Paid' || $row['status'] === 'completed') {
                                    $displayStatus = 'Completed';
                                    $badgeClass = 'Completed';
                                } elseif ($row['status'] === 'Refunded') {
                                    $displayStatus = 'Refunded';
                                    $badgeClass = 'Cancelled';
                                } else {
                                    $displayStatus = htmlspecialchars($row['status']);
                                    $badgeClass = preg_replace('/\s+/', '', $displayStatus);
                                }
                            ?>
                            <span class="status-badge <?php echo $badgeClass; ?>">
                                <?php echo $displayStatus; ?> 
                            </span>


                        </td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No payment records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination-wrapper">
        <div class="pagination">
            <span>
                Showing <?= $start + 1 ?>-<?= min($start + $perPage, $totalRecords) ?> of <?= $totalRecords ?>
            </span>
            <div class="pagination-arrows">
                <?php
                $queryParams = $_GET;
                // Previous
                $queryParams['page'] = $page - 1;
                $prevUrl = '?' . http_build_query($queryParams);
                ?>
                <a href="<?= $prevUrl ?>" class="arrow <?= $page == 1 ? 'disabled' : '' ?>">&lt;</a>
                <?php
                // Next
                $queryParams['page'] = $page + 1;
                $nextUrl = '?' . http_build_query($queryParams);
                ?>
                <a href="<?= $nextUrl ?>" class="arrow <?= $page == $totalPages ? 'disabled' : '' ?>">&gt;</a>
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
