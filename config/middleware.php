<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(new \Callingallpapers\Api\Middleware\OAuth($app));
$app->add(new \Callingallpapers\Api\Middleware\Renderer($app));
