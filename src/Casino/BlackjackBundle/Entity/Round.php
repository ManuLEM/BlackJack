<?php

namespace Casino\BlackjackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Round
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Casino\BlackjackBundle\Entity\RoundRepository")
 */
class Round
{
    /**
     * @ORM\OneToMany(targetEntity="Revealed", mappedBy="round")
     */
    protected $revealeds;

    /**
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="rounds")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    protected $game;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="bet", type="integer")
     */
    private $bet = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="won", type="string", length=50)
     */
    private $won = '';


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
     * Set bet
     *
     * @param integer $bet
     * @return Round
     */
    public function setBet($bet)
    {
        $this->bet = $bet;

        return $this;
    }

    /**
     * Get bet
     *
     * @return integer 
     */
    public function getBet()
    {
        return $this->bet;
    }

    /**
     * Set won
     *
     * @param string $won
     * @return Round
     */
    public function setWon($won)
    {
        $this->won = $won;

        return $this;
    }

    /**
     * Get won
     *
     * @return string 
     */
    public function getWon()
    {
        return $this->won;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->revealeds = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add revealeds
     *
     * @param \Casino\BlackjackBundle\Entity\Revealed $revealed
     * @return Round
     */
    public function addRevealed(\Casino\BlackjackBundle\Entity\Revealed $revealed)
    {
        $this->revealeds[] = $revealed;

        return $this;
    }

    /**
     * Remove revealeds
     *
     * @param \Casino\BlackjackBundle\Entity\Revealed $revealed
     */
    public function removeRevealed(\Casino\BlackjackBundle\Entity\Revealed $revealed)
    {
        $this->revealeds->removeElement($revealed);
    }

    /**
     * Get revealeds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRevealed()
    {
        return $this->revealeds;
    }

    /**
     * Set game
     *
     * @param \Casino\BlackjackBundle\Entity\Game $game
     * @return Round
     */
    public function setGame(\Casino\BlackjackBundle\Entity\Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \Casino\BlackjackBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Get revealeds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRevealeds()
    {
        return $this->revealeds;
    }
}
