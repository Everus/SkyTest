<?php
namespace Sky\TestBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class TeacherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name')
            ->add('save', 'submit');
    }

    public function getName() {
        return 'teacher';
    }
} 