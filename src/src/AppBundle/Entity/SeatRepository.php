<?php

namespace AppBundle\Entity;

/**
 * SeatRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SeatRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Find by movie name, cinema, date of projection and movie genre.
     */
    public function findFreeSeats($projection_id)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery('SELECT s FROM AppBundle\Entity\Seat s
            WHERE s.hall IN (SELECT IDENTITY(p.hall) FROM AppBundle\Entity\Projection p WHERE p.id = ?1)
            AND s.id NOT IN (SELECT IDENTITY(t.seat) FROM AppBundle\Entity\Ticket t JOIN AppBundle\Entity\Projection p2 WHERE p2.id = ?1)');
        $query->setParameter(1, $projection_id);
        return $query->getResult();
    }
}
