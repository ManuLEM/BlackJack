<?php

namespace Casino\BlackjackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Revealed
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Casino\BlackjackBundle\Entity\RevealedRepository")
 */
class Revealed
{
    /**
     * @ORM\ManyToOne(targetEntity="Round", inversedBy="revealeds")
     * @ORM\JoinColumn(name="round_id", referencedColumnName="id")
     */
    protected $round;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */

    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="player_type", type="string", length=50)
     */
    private $playerType;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Revealed
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set round
     *
     * @param \Casino\BlackjackBundle\Entity\Round $round
     * @return Revealed
     */
    public function setRound(\Casino\BlackjackBundle\Entity\Round $round = null)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round
     *
     * @return \Casino\BlackjackBundle\Entity\Round 
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set playerType
     *
     * @param string $playerType
     * @return Revealed
     */
    public function setPlayerType($playerType)
    {
        $this->playerType = $playerType;

        return $this;
    }

    /**
     * Get playerType
     *
     * @return string 
     */
    public function getPlayerType()
    {
        return $this->playerType;
    }
}
