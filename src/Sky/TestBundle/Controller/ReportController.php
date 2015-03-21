<?php

namespace Sky\TestBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query\ResultSetMapping;

class ReportController extends Controller
{
    public function maxPairAction($page = 1, $pageSize = 10) {
        $pageSize = (int)$pageSize;
        $page = (int)$page;

        if( $pageSize < 1 ){
            $pageSize = 10;
        }

        if( $page < 1 ){
            $page = 1;
        }

        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('cnt', 'cnt');
        $rsm->addScalarResult('first_teacher_name', 'first_teacher_name');
        $rsm->addScalarResult('second_teacher_name', 'second_teacher_name');
        $rsm->addScalarResult('first_teacher_id', 'first_teacher_id');
        $rsm->addScalarResult('second_teacher_id', 'second_teacher_id');

        $sql = '
                SELECT
                t1.id as first_teacher_id,
                t2.id as second_teacher_id,
                t1.name as first_teacher_name,
                t2.name as second_teacher_name,
                (
                    SELECT COUNT(s.student_id) FROM student_teacher s
                    WHERE s.teacher_id = t1.id AND s.student_id IN (
                        SELECT student_id
                        FROM student_teacher s2
                        WHERE s2.teacher_id = t2.id AND s.student_id=s2.student_id))
                as cnt
                FROM `Teacher` as t1
                INNER JOIN `Teacher` as t2 ON t1.id != t2.id AND t1.id < t2.id
                ORDER BY cnt DESC
                LIMIT ?, ?';

        $query = $this
            ->getDoctrine()
            ->getManager()
            ->createNativeQuery($sql, $rsm)
            ->setParameter(1, $pageSize * ($page - 1))
            ->setParameter(2, $pageSize);

        $teachers = $query->getResult();

        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('cnt', 'cnt');

        $sql = '
                SELECT
                  COUNT(t1.id) as cnt
                FROM `Teacher` as t1
                INNER JOIN `Teacher` as t2 ON t1.id != t2.id AND t1.id < t2.id
                ORDER BY cnt DESC';

        $query = $this
            ->getDoctrine()
            ->getManager()
            ->createNativeQuery($sql, $rsm);

        $count = $query->getSingleScalarResult();

        $data = array(
            'teachers' => $teachers,
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
        );
        return $this->render('SkyTestBundle:Report:max_pair.html.twig', $data);
    }
}