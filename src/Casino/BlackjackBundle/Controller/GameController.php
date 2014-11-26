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

    public function startRoundAction()
    {
        $session = new Session();
        if (!$session->get('playerId')) {
            return $this->redirect($this->generateUrl('get_blackjack_login'));
        }

        $player = $this->getCurrentPlayer( $session->get('playerId') );
        $game = $player->getGames()->last();
        $round = new Round();
        $round->setGame($game);

        $em = $this->getDoctrine()->getManager();
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

        if ($round->getWon() !== "") {
            return $this->redirect($this->generateUrl('get_blackjack_homepage'));
        }

        $error = "";
        foreach ($session->getFlashBag()->get('error', array()) as $message) {
            $error = $message;
        }

        if ($round->getBet() === 0) {
            return $this->render('CasinoBlackjackBundle:Game:roundStart.html.twig', array(
                'player' => $player,
                'game' => $game,
                'round' => $round,
                'error' => $error
            ));
        }

        $em = $this->getDoctrine()->getManager();
        $roundId = $round->getId();
        $playerRevealed = $em->getRepository('CasinoBlackjackBundle:Revealed')->findPlayerCards($roundId);
        $dealerRevealed = $em->getRepository('CasinoBlackjackBundle:Revealed')->findDealerCards($roundId);

        return $this->render('CasinoBlackjackBundle:Game:playing.html.twig', array(
            'player' => $player,
            'game' => $game,
            'round' => $round,
            'error' => $error,
            'playerRevealed' => $playerRevealed,
            'dealerRevealed' => $dealerRevealed
        ));
    }

    public function betAction(Request $request)
    {
        $session = new Session();

        $bet = $request->get('bet');

        $em = $this->getDoctrine()->getManager();
        $player = $this->getCurrentPlayer( $session->get('playerId') );
        $originalWallet = $player->getWallet();
        if ($originalWallet < $bet) {
            $session->getFlashBag()->add('error', 'You can\'t bet more than you have !');
            return $this->redirect($this->generateUrl('blackjack_game'));
        }
        $player->setWallet( $originalWallet - $bet );
        $round = $em->getRepository('CasinoBlackjackBundle:Round')->find( $request->get('roundId') );

        $round->setBet($bet);

        $revealedNames = $em->getRepository('CasinoBlackjackBundle:Revealed')->findNamesByRoundId($round->getId());

        if ($round->getRevealed()->count() > 50) {
            $session->getFlashBag()->add('error', 'The deck is empty, this shouldn\'t happend.');
            return $this->redirect($this->generateUrl('blackjack_game'));
        }

        $deck = new Deck();
        $revealed = array();
        
        for ($i=0; $i < 2; $i++) {
            $revealed[$i] = $deck->draw($round, $revealedNames, 'player');
            $em->persist($revealed[$i]);
        }
        for ($i=2; $i < 4; $i++) {
            $revealed[$i] = $deck->draw($round, $revealedNames, 'dealer');
            $em->persist($revealed[$i]);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('blackjack_game'));
    }

    public function playerDrawAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $roundId = $request->get('roundId');
        $round = $em->getRepository('CasinoBlackjackBundle:Round')->find( $roundId );
        $revealedNames = $em->getRepository('CasinoBlackjackBundle:Revealed')->findNamesByRoundId($roundId);

        if ($round->getRevealed()->count() > 50) {
            $session->getFlashBag()->add('error', 'The deck is empty, this shouldn\'t happend.');
            return $this->redirect($this->generateUrl('blackjack_game'));
        }

        $deck = new Deck();
        
        $revealed = $deck->draw($round, $revealedNames, 'player');
        $em->persist($revealed);

        $playerRevealed = $em->getRepository('CasinoBlackjackBundle:Revealed')->findPlayerCards($roundId);

        $deck = $deck->cards();
        array_push($playerRevealed, $revealed);
        $playerScore = $this->getScore($deck, $playerRevealed);
        
        if (!$playerScore) {
            $round->setWon('lost');
            $em->persist($round);
            $em->flush();
            return $this->redirect($this->generateUrl('blackjack_end_round'));
        }

        $em->flush();

        return $this->redirect($this->generateUrl('blackjack_game'));
    }

    public function dealerDrawAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $round = $em->getRepository('CasinoBlackjackBundle:Round')->find( $request->get('roundId') );
        $revealedNames = $em->getRepository('CasinoBlackjackBundle:Revealed')->findNamesByRoundId($round->getId());

        if ($round->getRevealed()->count() > 50) {
            $session->getFlashBag()->add('error', 'The deck is empty, this shouldn\'t happend.');
            return $this->redirect($this->generateUrl('blackjack_game'));
        }

        $deckObject = new Deck();
        $deck = $deckObject->cards();

        $dealerRevealed = $em->getRepository('CasinoBlackjackBundle:Revealed')->findDealerCards( $request->get('roundId') );
        $dealerScore = $this->getScore($deck, $dealerRevealed);
        
        if ($dealerScore) {
            while ($dealerScore < 17 && $dealerScore !== false) {
                $newRevealed = $deckObject->draw($round, $revealedNames, 'dealer');
                $em->persist($newRevealed);
                array_push($dealerRevealed, $newRevealed);
                $dealerScore = $this->getScore($deck, $dealerRevealed);
            }
        }
        else {
            $round->setWon('won');
            $em->persist($round);
            $em->flush();
            return $this->redirect($this->generateUrl('blackjack_end_round'));
        }

        $playerRevealed = $em->getRepository('CasinoBlackjackBundle:Revealed')->findPlayerCards( $request->get('roundId') );
        $playerScore = $this->getScore($deck, $playerRevealed);

        if (!$dealerScore || $dealerScore < $playerScore) {
            $round->setWon('won');
        }
        elseif ($dealerScore == $playerScore) {
            $round->setWon('tie');
        }
        else {
            $round->setWon('lost');    
        }

        $em->persist($round);
        $em->flush();

        return $this->redirect($this->generateUrl('blackjack_end_round'));
    }

    public function getScore($deck, $allCards)
    {
        $scores = array();
        foreach ($allCards as $card) {
            $cardValues = $deck[ $card->getName() ];
            if (count($scores) === 0) {
                $scores = $cardValues;
            }
            else {
                $newScores = $scores;
                foreach ($scores as $key => &$possibleScore) {
                    if (isset($cardValues[1])) {
                        array_push($newScores, ($possibleScore + $cardValues[1]));
                    }
                    $newScores[$key] += $cardValues[0];
                }
                $scores = $newScores;
            }
        }
        
        $score = false;
        foreach ($scores as $key => $possibleScore) {
            if ($possibleScore < 22) {
                $score = max($score, $possibleScore);
            }
        }

        return $score;
    }

    public function endAction($value='')
    {
        $session = new Session();
        if (!$session->get('playerId')) {
            return $this->redirect($this->generateUrl('get_blackjack_login'));
        }
        $em = $this->getDoctrine()->getManager();

        $player = $this->getCurrentPlayer( $session->get('playerId') );
        $game = $player->getGames()->last();
        $round = $game->getRounds()->last();
        $score = $game->getScore();
        $bet = $round->getBet();

        if ($round->getWon() === 'won') {
            $message = "Vous avez gagné !";
            $wallet = $player->getWallet();
            $player->setWallet( $wallet + ($bet * 2) );
            $game->setScore( $score + $bet );
            $class = "won";
        }
        elseif ($round->getWon() === 'tie') {
            $message = "Vous êtes à égalité avec la casino !";
            $wallet = $player->getWallet();
            $player->setWallet( $wallet + $bet );
            $class = "tie";
        }
        else {
            $message = "Vous avez perdu !";
            $game->setScore( $score - $bet );
            $class = "lost";
        }

        $em->persist($player);
        $em->persist($game);

        $roundId = $round->getId();
        $playerRevealed = $em->getRepository('CasinoBlackjackBundle:Revealed')->findPlayerCards($roundId);
        $dealerRevealed = $em->getRepository('CasinoBlackjackBundle:Revealed')->findDealerCards($roundId);

        $em->flush();

        return $this->render('CasinoBlackjackBundle:Game:end.html.twig', array(
            'player' => $player,
            'game' => $game,
            'round' => $round,
            'message' => $message,
            'result' => $class,
            'playerRevealed' => $playerRevealed,
            'dealerRevealed' => $dealerRevealed
        ));
    }

}
