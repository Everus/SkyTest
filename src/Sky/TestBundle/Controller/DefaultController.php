<?php

namespace Sky\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($page = 1, $pageSize = 10)
    {
        $pageSize = (int)$pageSize;
        $page = (int)$page;

        if( $pageSize < 1 ){
            $pageSize = 10;
        }

        if( $page < 1 ){
            $page = 1;
        }

        $teachers = $this->getDoctrine()
            ->getRepository('SkyTestBundle:Teacher')
            ->findAllPage($page, $pageSize);

        if(count($teachers) == 0) {
            return $this->render('SkyTestBundle::Teacher\empty.html.twig');
        }

        return $this->render('SkyTestBundle::index.html.twig', array('Teachers' => $teachers));
    }
}
