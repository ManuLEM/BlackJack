<?php

namespace Casino\BlackjackBundle\Controller;

use Casino\BlackjackBundle\Controller\DefaultController;
use Casino\BlackjackBundle\Entity\Player;
use Casino\BlackjackBundle\Form\Type\PlayerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class PlayerController extends DefaultController
{
    public function indexAction()
    {
        $session = new Session();
        if (!$session->get('playerId')) {
            return $this->redirect($this->generateUrl('get_blackjack_login'));
        }

        $player = $this->getCurrentPlayer( $session->get('playerId') );


        $em = $this->getDoctrine()->getManager();
        $ranking = $em->getRepository('CasinoBlackjackBundle:Player')->getRanking();

        return $this->render('CasinoBlackjackBundle:Game:index.html.twig', array(
            'player' => $player,
            'ranking' => $ranking
        ));
    }

    public function loginAction()
    {
        $player = new Player();

        $form = $this->createForm(new PlayerType(), $player, array(
            'action' => $this->generateUrl('post_blackjack_login'),
            'method' => 'POST',
        ));

        $em = $this->getDoctrine()->getManager();
        $ranking = $em->getRepository('CasinoBlackjackBundle:Player')->getRanking();

        return $this->render('CasinoBlackjackBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
            'ranking' => $ranking
        ));
    }

    public function createPlayerAction(Request $request)
    {
        $session = new Session();
        if (!$session->get('playerId')) {
            $player = new Player;
            $player->setName($request->get('player')['name']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();

            $session->set('playerId', $player->getId());
        }

        return $this->redirect($this->generateUrl('get_blackjack_homepage'));
    }

    public function profileAction($playerId)
    {
        $player = $this->getCurrentPlayer( $playerId );

        $em = $this->getDoctrine()->getManager();
        $total = $em->getRepository('CasinoBlackjackBundle:Player')->getTotalScore( $playerId );
        $games = $player->getGames();

        $totalBet = 0;
        foreach ($games as &$game) {
            $rounds = $game->getRounds();
            $winnings = 0;
            foreach ($rounds as $round) {
                $totalBet += $round->getBet();

                if ($round->getWon() === 'won') {
                    $winnings++;
                }
            }
            $allRounds = $rounds->count();
            $game->averageBet = round($totalBet / $allRounds, 2);
            $game->winningPercent = round($winnings / $allRounds * 100, 2);
        }

        return $this->render('CasinoBlackjackBundle:Player:index.html.twig', array(
            'player' => $player,
            'games' => $games,
            'total' => $total
        ));
    }
}
