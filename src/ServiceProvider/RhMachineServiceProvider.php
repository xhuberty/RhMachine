<?php
namespace RhMachine\ServiceProvider;

use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider;
use Mouf\Security\UserService\UserDaoInterface;
use Mouf\Utils\Session\SessionManager\DefaultSessionManager;
use Mouf\Utils\Session\SessionManager\SessionManagerInterface;
use RhMachine\Repository\RequestRepository;
use RhMachine\Repository\UserRepository;

class RhMachineServiceProvider implements ServiceProvider
{

    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     *
     * Factories have the following signature:
     *        function(ContainerInterface $container, callable $getPrevious = null)
     *
     * About factories parameters:
     *
     * - the container (instance of `Interop\Container\ContainerInterface`)
     * - a callable that returns the previous entry if overriding a previous entry, or `null` if not
     *
     * @return callable[]
     */
    public function getServices()
    {
        return [
            "twigLoader" =>function(ContainerInterface $container) {
                return new \Twig_Loader_Filesystem([__DIR__ . '/../../views']);
            },
            "twigEnvironnement" =>function(ContainerInterface $container) {
                return new \Twig_Environment($container->get('twigLoader'));
            },
            UserDaoInterface::class => function (ContainerInterface $container) {
                return $container->get('RepositoryFactory')->get(UserRepository::class);
            },
            RequestRepository::class => function (ContainerInterface $container) {
                return $container->get('RepositoryFactory')->get(RequestRepository::class);
            },
            SessionManagerInterface::class=> function (ContainerInterface $container) {
                return new DefaultSessionManager();
            },
            "rootController"=>function(ContainerInterface $container) {
                return new \RhMachine\Controller\RootController(
                    $container->get('twigEnvironnement'),
                    $container->get('userService'),
                    $container->get(RequestRepository::class)
                );
            },
            'ConnectionPool' => function (ContainerInterface $container, callable $getPrevious = null) {
                $connectionPool = $getPrevious();
                $connectionPool->setConfig([
                    'main' => [
                        'namespace' => '\CCMBenchmark\Ting\Driver\Mysqli',
                        'master' => [
                            'host'      => DB_HOST,
                            'user'      => DB_USER,
                            'password'  => DB_PASSWORD,
                            'port'      => DB_PORT,
                        ]
                    ]
                ]);
                return $connectionPool;
            },
            'MetadataRepository' => function (ContainerInterface $container, callable $getPrevious = null) {
                $metadateRepository = $getPrevious();
                $metadateRepository->batchLoadMetadata('RhMachine\Repository', __DIR__ . '../Repository/*.php');
                return $metadateRepository;
            },
        ];
    }
}