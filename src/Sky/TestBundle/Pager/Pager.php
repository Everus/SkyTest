<?php
namespace Sky\TestBundle\Pager;
use Doctrine\ORM\Tools\Pagination\Paginator;


class Pager {

    protected $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function paginate($query, &$page = 1, &$pageSize = 10) {
        $pageSize = (int)$pageSize;
        $page = (int)$page;

        if( $pageSize < 1 ){
            $pageSize = 10;
        }

        if( $page < 1 ){
            $page = 1;
        }

        $query = $this->em
            ->createQuery($query)
            ->setFirstResult($pageSize * ($page - 1))
            ->setMaxResults($pageSize);

        return new Paginator($query);
    }

} 