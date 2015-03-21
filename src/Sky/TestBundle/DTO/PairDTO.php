<?php
namespace Sky\TestBundle\DTO;

class PairDTO
{
    protected $first_teacher;
    protected $second_teacher;
    protected $count;

    public function __construct($first_teacher, $second_teacher, $count)
    {
        $this->first_teacher = $first_teacher;
        $this->second_teacher = $second_teacher;
        $this->count = $count;
    }

    public function getFirstTeacher() {
        return $this->first_teacher;
    }

    public function getSecondTeacher() {
        return $this->$second_teacher;
    }

    public function getCount() {
        return $this->count;
    }
}