<?php

namespace Users\Groups\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table(name="users_groups")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Groups extends \Kazist\Table\BaseTable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="public_default", type="integer", length=11, nullable=false)
     */
    protected $public_default;

    /**
     * @var integer
     *
     * @ORM\Column(name="registered_default", type="integer", length=11, nullable=false)
     */
    protected $registered_default;

    /**
     * @var integer
     *
     * @ORM\Column(name="published", type="integer", length=11, nullable=true)
     */
    protected $published;

    /**
     * @var string
     *
     * @ORM\Column(name="rights", type="text", nullable=true)
     */
    protected $rights;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", length=11, nullable=false)
     */
    protected $created_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    protected $date_created;

    /**
     * @var integer
     *
     * @ORM\Column(name="modified_by", type="integer", length=11, nullable=false)
     */
    protected $modified_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=false)
     */
    protected $date_modified;


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
     *
     * @return Groups
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
     * Set description
     *
     * @param string $description
     *
     * @return Groups
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set publicDefault
     *
     * @param integer $publicDefault
     *
     * @return Groups
     */
    public function setPublicDefault($publicDefault)
    {
        $this->public_default = $publicDefault;

        return $this;
    }

    /**
     * Get publicDefault
     *
     * @return integer
     */
    public function getPublicDefault()
    {
        return $this->public_default;
    }

    /**
     * Set registeredDefault
     *
     * @param integer $registeredDefault
     *
     * @return Groups
     */
    public function setRegisteredDefault($registeredDefault)
    {
        $this->registered_default = $registeredDefault;

        return $this;
    }

    /**
     * Get registeredDefault
     *
     * @return integer
     */
    public function getRegisteredDefault()
    {
        return $this->registered_default;
    }

    /**
     * Set published
     *
     * @param integer $published
     *
     * @return Groups
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return integer
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set rights
     *
     * @param string $rights
     *
     * @return Groups
     */
    public function setRights($rights)
    {
        $this->rights = $rights;

        return $this;
    }

    /**
     * Get rights
     *
     * @return string
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Get modifiedBy
     *
     * @return integer
     */
    public function getModifiedBy()
    {
        return $this->modified_by;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }
    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        // Add your code here
    }
}

