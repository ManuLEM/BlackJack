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

    public function getStats()
    {
        $stats =  $this->getEntityManager()
                        ->createQuery(
                            'SELECT p.id, p.name, g as game FROM CasinoBlackjackBundle:Game g
                            JOIN g.player p'
                        )
                        ->getResult();

        $ranking = array();
        foreach ($stats as $stat) {
            if (!isset($ranking[$stat['id']])) {
                $ranking[$stat['id']]['id'] = $stat['id'];
                $ranking[$stat['id']]['name'] = $stat['name'];
                $ranking[$stat['id']]['gamesNbr'] = 0;
                $ranking[$stat['id']]['score'] = 0;
                $ranking[$stat['id']]['maxScore'] = 0;
                $ranking[$stat['id']]['roundsNbr'] = $stat['game']->getRounds()->count();
            }
            $ranking[$stat['id']]['gamesNbr']++;
            $score = $stat['game']->getScore();
            $ranking[$stat['id']]['score'] += $score;
            $ranking[$stat['id']]['maxScore'] = max($ranking[$stat['id']]['maxScore'], $score);
        }
        
        return $ranking;
    }

    public function getTotalScore($playerId)
    {
        $scores =  $this->getEntityManager()
                        ->createQuery(
                            'SELECT g.score FROM CasinoBlackjackBundle:Game g
                            JOIN g.player p
                            WHERE p.id = :playerId'
                        )
                        ->setParameter('playerId', $playerId)
                        ->getResult();

        $finalScore = 0;
        foreach ($scores as $score) {
            $finalScore += $score['score'];
        }
        
        return $finalScore;
    }

    private static function sort($a, $b)
    {
        return  $b['score'] - $a['score'];
    }
}
