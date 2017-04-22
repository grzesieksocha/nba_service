<?php declare(strict_types = 1);

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity()
 * @UniqueEntity(
 *     "name",
 *      message="Conference {{ value }} already exists!"
 * )
 * @ORM\Table(name = "conference")
 * @ORM\HasLifecycleCallbacks()
 */
class Conference
{
    const V_ACTIVE = true;
    const V_DISABLED = false;

    /**
     * @ORM\Id
     * @ORM\Column(name = "id", type = "integer")
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name = "name", type = "string", length = 64)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\Team", mappedBy = "conference")
     */
    private $teams;

    /**
     * @ORM\Column(name = "last_change_on", type = "datetime")
     */
    private $lastChangeOn;

    /**
     * @ORM\Column(name = "is_active", type = "boolean")
     */
    private $isActive;

    /**
     * Conference constructor
     */
    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Conference
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection|Team[]
     */
    public function getTeams()
    {
        return $this->teams;
    }

    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * @param $lastChangeOn
     *
     * @return Conference
     */
    public function setLastChangeOn(DateTime $lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return Conference
     */
    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateLastChangedOn()
    {
        $this->setLastChangeOn(new DateTime(date('Y-m-d H:i:s')));
    }
}