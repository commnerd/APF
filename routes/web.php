<?php

return array(
	array('GET', '/', '\App\Controllers\WelcomeController#index', 'home'),
	array('RESOURCE', 'posts', '\App\Controllers\PostController'),
	array('RESOURCE', 'comments', '\App\Controllers\CommentController'),
);

?>
