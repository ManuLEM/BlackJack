<?php

namespace Casino\BlackjackBundle\Controller;

use Casino\BlackjackBundle\Controller\DefaultController;
use Casino\BlackjackBundle\Entity\Deck;
use Casino\BlackjackBundle\Entity\Game;
use Casino\BlackjackBundle\Entity\Round;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class GameController extends DefaultController
{
    public function startAction()
    {
        $session = new Session();
        if (!$session->get('playerId')) {
            return $this->redirect($this->generateUrl('get_blackjack_login'));
        }

        $player = $this->getCurrentPlayer( $session->get('playerId') );
        $game = new Game();
        $game->setPlayer($player);
        $round = new Round();
        $round->setGame($game);

        $em = $this->getDoctrine()->getManager();
        $em->persist($game);
        $em->persist($round);
        $em->flush();

        return $this->redirect($this->generateUrl('blackjack_game'));
    }

    public function playAction()
    {
        $session = new Session();
        if (!$session->get('playerId')) {
            return $this->redirect($this->generateUrl('get_blackjack_login'));
        }

        $player = $this->getCurrentPlayer( $session->get('playerId') );
        $game = $player->getGames()->last();
        $round = $game->getRounds()->last();

        // if ($round->getBet() === 0) {
            return $this->render('CasinoBlackjackBundle:Game:roundStart.html.twig', array(
                'player' => $player,
                'game' => $game,
                'round' => $round
            ));
        // }

        $error = "";
        foreach ($session->getFlashBag()->get('error', array()) as $message) {
            $error = $message;
        }

        // return $this->render('CasinoBlackjackBundle:Game:playing.html.twig', array(
        //     'player' => $player,
        //     'game' => $game,
        //     'round' => $round
        //     'error' => $error
        // ));
    }

    public function betAction(Request $request)
    {
        $session = new Session();
        if (!$session->get('playerId')) {
            return $this->redirect($this->generateUrl('get_blackjack_login'));
        }

        $bet = $request->get('bet');

        $em = $this->getDoctrine()->getManager();
        $player = $this->getCurrentPlayer( $session->get('playerId') );
        $originalWallet = $player->getWallet();
        $player->setWallet( $originalWallet - $bet );
        $round = $em->getRepository('CasinoBlackjackBundle:Round')->find( $request->get('roundId') );

        $round->setBet($bet);

        $query = $em->createQueryBuilder('r')
            ->select('r.name')
            ->from('CasinoBlackjackBundle:Revealed', 'r')
            ->getQuery();

        $revealedNames = $query->getResult();

        if ($round->getRevealed()->count() > 50) {
            $session->getFlashBag()->add('error', 'Your deck is empty, this shouldn\'t happend.');
            return $this->redirect($this->generateUrl('blackjack_game'));
        }

        $deck = new Deck();
        $revealed = array();
        
        for ($i=0; $i < 2; $i++) {
            $revealed[] = $deck->draw($round, $revealedNames, 'player');
            $em->persist($revealed);
        }
        for ($i=0; $i < 2; $i++) {
            $revealed[] = $deck->draw($round, $revealedNames, 'dealer');
            $em->persist($revealed);
        }

        $em->flush();

        var_dump($revealed->getName());
        die;
    }
}
