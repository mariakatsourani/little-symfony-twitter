<?php
namespace Test\TwitterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="relationship")
 */
class Relationship{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="follower", referencedColumnName="id")
     */
    protected $follower;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    protected $following;

    // ------------------ Getters & Setters ------------------ //

    /**
     * Set follower
     *
     * @param integer $follower
     * @return Relationship
     */
    public function setFollower($follower)
    {
        $this->follower = $follower;

        return $this;
    }

    /**
     * Get follower
     *
     * @return integer 
     */
    public function getFollower()
    {
        return $this->follower;
    }

    /**
     * Set following
     *
     * @param integer $following
     * @return Relationship
     */
    public function setFollowing($following)
    {
        $this->following = $following;

        return $this;
    }

    /**
     * Get following
     *
     * @return integer 
     */
    public function getFollowing()
    {
        return $this->following;
    }
}
