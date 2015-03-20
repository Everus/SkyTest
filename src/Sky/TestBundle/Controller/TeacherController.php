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
        $em = $this->getDoctrine()->getEntityManager();
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

        return $this->render('SkyTestBundle:Teacher:link.html.twig', array(
            'teachers_count' => count($students),
            'teacher' => $teacher,
            'students' => $students,
            'page' => $page,
            'search' => $search
        ));
    }

    public function linkCrateAction($id, $page, $search, $studentId) {
        $em = $this->getDoctrine()->getEntityManager();
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
} 