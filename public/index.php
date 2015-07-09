<?php

	/* App Configuration */

	$config = include dirname(__DIR__).'/app/config/app.php';
	
	/* Creating the app object */
	
	$app = \App\Core\App::getInstance($config);
	
	include(dirname(__DIR__).'/app/config/vars.php');
	
	/* If match the URL, no problem */
	
	if($match = $app -> router -> match()){
		
		/* Add the matched route to the Application */
		
		$app -> router -> match = $match;
		
		/* Controller */
		
		if(is_array($match['target'])){
		
			list($class, $method) = $match['target'];
			$controller = new $class();
			
			$match['target'] = array($controller, $method);
		
		}
	
		/* Call the function if it's callable */
	
		if(is_callable($match['target']))
		
			call_user_func_array($match['target'], $match['params']);
		
		/* Else 500 */
		
		else
		
			$app -> httpError(500, 'Internal Server Error', 'Error with the declared function for this route.');
		
	}
	
	/* Else 404 */
	
	else
	
		$app -> httpError(404, 'Not Found', 'The requested URL was not found on this server.');

?>