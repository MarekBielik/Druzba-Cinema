<?php

namespace AppBundle\Entity;

/**
 * UserRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
   * Find by name and email.
   */
  public function findNameEmail($user = null, $email = null)
  {
      $qb = $this->getEntityManager()->createQueryBuilder();

      $qb->select('u')
        ->from('AppBundle:User', 'u');

      if ($user) {
          $qb->andWhere($qb->expr()->like('u.name', '?1'))
              ->setParameter(1, '%'.$user.'%');
      }

      if ($email) {
          $qb->andWhere($qb->expr()->like('u.email', '?2'))
              ->setParameter(2, '%'.$email.'%');
      }

      $qb->orderBy('u.name', 'ASC');
      $qb->addOrderBy('u.email', 'ASC');

      $query = $qb->getQuery();

      return $query->getResult();
  }

}
