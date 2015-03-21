<?php
namespace Sky\TestBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class TeacherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', 'text', array('label' => 'Имя учителя:'))
            ->add('save', 'submit', array('label' => 'Сохранить'));
    }

    public function getName() {
        return 'teacher';
    }
} 