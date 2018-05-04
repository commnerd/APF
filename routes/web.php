<?php

return array(
	array('GET', '/', '\App\Controllers\WelcomeController#index', 'home'),
	array('RESOURCE', 'sections', '\App\Controllers\SectionController'),
);

?>
