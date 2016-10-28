<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Simplex\Container;
use TheCodingMachine\DoctrineAnnotationsServiceProvider;
use TheCodingMachine\DoctrineCacheBridgeServiceProvider;
use TheCodingMachine\MiddlewareListServiceProvider;
use TheCodingMachine\StashServiceProvider;
use TheCodingMachine\StratigilityServiceProvider;
use Mouf\Mvc\Splash\DI\SplashServiceProvider;
use TheCodingMachine\WhoopsMiddlewareServiceProvider;
use RhMachine\ServiceProvider\RhMachineServiceProvider;
use RhMachine\ServiceProvider\TingServiceProvider;
use RhMachine\ServiceProvider\UserServiceServiceProvider;

$container = new Container();

// Let's register all the services we need
$container->register(new DoctrineAnnotationsServiceProvider());
$container->register(new DoctrineCacheBridgeServiceProvider());
$container->register(new MiddlewareListServiceProvider());
$container->register(new StashServiceProvider());
$container->register(new StratigilityServiceProvider());
$container->register(new WhoopsMiddlewareServiceProvider());

// The Splash service provider will automatically register Splash in the Stratigility middleware pipe.
$container->register(new SplashServiceProvider());

$container->register(new TingServiceProvider());
$container->register(new UserServiceServiceProvider());
$container->register(new RhMachineServiceProvider());


// The 'thecodingmachine.splash.controllers' entry must contain an array of controller instances.
$container->set('thecodingmachine.splash.controllers', [
    'rootController'
]);

// We assume the 'BASE' environment variable contains the base URL of the application (see .htaccess below)
$container->set('root_url', getenv('BASE'));

// Let's get the PSR-7 server, and let's bootstrap it.
$diactorosServer = $container->get(\Zend\Diactoros\Server::class);
$diactorosServer->listen();