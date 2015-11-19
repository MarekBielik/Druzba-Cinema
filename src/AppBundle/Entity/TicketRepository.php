<?php

namespace AppBundle\Entity;

/**
 * TicketRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TicketRepository extends \Doctrine\ORM\EntityRepository
{
    /**
       * Find by movie name, cinema, date of projection and movie genre.
       */
      public function findReservation($id)
      {
          $qb = $this->getEntityManager()->createQueryBuilder();

          $qb->select('t')
            ->from('AppBundle:Ticket', 't');

          $qb->where($qb->expr()->isNull('t.payment_date'));
          $qb->andWhere('t.id = ?1')
            ->setParameter(1, $id);

          $query = $qb->getQuery();

          try {
              return $query->getSingleResult();
          } catch (\Doctrine\ORM\NoResultException $e) {
              return;
          }
      }

    public function findSellTickets($movie, $cinemas)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();


        $qb->select('t')
            ->from('AppBundle:Ticket', 't')
            ->andWhere($qb->expr()->isNotNull('t.payment_date'))
            ->join('t.projection', 'p');

        if ($movie) {
            $qb->join('p.movie', 'm')
                ->andWhere('m.id = ?1')
                ->setParameter(1, $movie->getId());
        }

        if (!$cinemas->isEmpty()) {
            $qb->join('p.hall', 'h')
                ->join('h.cinema', 'c');


            $cinema_name = array();
            foreach ($cinemas as $cinema) {
                $cinema_name[] = $cinema->getName();
            }

            $qb->andWhere($qb->expr()->in('c.name', $cinema_name));
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function removeExpiredTickets($user) {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $expiredTickets = $qb
            ->select('t')
            ->from('AppBundle:Ticket', 't')
            ->where('t.user = ?1')
            ->andWhere('t.payment_date IS NULL')
            ->join('t.projection', 'p')
            ->andWhere('p.date < CURRENT_DATE()')
            ->orWhere('p.date = CURRENT_DATE() AND p.end < CURRENT_TIME()' )
            ->setParameter(1, $user)
            ->getQuery()
            ->getResult();

        foreach($expiredTickets as $expiredTicket) {
                $em->remove($expiredTicket);
        }

        $em->flush();

    }
}
