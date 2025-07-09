<?php
include 'connection.php';

$records = [];

$query ="
    SELECT
        fbt.flight_id,
        fbt.flight_booking_id,
        CONCAT(ud.fst_name, ' ', ud.lst_name) AS username,
        fpt.payment_method,
        fpt.amount,
        fpt.payment_status,
        fpt.payment_date
    FROM flight_payment_t fpt
    JOIN flight_booking_t fbt ON fpt.f_book_id = fbt.flight_booking_id
    JOIN user_detail_t ud ON fbt.user_id = ud.user_id
    ORDER BY fpt.payment_date DESC
";

$result = mysqli_query($connection, $query);

if (!$result) {
    die('Query error: ' . mysqli_error($connection));
}

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = [
            'id' => $row['flight_id'],
            'booking_reference' => $row['flight_booking_id'],
            'username' => $row['username'],
            'method' => $row['payment_method'],
            'amount' => 'RM ' . number_format($row['amount'], 2, '.', ''),
            'status' => $row['payment_status'],
            'date' => date('d M Y', strtotime($row['payment_date'])),
        ];
    }
}

$perPage = 10;
$totalRecords = count($records);
$totalPages = ceil($totalRecords / $perPage);
$page = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$start = ($page - 1) * $perPage;
$recordsToShow = array_slice($records, $start, $perPage);
?>
