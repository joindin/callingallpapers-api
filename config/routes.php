<?php
// Routes
$app->get('/', function(
    \Psr\Http\Message\ServerRequestInterface $request,
    \Psr\Http\Message\ResponseInterface $response
) {
    return $this->view->render($response, [], 200, 'cfp/index.twig');
});
$app->get('/v1/cfp', function(
    \Psr\Http\Message\ServerRequestInterface $request,
    \Psr\Http\Message\ResponseInterface $response
) use ($app){
    $cpl = new \Callingallpapers\Api\PersistenceLayer\CfpPersistenceLayer(
        $app->getContainer()['pdo'],
        $app->getContainer()['timezoneHelper']
    );
    $list = $cpl->getCurrent();

    $cfpMapper = new \Callingallpapers\Api\Entity\CfpListMapper();

    return $this->view->render($response, $cfpMapper->map($list), 200, 'cfp/list.twig');
});

$app->get('/v1/cfp/{hash}', function(
    \Psr\Http\Message\ServerRequestInterface $request,
    \Psr\Http\Message\ResponseInterface $response,
    array $args
) use ($app){

    $cpl = new \Callingallpapers\Api\PersistenceLayer\CfpPersistenceLayer(
        $app->getContainer()['pdo'],
        $app->getContainer()['timezoneHelper']
    );
    $list = $cpl->select($args['hash']);

    $cfpMapper = new \Callingallpapers\Api\Entity\CfpListMapper();

    return $this->view->render($response, $cfpMapper->map($list), 200, 'cfp/list.twig');
});

$app->post('/v1/cfp', function(
    \Psr\Http\Message\ServerRequestInterface $request,
    \Psr\Http\Message\ResponseInterface $response
) use ($app){
    $params = $request->getParsedBody();
    if (! is_array($params)) {
        throw new UnexpectedValueException('Expected array');
    }

    $cfpFactory = new \Callingallpapers\Api\Service\CfpFactory();
    $cfp = $cfpFactory->createCfp($params);

    $cpl = new \Callingallpapers\Api\PersistenceLayer\CfpPersistenceLayer(
        $app->getContainer()['pdo'],
        $app->getContainer()['timezoneHelper']
    );
    $cpl->insert($cfp);

    $uri = $request->getUri();
    $uri = $uri->withPath('v1/cfp/' . $cfp->getId());
    return $response->withRedirect((string)$uri, 201);
});


$app->put('/v1/cfp/{hash}', function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Psr\Http\Message\ResponseInterface $response,
    array $args
) use ($app){
    $params = $request->getParsedBody();
    if (! is_array($params)) {
        throw new UnexpectedValueException('Expected array');
    }

    $cfpFactory = new \Callingallpapers\Api\Service\CfpFactory();
    $cfp = $cfpFactory->createCfp($params);

    $cpl = new \Callingallpapers\Api\PersistenceLayer\CfpPersistenceLayer(
        $app->getContainer()['pdo'],
        $app->getContainer()['timezoneHelper']
    );
    $cpl->update($cfp, $args['hash']);

    $uri = $request->getUri();
    $uri = $uri->withPath('v1/cfp/' . $cfp->getId());
    return $response->withHeader('Location', (string)$uri)->withStatus(204);
});


$app->delete('/v1/cfp/{id}', function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Psr\Http\Message\ResponseInterface $response
) use ($app){
    $params = $request->getParsedBody();
    $cpl = new \Callingallpapers\Api\PersistenceLayer\CfpPersistenceLayer(
        $app->getContainer()['pdo'],
        $app->getContainer()['timezoneHelper']
    );
    $cpl->delete($params['hash']);

    return $response->withHeader('Content-Length', '0')->withStatus(204);
});

$app->get('/v1/search', function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Psr\Http\Message\ResponseInterface $response,
    array $args
) use ($app) {

    $cpl = new \Callingallpapers\Api\PersistenceLayer\CfpPersistenceLayer(
        $app->getContainer()['pdo'],
        $app->getContainer()['timezoneHelper']
    );

    $list = $cpl->search($request->getQueryParams());

    $cfpMapper = new \Callingallpapers\Api\Entity\CfpListMapper();

    return $this->view->render($response, $cfpMapper->map($list), 200, 'cfp/list.twig');;
});
