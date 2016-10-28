<?php
namespace RhMachine\Controller;

use Mouf\Security\UserService\UserServiceInterface;
use RhMachine\Repository\RequestRepository;
use Zend\Diactoros\Response\HtmlResponse;
use Mouf\Mvc\Splash\Annotations\URL;
use Zend\Diactoros\Response\JsonResponse;

class RootController {

    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var RequestRepository
     */
    private $requestRepository;


    public function __construct(\Twig_Environment $twigEnvironment, UserServiceInterface $userServiceInterface, RequestRepository $requestRepository) {
        $this->twigEnvironment = $twigEnvironment;
        $this->userService = $userServiceInterface;
        $this->requestRepository = $requestRepository;
    }

    /**
     *
     * @URL("/")
     */
    public function index() {
        $requests = [$this->requestRepository->get(1)];
        $template = $this->twigEnvironment->loadTemplate('homepage.twig');
        return new HtmlResponse($template->render([ 'requests' => $requests]));
    }

    /**
     *
     * @URL("/isLogged")
     */
    public function isLogged() {
        $user = $this->userService->getLoggedUser();
        return new JsonResponse($user);
    }
}