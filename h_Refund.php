<?php
$records = [
    [
        'id' => 'RF00123456',
        'booking_reference' => 'BK009876543',
        'username' => 'Jane Smith',
        'method' => 'Paypal',
        'amount' => 'RM 2,100',
        'status' => 'Completed',
        'date' => '26 Jan 2025',
    ],
    [
        'id' => 'RF00123456',
        'booking_reference' => 'BK009876543',
        'username' => 'Jane Smith',
        'method' => 'Paypal',
        'amount' => 'RM 2,100',
        'status' => 'Completed',
        'date' => '26 Jan 2025',
    ]
];
$perPage = 10;
$totalRecords = count($records);
$totalPages = ceil($totalRecords / $perPage);
$page = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$start = ($page - 1) * $perPage;
$recordsToShow = array_slice($records, $start, $perPage);

?>