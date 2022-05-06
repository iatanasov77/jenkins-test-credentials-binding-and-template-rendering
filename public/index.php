<?php
require_once dirname( __DIR__ ) . '/../vendor/autoload_runtime.php';
$dotenv = new Dotenv\Dotenv();
$dotenv->load( dirname( __DIR__ ) );

echo '<h1>Jenkins Test Deploy is DONE !!!</h1>';
echo '<p>================================================</p>';

echo '<h2>PHP VERSION: '. phpversion() . '</h2>';
echo '<h2>PHP ENV: '. $_ENV['APP_ENV'] . '</h2>';
echo '<h2>DATABASE_URL: '. $_ENV['DATABASE_URL'] . '</h2>';

