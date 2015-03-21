<?php

namespace Sky\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Student
 */
class Student
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Student
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $teachers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->teachers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add teachers
     *
     * @param \Sky\TestBundle\Entity\Teacher $teachers
     * @return Student
     */
    public function addTeacher(\Sky\TestBundle\Entity\Teacher $teachers)
    {
        $this->teachers[] = $teachers;
        $teachers->getStudents()->add($this);

        return $this;
    }

    /**
     * Remove teachers
     *
     * @param \Sky\TestBundle\Entity\Teacher $teachers
     */
    public function removeTeacher(\Sky\TestBundle\Entity\Teacher $teachers)
    {
        $this->teachers->removeElement($teachers);
        $teachers->getStudents()->removeElement($this);
    }

    /**
     * Get teachers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeachers()
    {
        return $this->teachers;
    }
}
