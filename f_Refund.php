<?php
$records = [
    [
        'id' => 'RF00187654',
        'booking_reference' => 'BK001234896',
        'username' => 'John Doe',
        'method' => 'Credit Card',
        'amount' => 'RM 1,750',
        'status' => 'Completed',
        'date' => '25 Jan 2025',
    ],
    [
        'id' => 'RF00187654',
        'booking_reference' => 'BK001234896',
        'username' => 'John Doe',
        'method' => 'Credit Card',
        'amount' => 'RM 1,750',
        'status' => 'Completed',
        'date' => '25 Jan 2025',
    ]
];
$perPage = 10;
$totalRecords = count($records);
$totalPages = ceil($totalRecords / $perPage);
$page = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$start = ($page - 1) * $perPage;
$recordsToShow = array_slice($records, $start, $perPage);
?>