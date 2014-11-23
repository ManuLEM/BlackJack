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

        return $this->render('CasinoBlackjackBundle:Game:index.html.twig', array(
            'player' => $player,
        ));
    }

    public function loginAction()
    {
        $player = new Player();

        $form = $this->createForm(new PlayerType(), $player, array(
            'action' => $this->generateUrl('post_blackjack_login'),
            'method' => 'POST',
        ));

        return $this->render('CasinoBlackjackBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
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
}
