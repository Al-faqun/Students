<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Shinoa\StudentsList\Controllers\RegEditController;
use Shinoa\StudentsList\FileSystem;
use Shinoa\StudentsList\Controllers\ListController;
use Shinoa\StudentsList\ErrorHelper;
use Shinoa\StudentsList\Loader;

//необходимые части приложения, ошибки в которых нельзя словить, а необходимо протестировать до.
require_once '../bootstrap.php';
$root = dirname(__DIR__);
$public = __DIR__;
$errorHelper = new ErrorHelper(FileSystem::append([$root, 'templates']));
//инициализируем базовые конфиги и обработку ошибок
Loader::setRoot($root);
$errorHelper->setLogFilePath(FileSystem::append([$public, 'errors.log']));
$errorHelper->registerFallbacks(Loader::getStatus());
try {
    //начиная с этого места, код может обрабатываться errorHelper'ом
    $config = [
        'settings' => [
            'displayErrorDetails' => true
        ]
    ];
    $app = new \Slim\App($config);
    $c = $app->getContainer();
    
    //отключаем встроенный обработчик ошибок Slim, т.к. все ошибки обрабатывает мой класс.
    unset($c['errorHandler']);
    unset($c['phpErrorHandler']);
    //заполняем контейнер
    $c['root'] = $root;
    $c['TwigFactory'] = $c->protect(function ($c, $templatesDir) {
        $twig = new \Slim\Views\Twig(
            $templatesDir,
            array(
                'cache' => FileSystem::append([$templatesDir, 'cache']),
                'auto_reload' => true,
                'autoescape' => 'html',
                'strict_variables' => true
            )
        );
        $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
        $twig->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));
        return $twig;
    });
    $c['ListController'] = function ($c) {
        $controller = new ListController(
            $c['root'],
            $c['TwigFactory']($c, FileSystem::append([$c['root'], 'templates', 'List']))
        );
        return $controller;
    };
    $c['RegEditController'] = function ($c) {
        $controller = new RegEditController(
            $c['root'],
            $c['TwigFactory']($c, FileSystem::append([$c['root'], 'templates', 'RegEdit']))
        );
        return $controller;
    };
    
    //маршруты сайта
    $app->get('/', function (Request $request, Response $response, $args) {
        $controller = $this->ListController;
        $response = $controller->redirect('/list', $response);
        return $response;
    });
    
    $app->get('/list', function (Request $request, Response $response, $args) {
        $controller = $this->ListController;
        $response = $controller->cookieStatusAction($request, $response, $args);
        $response = $controller->getExceptionAction($request, $response, $args);
        $response = $controller->getErrorAction($request, $response, $args);
        $response = $controller->list($request, $response, $args);
        return $response;
    });
    
    $app->post('/list', function (Request $request, Response $response, $args) {
        $controller = $this->ListController;
        $response = $controller->postStatusAction($request, $response, $args);
        return $response;
    });
    
    $app->get('/reg-edit', function (Request $request, Response $response, $args) {
        $controller = $this->RegEditController;
        $response = $controller->getUploadedAction($request, $response, $args);
        $response = $controller->getUpdatedAction($request, $response, $args);
        $response = $controller->cookieStatusAction($request, $response, $args);
        $response = $controller->getRegEdit($request, $response, $args);
        return $response;
    });
    
    $app->post('/reg-edit', function (Request $request, Response $response, $args) {
        $controller = $this->RegEditController;
        $response = $controller->cookieStatusAction($request, $response, $args);
        $response = $controller->postRegEdit($request, $response, $args);
        return $response;
    });
    
    $app->run();
} catch (\Throwable $e) {
    //словили ошибку - исправляем
    $errorHelper->dispatch($e, Loader::getStatus());
}
