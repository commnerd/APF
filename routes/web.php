<?php

return array(
	array('GET', '/', '\App\Controllers\HelloWorldController#index', 'home'),
	array('POST', '/', '\App\Controllers\HelloWorldController#store', 'store'),
	array('RESOURCE', 'sections', '\App\Controllers\SectionController'),
	array('RESOURCE', 'entries', '\App\Controllers\EntryController'),
);

?>
