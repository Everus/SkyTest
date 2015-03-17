<?php

namespace Sky\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SkyTestBundle:Default:index.html.twig', array('name' => $name));
    }
}
