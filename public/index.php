<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Middleware\MethodOverrideMiddleware;
use Sasha\Slim\Repo;
use Sasha\Slim\Validator;

use function DI\value;

session_start();

$container = new Container();
$container->set('renderer', function() {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/templates');
});
$container->set('flash', function() {
    return new \Slim\Flash\Messages();
});

$app = AppFactory::createFromContainer($container);
$app->add(MethodOverrideMiddleware::class);
$app->addErrorMiddleware(true, true, true);
$router = $app->getRouteCollector()->getRouteParser();
$validator = new Validator();

$app->get('/users/show/{id}', function($request, $response, $args) {
    $id = $args['id'];
    $current = $_SESSION['users'][$id];
    if (empty($current)) {
        return $response->withStatus(404)->write('404 Not found');
    }
    $params = ['user' => $current];
    return $this->get('renderer')->render($response, '/users/show.phtml', $params);
})->setName('users.show');

$app->get('/', function ($request, $response) {
    $search = $request->getQueryParam('name') ?? '';
    $users = $_SESSION['users'] ?? [];
    $filtered = array_filter(
        $users,
        fn($user) => str_contains($user['name'], $search) //[1 => ['name' => name, 'id' => 'id']]
    );
    $messages = $this->get('flash')->getMessages();
    $params = ['name' => $search, 'users' => $filtered, 'messages' => $messages];
    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
})->setName('users.index');

$app->get('/users/new', function ($request, $response) {
    $params = ['user' => ['name' => '', 'email' => '', 'password' => '', 'password_confirmation' => ''],
            'errors' => '',
            'messages' => ['errors' => []]
    ];
    return $this->get('renderer')->render($response, 'users/new.phtml', $params);
})->setName('users.create');

$app->post('/users', function ($request, $response) use ($router, $validator) {
    $user = $request->getParsedBodyParam('user');
    $errors = $validator->validate($user);
    if (empty($errors)) {
        $this->get('flash')->addMessage('success', 'User successfully created');
        $user['id'] = uniqid();
        $_SESSION['users'][$user['id']] = $user;
        return $response->withRedirect($router->urlFor('users.index'), 302);
    }
    $this->get('flash')->addMessage('errors', 'Что то пошло не так');
    $messages = $this->get('flash')->getMessages() ?? ['errors' => []];
    $params = ['user' => $user, 'errors' => $errors, 'messages' => $messages ];
    return $this->get('renderer')->render($response, 'users/new.phtml', $params);
})->setName('users.store');

$app->get('/users/{id}/edit', function ($request, $response, $args) {
    $user = $_SESSION['users'][$args['id']];
    $params = ['user' => $user, 'errors' => '', 'messages' => ['errors' => []]];
    return $this->get('renderer')->render($response, 'users/edit.phtml', $params);
})->setName('users.edit');

$app->patch('/users/{id}', function ($request, $response, $args) use ($router, $validator) {
    $userData = $request->getParsedBodyParam('user');
    $user = $_SESSION['users'][$args['id']];
    $errors = $validator->validate($user);
    
    if (empty($errors)) {
        $this->get('flash')->addMessage('success', 'User successfully created');
        $user['name'] = $userData['name'];
        $user['email'] = $userData['email'];
        $_SESSION['users'][$user['id']] = $user;
        return $response->withRedirect($router->urlFor('users.index'), 302);
    }
    $params = ['user' => ['name' => $user['name'], 'email' => $user['email'], 'password' => '', 'password_confirmation' => ''],
            'errors' => $errors,
            'messages' => []
    ];
    return $this->get('renderer')->render($response, 'users/edit.phtml', $params);
})->setName('users.update');

$app->delete('/users/{id}', function ($request, $response, $args) use ($router) {
    $id = $args['id'];
    $this->get('flash')->addMessage('success', 'User successfully deleted');
    unset($_SESSION['users'][$id]);
    return $response->withRedirect($router->urlFor('users.index'), 302);
});

$app->run();