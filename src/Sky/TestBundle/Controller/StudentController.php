<?php
namespace Sky\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sky\TestBundle\Entity\Student;
use Sky\TestBundle\Form\StudentType;

class StudentController extends Controller
{
    public function indexAction($id) {
        $student = $this->getDoctrine()
            ->getRepository('SkyTestBundle:Student')
            ->find($id);
        if(!$student) {
            throw $this->createNotFoundException('Запрошенный ученик не найден.');
        }
        return $this->render('SkyTestBundle:Student:index.html.twig', array('student' => $student));
    }

    public function newAction(Request $request) {
        $student = new Student();

        $form = $this->createForm(new StudentType(), $student);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($student);
            $em->flush();

            return $this->redirectToRoute('sky_test_student_index', array(
                'id' => $student->getId()
            ));
        }
        return $this->render('SkyTestBundle:Student:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function linkCreateAction($id, $page = 1, $search, $teacherId) {
        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository('SkyTestBundle:Student')
            ->find($id);
        if(!$student) {
            throw $this->createNotFoundException('Запрошенный ученик не найден.');
        }
        $teacher = $em->getRepository('SkyTestBundle:Teacher')
            ->find($teacherId);
        if($teacher) {
            $student->addTeacher($teacher);

            $em->persist($student);
            $em->persist($teacher);
            $em->flush();

            $this->addFlash('message','Учитель '.$teacher->getName().' успешно назначен ученику '.$student->getName().'.');
        } else {
            $this->addFlash('error', 'Произошла ошибка при назначении учителя ученику '.$student->getName().', попробуйте снова позже.');
        }


        return $this->redirectToRoute('sky_test_student_link', array(
            'id' => $student->getId(),
            'page' => $page,
            'search' => $search,
        ));
    }

    public function linkAction($id, $page, $search) {
        $em = $this->getDoctrine()->getManager();
        $student = $em
            ->getRepository('SkyTestBundle:Student')
            ->find($id);

        if(!$student) {
            throw $this->createNotFoundException('Запрошенный ученик не найден.');
        }

        $query = '
            SELECT t
            FROM SkyTestBundle:Teacher t
            WHERE
            NOT EXISTS (
                SELECT s
                FROM SkyTestBundle:Student s
                WHERE s.id = :studentId
                AND s MEMBER OF t.students
            )';

        $query = $em->createQuery($query)->setParameter('studentId', $student->getId());
        $teachers = $this->get('pager')->paginate($query, $page);
        $count = count($teachers);

        return $this->render('SkyTestBundle:Student:link.html.twig', array(
            'count' => count($teachers),
            'teachers' => $teachers,
            'student' => $student,
            'page' => $page,
            'search' => $search,
            'pageSize' => 10
        ));
    }

    public function unlinkAction($id, $teacherId) {
        $em = $this->getDoctrine()->getManager();

        $student = $em
            ->getRepository('SkyTestBundle:Student')
            ->find($id);
        $teacher = $em
            ->getRepository('SkyTestBundle:Teacher')
            ->find($teacherId);

        if(!$student) {
            throw $this->createNotFoundException('Запрошенный ученик не найден.');
        }

        if($teacher) {
            $student->removeTeacher($teacher);
            $em->persist($student);
            $em->persist($teacher);
            $em->flush();
        }

        return $this->redirectToRoute('sky_test_student_index', array(
            'id' => $student->getId()
        ));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $student = $em
            ->getRepository('SkyTestBundle:Student')
            ->find($id);
        $em->remove($student);
        $em->flush();

        return $this->redirectToRoute('sky_test_student_list');
    }

    public function editAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $student = $em
            ->getRepository('SkyTestBundle:Student')
            ->find($id);

        if(!$student) {
            throw $this->createNotFoundException('Запрошенный ученик не найден.');
        }

        $form = $this->createForm(new StudentType(), $student);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($student);
            $em->flush();

            return $this->redirectToRoute('sky_test_student_index', array(
                'id' => $student->getId()
            ));
        }
        return $this->render('SkyTestBundle:Student:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function listAction($page = 1, $pageSize = 10, $sortField = 'name', $sortDirection = 'DESC') {
        $query = '
                SELECT s, COUNT(t) AS HIDDEN cnt
                FROM SkyTestBundle:Student s
                LEFT OUTER JOIN s.teachers t
                GROUP BY s.id';

        switch ($sortField) {
            case 'count':
                $query.= ' ORDER BY cnt';
                break;
            case 'name':
            default:
                $query.= ' ORDER BY s.name';
        }

        switch ($sortDirection) {
            case 'DESC':
                $query.= ' DESC';
                break;
            case 'ASC':
            default:
                $query.= ' ASC';
        }

        $query = $this
            ->getDoctrine()
            ->getManager()
            ->createQuery($query);


        $students = $this->get('pager')->paginate($query, $page, $pageSize);

        $count = count($students);

        $data = array(
            'students' => $students,
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        );
        return $this->render('SkyTestBundle:Student:list.html.twig', $data);
    }
} 