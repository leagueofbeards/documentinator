<?php
namespace Habari;
if ( !defined( 'HABARI_PATH' ) ) { die( 'No direct access' ); }
Config::set( 'db_connection', array(
	'connection_string'=>'mysql:host=localhost;dbname=docs',
	'username'=>'root',
	'password'=>'walker0376',
	'prefix'=>'docs__'
));

// Config::set('locale', 'en-us');
?>

