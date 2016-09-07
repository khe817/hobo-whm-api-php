<?php
// --- general settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . 'class.whm_api.php';

$whm_api = new WHM_API('1.0.0.127', 'whm_username', 'hash_or_pass', 'cpanel_username');

try {
	$params = array(
		'domain' => 'example.com',
		);

	$test = $whm_api->cpanel_api2( 'MysqlFE', 'listdbs', $params);

	// DEBUG: output array to screen
	echo '<pre>';
	echo HtmlSpecialChars(var_export($test, 1));
	echo '</pre>';
} catch (Exception $e) {
	echo PHP_EOL . 'Failed to process request:' . PHP_EOL . $e->getMessage();
}

exit();
// eof