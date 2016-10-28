<?php
namespace RhMachine\ServiceProvider;

use Interop\Container\ContainerInterface;
use Interop\Container\ServiceProvider;
use Mouf\Security\UserService\UserDaoInterface;
use Mouf\Security\UserService\UserService;
use Mouf\Security\UserService\UserServiceException;
use Mouf\Utils\Session\SessionManager\SessionManagerInterface;
use Psr\Log\LoggerInterface;

class UserServiceServiceProvider implements ServiceProvider
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
            'userService' =>  function (ContainerInterface $container) {
                $userService = new UserService();

                if ($container->has(LoggerInterface::class)) {
                    $userService->log = $container->get(LoggerInterface::class);
                }
                    
                if ($container->has("SECRET")) {
                    $userService->sessionPrefix = $container->get("SECRET");
                }
                
                $userService->sessionManager = $container->get(SessionManagerInterface::class);
                $userService->userDao = $container->get(UserDaoInterface::class);
                return $userService;
            }
        ];
    }


}