<?php
namespace UserBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use UserBundle\Controller\PageController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class listenerController{

  private $router;
  private $container;

  public function __construct($router ,$container){
    $this->router = $router;
    $this->container = $container;
  }

  public function onKernelController(FilterControllerEvent $event){
    $controller = $event->getController();
    // $controller = array();
    // $controller[0] = new PageController();
    // $controller[1] = 'preferenceAction';

    // print_r($event->getRequest()->get('_route'));
    // print "aaaaaaa";
    // $Session = new Session();
    // if(!$Session->has($this->container->getParameter('session_login'))){
    //   if($controller[0] instanceof AdminapiController){
    //       $controller[1] = 'notpassedeAction';//if not login print error msg
    //   }
    //   if($controller[0] instanceof ManageController){
    //       $controller[1] = 'indexAction';//if not login pag
    //   }
    // }
    return $event->setController($controller);
  }

}
