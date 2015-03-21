<?php
namespace Sky\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sky\TestBundle\Entity\Teacher;
use Sky\TestBundle\Form\TeacherType;

class TeacherController extends Controller
{
    public function indexAction($id) {
        $teacher = $this->getDoctrine()
            ->getRepository('SkyTestBundle:Teacher')
            ->find($id);

        if(!$teacher) {
            throw $this->createNotFoundException('Запрошенный учитель не найден.');
        }

        return $this->render('SkyTestBundle:Teacher:index.html.twig',
            array(
                'teacher' => $teacher
            ));
    }

    public function newAction(Request $request) {
        $teacher = new Teacher();

        $form = $this->createForm(new TeacherType(), $teacher);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($teacher);
            $em->flush();

            return $this->redirectToRoute('sky_test_teacher_index',array(
                'id' => $teacher->getId()
            ));
        }
        return $this->render('SkyTestBundle:Teacher:new.html.twig', array(
            'form' => $form->createView()
            ));
    }

    public function linkAction($id, $page, $search) {
        $em = $this->getDoctrine()->getManager();
        $teacher = $em
            ->getRepository('SkyTestBundle:Teacher')
            ->find($id);

        if(!$teacher) {
            throw $this->createNotFoundException('Запрошенный учитель не найден.');
        }

        $query = '
            SELECT s
            FROM SkyTestBundle:Student s
            WHERE
            NOT EXISTS (
                SELECT t
                FROM SkyTestBundle:Teacher t
                WHERE t.id = :teacherId
                AND t MEMBER OF s.teachers)';

        $query  = $em->createQuery($query)->setParameter('teacherId', $teacher->getId());

        $students = $this->get('pager')->paginate($query, $page);
        $count = count($students);

        return $this->render('SkyTestBundle:Teacher:link.html.twig', array(
            'count' => $count,
            'teacher' => $teacher,
            'students' => $students,
            'page' => $page,
            'search' => $search,
            'pageSize' => 10
        ));
    }

    public function linkCreateAction($id, $page, $search, $studentId) {
        $em = $this->getDoctrine()->getManager();
        $teacher = $em->getRepository('SkyTestBundle:Teacher')
            ->find($id);
        if(!$teacher) {
            throw $this->createNotFoundException('Запрошенный учитель не найден.');
        }

        $student = $em->getRepository('SkyTestBundle:Student')
            ->find($studentId);

        if($student) {
            $teacher->addStudent($student);

            $em->persist($student);
            $em->persist($teacher);
            $em->flush();

            $this->addFlash('message', 'Ученик '.$student->getName().' добавлен учителю '.$teacher->getName().'.');
        } else {
            $this->addFlash('error', 'Произошла ошибка при назначении ученика учителю '.$teacher->getName().', попробуйте снова позже.');
        }

        return $this->redirectToRoute('sky_test_teacher_link', array(
            'id' => $id,
            'page' => $page,
            'search' => $search
        ));
    }

    public function unlinkAction($id, $studentId) {
        $em = $this->getDoctrine()->getManager();

        $teacher = $em
            ->getRepository('SkyTestBundle:Teacher')
            ->find($id);
        $student = $em
            ->getRepository('SkyTestBundle:Student')
            ->find($studentId);

        if(!$teacher) {
            throw $this->createNotFoundException('Запрошенный учитель не найден.');
        }

        if($student) {
            $student->removeTeacher($teacher);
            $em->persist($teacher);
            $em->persist($student);
            $em->flush();
        }
        return $this->redirectToRoute('sky_test_teacher_index', array(
            'id' => $teacher->getId()
        ));
    }

    public function listAction($page = 1, $pageSize = 10, $sortField = 'name', $sortDirection = 'DESC')
    {
        $query = '
                SELECT t, COUNT(s.id) AS HIDDEN cnt
                FROM SkyTestBundle:Teacher t
                LEFT OUTER JOIN t.students s
                GROUP BY t.id';

        switch ($sortField) {
            case 'count':
                $query.= ' ORDER BY cnt';
                break;
            case 'name':
            default:
                $query.= ' ORDER BY t.name';
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


        $teachers = $this->get('pager')->paginate($query, $page, $pageSize);

        $count = count($teachers);

        $data = array(
            'teachers' => $teachers,
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        );
        return $this->render('SkyTestBundle::index.html.twig', $data);
    }

    public function editAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $teacher = $em
            ->getRepository('SkyTestBundle:Teacher')
            ->find($id);

        if(!$teacher) {
            throw $this->createNotFoundException('Запрошенный учитель не найден.');
        }

        $form = $this->createForm(new TeacherType(), $teacher);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($teacher);
            $em->flush();

            return $this->redirectToRoute('sky_test_teacher_index', array(
                'id' => $teacher->getId()
            ));
        }
        return $this->render('SkyTestBundle:Teacher:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $teacher = $em
            ->getRepository('SkyTestBundle:Teacher')
            ->find($id);
        $em->remove($teacher);
        $em->flush();

        return $this->redirectToRoute('sky_test_teacher_list');
    }
} 