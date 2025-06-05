<?php
include('config.php');

// Get Access Token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://test.api.amadeus.com/v1/security/oauth2/token");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'client_credentials',
    'client_id' => $client_id,
    'client_secret' => $client_secret
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$access_token = $data['access_token'];

if (!$access_token) {
    die("Failed to get access token.");
}
?>

<?php
// Set search parameters
$origin = 'KUL';
$destination = 'SIN';
$departureDate = '2025-06-15';
$adults = 1;

$url = "https://test.api.amadeus.com/v2/shopping/flight-offers?"
     . "originLocationCode=$origin"
     . "&destinationLocationCode=$destination"
     . "&departureDate=$departureDate"
     . "&adults=$adults"
     . "&max=5";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token"
]);
$response = curl_exec($ch);
curl_close($ch);

$flights = json_decode($response, true);

foreach ($flights['data'] as $offer) {
    $eur_price = $offer['price']['total'];
    $eur_to_myr = 5.00;
    $myr_price = $eur_price * $eur_to_myr;

    foreach ($offer['itineraries'][0]['segments'] as $segment) {
        $carrier = $segment['carrierCode'];
        $flight_number = $segment['number'];
        $departure = $segment['departure']['iataCode'] . ' at ' . $segment['departure']['at'];
        $arrival = $segment['arrival']['iataCode'] . ' at ' . $segment['arrival']['at'];

        echo "$carrier $flight_number: $departure → $arrival | Price: MYR " . number_format($myr_price, 2) . "<br>";
    }
}

// Display results
echo "<pre>";
print_r($flights);
echo "</pre>";
?>
