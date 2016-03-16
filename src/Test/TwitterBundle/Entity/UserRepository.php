<?php
namespace TestTwitterBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u.username FROM TestTwitterBundle:User u'
            )
            ->getResult();
    }
}