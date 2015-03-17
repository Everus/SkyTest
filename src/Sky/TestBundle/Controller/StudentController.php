<?php
namespace Sky\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StudentController extends Controller
{
    public function indexAction($id) {
        $student = $this->getDoctrine()
            ->getRepository('SkyTestBundle:Student')
            ->find($id);
        return $this->render('SkyTestBundle:Student:index.html.twig', array('Student' => $student));
    }
} 