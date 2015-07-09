<?php

	$this -> map('POST', '/api/user/[*:username]/toggleFollow/[i:bool]', 'API\User@toggleFollow');
	$this -> map('GET', '/api/user/[*:username]', 'API\User@getByUsername');
	$this -> map('GET', '/api/user', 'API\User@getCurrentUser');
	
?>