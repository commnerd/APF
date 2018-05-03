<?php

return array(
	array('GET', '/', '\App\Controllers\WelcomeController#index', 'home'),
	array('RESOURCE', 'section', '\App\Controllers\SectionController'),
);

?>
