<?php
include "connection.php";

$records = [];

$query="
    SELECT
    frt.f_refund_id,
    frt.f_book_id,
    CONCAT(ud.fst_name, ' ', ud.lst_name) AS username,
    frt.refund_method,
    frt.refund_amt,
    frt.status,
    frt.refund_date
    FROM flight_refund_t frt
    JOIN flight_booking_t fbt ON frt.f_book_id = fbt.f_book_id
    JOIN user_detail_t ud ON fbt.user_id = ud.user_id
";

$result = mysqli_query($connection, $query);

if (!$result) {
    die('Query error: ' . mysqli_error($connection));
}

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = [
            'id' => $row['f_refund_id'],
            'booking_reference' => $row['f_book_id'],
            'username' => $row['username'],
            'method' => $row['refund_method'],
            'amount' => 'RM ' . number_format($row['refund_amt'], 2, '.', ''),
            'status' => $row['status'],
            'date' => date('d M Y', strtotime($row['refund_date'])),
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