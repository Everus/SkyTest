<?php
namespace Sky\TestBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', 'text', array('label' => 'Имя ученика:'))
            ->add('save', 'submit', array('label' => 'Сохранить'));
    }

    public function getName() {
        return 'student';
    }
} 