<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

//$app->add(new \Callingallpapers\Api\Middleware\NotifyGoogleAnalytics(
//    $app->getContainer()->get('googleAnalytics')
//));
$app->add(new \Callingallpapers\Api\Middleware\CORS($app));
$app->add(new \Callingallpapers\Api\Middleware\OAuth($app));
$app->add(new \Callingallpapers\Api\Middleware\Renderer($app));
$app->add(new \Callingallpapers\Api\Middleware\ConvertTypeToAccept());
$app->add(new Org_Heigl\Middleware\Clacks\Clacks());
$app->add(new RKA\Middleware\IpAddress(true));

