<?php

	$this -> map('POST', '/api/register', 'API\Auth@register');
	$this -> map('POST', '/api/login', 'API\Auth@login');
	
?>