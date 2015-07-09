<?php

	$params = array(
		'scheme' => 'tcp',
		'host' => '127.0.0.1',
		'port' => 6379	
	);
	
	$options = array(
		'prefix' => 'twedis:'
	);
	
	$app -> Redis = new Predis\Client($params, $options);
	
?>