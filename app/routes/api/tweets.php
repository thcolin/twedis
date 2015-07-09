<?php

	$this -> map('POST', '/api/tweet', 'API\Tweets@tweet');
	$this -> map('GET', '/api/tweets/user/[*:username]/[i:page]', 'API\Tweets@getByUsername');
	$this -> map('GET', '/api/tweets/hashtag/[*:hashtag]/[i:page]', 'API\Tweets@getByHashtag');
	$this -> map('GET', '/api/tweets/timeline/[i:page]', 'API\Tweets@getTimeline');
	
?>