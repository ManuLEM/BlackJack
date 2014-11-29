<?php

namespace Casino\BlackjackBundle\Controller;

use Casino\BlackjackBundle\Controller\DefaultController;
use Casino\BlackjackBundle\Entity\Player;
use Casino\BlackjackBundle\Form\Type\PlayerType;

class InfoController extends DefaultController
{
    public function getStatsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $stats = $em->getRepository('CasinoBlackjackBundle:Player')->getStats();

        return $this->render('CasinoBlackjackBundle:Stats:index.html.twig', array(
            'stats' => $stats
        ));
    }
}
