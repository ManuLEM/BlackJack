<?php

namespace Casino\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Casino\BlackjackBundle\Entity\Player;
use Casino\BlackjackBundle\Form\Type\PlayerType;

class DefaultController extends Controller
{
    public function getCurrentPlayer($id)
    {
        $em = $this->getDoctrine()->getManager();
        $player = $em->getRepository('CasinoBlackjackBundle:Player')->find($id);

        return $player;
    }
}
