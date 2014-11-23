<?php

namespace Casino\BlackjackBundle\Entity;

use Symfony\Component\Yaml\Parser;
use Casino\BlackjackBundle\Entity\Revealed;

class Deck
{
    public static function cards() {
        $yaml = new Parser();

        $value = $yaml->parse(file_get_contents(dirname(__DIR__).'/Resources/config/deck.yml'));
        
        return $value;
    }

    public function draw($round, $revealedNames, $playerType)
    {
        $deck = $this->cards();
        $name = array_rand($deck);
        $nameArray = array('name' => $name);

        if (in_array($nameArray, $revealedNames)) {
            return $this->draw($round, $revealedNames);
        }

        $revealed = new Revealed();
        $revealed->setName($name);
        $revealed->setPlayerType($name);
        $revealed->setRound($round);

        return $revealed;
    }
}