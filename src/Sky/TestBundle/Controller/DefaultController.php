<?php

namespace Sky\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($page = 1, $pageSize = 10)
    {
        $query = '
                SELECT t
                FROM SkyTestBundle:Teacher t';


        $teachers = $this->get('pager')->paginate($query, $page, $pageSize);

        $count = count($teachers);

        if(count($teachers) == 0) {
            return $this->render('SkyTestBundle::Teacher\empty.html.twig');
        }
        $data = array(
            'teachers' => $teachers,
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
        );
        return $this->render('SkyTestBundle::index.html.twig', $data);
    }
}
