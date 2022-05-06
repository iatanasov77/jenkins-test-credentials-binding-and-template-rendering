<?php
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createMutable( dirname( __DIR__ ) );
$dotenv->load();

echo '<h1>Jenkins Test Deploy is DONE !!!</h1>';
echo '<p>================================================</p>';

echo '<h2>PHP VERSION: '. phpversion() . '</h2>';
echo '<h2>PHP ENV: '. $_ENV['APP_ENV'] . '</h2>';
echo '<h2>DATABASE_URL: '. $_ENV['DATABASE_URL'] . '</h2>';

