<?php

namespace ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ArticleBundle:Default:index.html.twig', array('name' => $name));
    }

    public function api1Action()
    {
      print_r($this->container->getParameter('article_permission'));
      return new Response("\n123456789");
    }
}
