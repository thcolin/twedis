<?php
	
	$projectDir = dirname(dirname(__DIR__));

	$return = array(
		'app' => array(
			'directory' => $projectDir.'/app/assets'
		)
	);
	
	if(is_file($projectDir.'/.bowerrc'))
	
		$return['bower']['bowerrc'] = json_decode(file_get_contents($projectDir.'/.bowerrc'), true);
	
	if(is_file($projectDir.'/bower.json'))
	
		$return['bower']['config'] = json_decode(file_get_contents($projectDir.'/bower.json'), true);
		
	return $return;
	
?>