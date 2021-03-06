<?php declare(strict_types = 1);

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use \DateTime;

/**
 * @ORM\Entity()
 * @UniqueEntity(
 *     "name",
 *      message="League {{ value }} already exists!"
 * )
 * @ORM\Table(name="league")
 * @ORM\HasLifecycleCallbacks()
 */
class League
{
    const V_ACTIVE = true;
    const V_DISABLED = false;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\Length(
     *     min = 2,
     *     max = 20,
     *     minMessage="League name must be at least {{ limit }} characters long",
     *     maxMessage="League name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="name", type="string", length=20, unique=true)
     */
    private $name;

    /**
     * @Assert\Length(
     *     min = 5,
     *     max = 255,
     *     minMessage="Description must be at least {{ limit }} characters long",
     *     maxMessage="Description cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @Assert\Type(
     *     type="boolean",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $isPrivate;

    /**
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="LeagueHasUser", mappedBy="league")
     */
    private $leagueHasUser;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pick", mappedBy="league")
     */
    private $picks;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\LeagueOptions", inversedBy="league", cascade={"persist"})
     */
    private $options;

    /**
     * @ORM\Column(name="last_change_on", type="datetime")
     */
    private $lastChangeOn;

    /**
     * @ORM\Column(name = "is_active", type = "boolean")
     */
    private $isActive;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->leagueHasUser = new ArrayCollection();
        $this->picks = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return League
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     *
     * @return League
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param boolean $isPrivate
     *
     * @return League
     */
    public function setIsPrivate(bool $isPrivate)
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param string $password
     *
     * @return League
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return ArrayCollection|LeagueHasUser[]
     */
    public function getLeagueHasUsers()
    {
        return $this->leagueHasUser;
    }

    /**
     * @return ArrayCollection|Pick[]
     */
    public function getPicks()
    {
        return $this->picks;
    }

    /**
     * @return DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * @param $lastChangeOn
     *
     * @return League
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
        return $this;
    }

    /**
     * @return LeagueOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param LeagueOptions $options
     *
     * @return League
     */
    public function setOptions(LeagueOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return League
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

    /**
     * @return array
     */
    public function getUsers()
    {
        $users = [];
        foreach ($this->getLeagueHasUsers() as $leagueHasUser) {
            $users[] = $leagueHasUser->getUser();
        }

        return $users;
    }
}

