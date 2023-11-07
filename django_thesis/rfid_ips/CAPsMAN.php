require 'vendor/autoload.php'; // Include Composer's autoloader

<?php
set_time_limit(0);
$userIp = '192.168.203.2'; //ip here
$username = 'thesis2.0'; //password here
$password = 'admin'; //username here
$wlan = 0; //wlan id here
$scan_duration = 6; //Duration of scan in seconds here
$connection_timeout = 30; //API connection timeout here (set it bigger than scan_duration)

$client = new RouterOS\Client($userIp, $username, $password, null, false, $connection_timeout, Transmitter\NetworkStream::CRYPTO_TLS);
$setRequest = new RouterOS\Request('/caps-man/interface/scan cap2');
$responses = $client->sendSync($setRequest
                ->setArgument('duration', 6)
                ->setArgument('number', $wlan)
                );
$networks = array();
foreach ($responses as $response) {
	if ($response->getType() === RouterOS\Response::TYPE_DATA) {
	$mac = $response->getArgument('address');
	$ssid = $response->getArgument('ssid');
	$freq = $response->getArgument('freq');
	$sig = $response->getArgument('sig');
	$snr = $response->getArgument('snr');
	$radio_name = $response->getArgument('radio-name');

	$networks[$mac] = array(
	'ssid' => $ssid,
	'freq' => $freq,
	'sig' => $sig,
	'snr' => $snr,
	'radio_name' => $radio_name
	);
	}
}

print_r($networks);
?>