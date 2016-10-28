<?php
namespace RhMachine\ServiceProvider;

use CCMBenchmark\Ting\ConnectionPool;
use CCMBenchmark\Ting\MetadataRepository;
use CCMBenchmark\Ting\Query\QueryFactory;
use CCMBenchmark\Ting\Repository\CollectionFactory;
use CCMBenchmark\Ting\Repository\Hydrator;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\RepositoryFactory;
use CCMBenchmark\Ting\Serializer\SerializerFactory;
use CCMBenchmark\Ting\Cache\Memcached;
use CCMBenchmark\Ting\UnitOfWork;
use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider;

class TingServiceProvider implements ServiceProvider
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
            'ConnectionPool' => function (ContainerInterface $container) {
                return new ConnectionPool();
            },
            'QueryFactory' => function (ContainerInterface $container) {
                return new QueryFactory();
            },
            'SerializerFactory' => function (ContainerInterface $container) {
                return new SerializerFactory();
            },
            'Hydrator' => function (ContainerInterface $container) {
                return new Hydrator();
            },
            'HydratorSingleObject' => function (ContainerInterface $container) {
                return new HydratorSingleObject();
            },
            'MetadataRepository' => function (ContainerInterface $container) {
                return new MetadataRepository($container->get('SerializerFactory'));
            },
            'UnitOfWork' => function (ContainerInterface $container) {
                return new UnitOfWork(
                    $container->get('ConnectionPool'),
                    $container->get('MetadataRepository'),
                    $container->get('QueryFactory')
                );
            },
            'CollectionFactory' => function (ContainerInterface $container) {
                return new CollectionFactory(
                    $container->get('MetadataRepository'),
                    $container->get('UnitOfWork'),
                    $container->get('Hydrator')
                );
            },
            'RepositoryFactory' => function (ContainerInterface $container) {
                return new RepositoryFactory(
                    $container->get('ConnectionPool'),
                    $container->get('MetadataRepository'),
                    $container->get('QueryFactory'),
                    $container->get('CollectionFactory'),
                    $container->get('UnitOfWork'),
                    $container->get('Cache'),
                    $container->get('SerializerFactory')
                );
            },
            'Cache' => function (ContainerInterface $container) {
                $cache = new Memcached();
                $cache->setConnection(new Memcached($cache->getPersistentId()));
                return $cache;
            }
        ];
    }
}