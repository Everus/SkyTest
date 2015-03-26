<?php

namespace Sky\TestBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query\ResultSetMapping;

class ReportController extends Controller
{
    public function maxPairAction($page = 1, $pageSize = 10) {
        $teacherRep = $this->getDoctrine()->getManager()->getRepository('SkyTestBundle:Teacher');

        $pageSize = (int)$pageSize;
        $page = (int)$page;

        if( $pageSize < 1 ){
            $pageSize = 10;
        }

        if( $page < 1 ){
            $page = 1;
        }

        $teachers = $teacherRep->findPairs($pageSize * ($page - 1), $pageSize);

        $count = $teacherRep->countPairs();

        $data = array(
            'teachers' => $teachers,
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
        );
        return $this->render('SkyTestBundle:Report:max_pair.html.twig', $data);
    }

    public function teacherPairAction($firstId, $secondId) {
        $teacherRep = $this->getDoctrine()->getManager()->getRepository('SkyTestBundle:Teacher');

        $teachers = array(
            'first' => $teacherRep->find($firstId),
            'second' => $teacherRep->find($secondId),
        );

        if (!$teachers['first'] || !$teachers['second']) {
            throw $this->createNotFoundException('Запрошенный учитель не найден.');
        }

        $students = array();

        $students[] = $teachers['first']->getStudents()->filter(function($item) use ($teachers) {
            return $teachers['second']->getStudents()->contains($item);
        });

        $students[] = $teachers['first']->getStudents()->filter(function($item) use ($teachers) {
            return !$teachers['second']->getStudents()->contains($item);
        });

        $students[] = $teachers['second']->getStudents()->filter(function($item) use ($teachers) {
            return !$teachers['first']->getStudents()->contains($item);
        });

        return $this->render('SkyTestBundle:Report:teacher_pair.html.twig', array(
            'teachers' => $teachers,
            'students' => $students,
        ));
    }
}