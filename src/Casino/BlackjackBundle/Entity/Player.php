<?php

namespace Casino\BlackjackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Player
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Casino\BlackjackBundle\Entity\PlayerRepository")
 */
class Player
{
    /**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="player")
     */
    protected $games;

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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="wallet", type="integer")
     */
    private $wallet = 10000;

    /**
     * Get player
     *
     * @return Player 
     */
    public function getPlayer()
    {
        return $this;
    }

    /**
     * Get player from id
     *
     * @return Player 
     */
    public function getPlayerFromId($id)
    {
        return $this;
    }

    /**
     * Is player
     *
     * @return bool 
     */
    public function isPlayer()
    {
        $player = $this->getDoctrine()
                ->getRepository('BlackjackBundle:Player')
                ->find($this->id);
        if (!$player) {
            return false;
        }

        return true;
    }

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
     * @return Player
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
     * Set wallet
     *
     * @param integer $wallet
     * @return Player
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Get wallet
     *
     * @return integer 
     */
    public function getWallet()
    {
        return $this->wallet;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add games
     *
     * @param \Casino\BlackjackBundle\Entity\Game $games
     * @return Player
     */
    public function addGame(\Casino\BlackjackBundle\Entity\Game $games)
    {
        $this->games[] = $games;

        return $this;
    }

    /**
     * Remove games
     *
     * @param \Casino\BlackjackBundle\Entity\Game $games
     */
    public function removeGame(\Casino\BlackjackBundle\Entity\Game $games)
    {
        $this->games->removeElement($games);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Player
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Player
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }
}
