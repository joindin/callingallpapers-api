<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

$container['pdo'] = function($c) {
    $settings = $c->get('settings')['db'];
    $db = new PDO($settings['dsn'], $settings['username'], $settings['password']);

    return $db;
};

$container['timezoneHelper'] = function($c) {
    $tzh = \Org_Heigl\PdoTimezoneHelper\PdoTimezoneHelper::create($c->get('pdo'));
    $tzh->setTimezoneField($c->get('settings')['db']['timezonefield']);

    return $tzh;
};

$container['googleAnalytics'] = function($c) {
    $ga = new \TheIconic\Tracking\GoogleAnalytics\Analytics(true);
    $ga->setProtocolVersion(1)
       ->setTrackingId($c->get('settings')['ga']['trackingCode'])
       ->setClientId(\Sabre\VObject\UUIDUtil::getUUID())
       ->setAnonymizeIp(true)
       ->setAsyncRequest(true);

    return $ga;
};
