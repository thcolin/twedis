<?php
	
	# Errors
	
	error_reporting(E_ALL ^ E_STRICT);
	
	# Timezone
	
	date_default_timezone_set('Europe/Paris');
	
	# Include Composer autoloader
	
	require dirname(dirname(__DIR__)).'/vendor/autoload.php';
	
	# Return application configuration
	
	return array(
		'name' => 'Twedis',
		'debug' => false,
		'dev' => false,
		'auth' => array(
			'driver' => 'App\Auth\Simple'
		),
		'custom' => array(
			'secretKey' => '39JzaKCec38bdeDECezcj32DBE9391'
		)
	);

?>