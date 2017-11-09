<?php
date_default_timezone_set('Europe/Paris');
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../pdf/fpdf.php';
require_once __DIR__.'/../pdf/pdf.php';
$app = new Silex\Application();
$app['debug'] = true;
require_once __DIR__.'/../app/services.php';
require_once __DIR__.'/../app/routes.php';
require_once __DIR__.'/../app/controleurs.php';
$app->run();

