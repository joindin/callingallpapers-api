<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

//$app->add(new \Callingallpapers\Api\Middleware\NotifyGoogleAnalytics(
//    $app->getContainer()->get('settings')['ga']['trackingCode'],
//    'DACE4DF8-9A9F-4CAA-9479-F7C1757DA60E'
//));
$app->add(new \Callingallpapers\Api\Middleware\CORS($app));
$app->add(new \Callingallpapers\Api\Middleware\OAuth($app));
$app->add(new \Callingallpapers\Api\Middleware\Renderer($app));
$app->add(new \Callingallpapers\Api\Middleware\ConvertTypeToAccept());
$app->add(new Org_Heigl\Middleware\Clacks\Clacks());
