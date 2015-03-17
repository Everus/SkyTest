<?php
namespace Sky\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TeacherController extends Controller
{
    public function indexAction($id) {
        $teacher = $this->getDoctrine()
            ->getRepository('SkyTestBundle:Teacher')
            ->find($id);
        return $this->render('SkyTestBundle:Teacher:index.html.twig', array('Teacher' => $teacher));
    }
} 