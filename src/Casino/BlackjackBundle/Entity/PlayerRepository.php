<?php

namespace Casino\BlackjackBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PlayerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends EntityRepository
{
    public function getPlayerScore($playerId)
    {
        $scores =  $this->getEntityManager()
                        ->createQuery(
                            'SELECT g.score FROM CasinoBlackjackBundle:Game g
                            JOIN g.player p
                            WHERE p.id = :id'
                        )
                        ->setParameter('id', $playerId)
                        ->getResult();
        $finalScore = 0;
        foreach ($scores as $score) {
            $finalScore += $score['score'];
        }

        return $finalScore;
    }

    public function getRanking()
    {
        $scores =  $this->getEntityManager()
                        ->createQuery(
                            'SELECT p.id, p.name, g.score FROM CasinoBlackjackBundle:Game g
                            JOIN g.player p'
                        )
                        ->getResult();
        $ranking = array();
        foreach ($scores as $score) {
            if (!isset($ranking[$score['id']])) {
                $ranking[$score['id']]['id'] = $score['id'];
                $ranking[$score['id']]['name'] = $score['name'];
                $ranking[$score['id']]['score'] = 0;
            }
            $ranking[$score['id']]['score'] += $score['score'];
        }

        usort($ranking, 'self::sort');
        $ranking = array_slice($ranking, 0, 10);
        
        return $ranking;
    }

    private static function sort($a, $b)
    {
        return  $b['score'] - $a['score'];
    }
}
