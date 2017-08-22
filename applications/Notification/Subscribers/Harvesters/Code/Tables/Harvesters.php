<?php

namespace Notification\Subscribers\Harvesters\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Harvesters
 *
 * @ORM\Table(name="notification_subscribers_harvesters", indexes={@ORM\Index(name="group_id_index", columns={"group_id"}), @ORM\Index(name="created_by_index", columns={"created_by"}), @ORM\Index(name="modified_by_index", columns={"modified_by"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Harvesters extends \Kazist\Table\BaseTable
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="group_id", type="integer", length=11, nullable=true)
     */
    protected $group_id;

    /**
     * @var string
     *
     * @ORM\Column(name="email_field", type="string", length=255, nullable=true)
     */
    protected $email_field;

    /**
     * @var string
     *
     * @ORM\Column(name="user_field", type="string", length=255, nullable=true)
     */
    protected $user_field;

    /**
     * @var string
     *
     * @ORM\Column(name="extension_path", type="string", length=255, nullable=true)
     */
    protected $extension_path;

    /**
     * @var integer
     *
     * @ORM\Column(name="published", type="integer", length=11, nullable=true)
     */
    protected $published;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", length=11, nullable=true)
     */
    protected $created_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    protected $date_created;

    /**
     * @var integer
     *
     * @ORM\Column(name="modified_by", type="integer", length=11, nullable=true)
     */
    protected $modified_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
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
     * Set title
     *
     * @param string $title
     *
     * @return Harvesters
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     *
     * @return Harvesters
     */
    public function setGroupId($groupId)
    {
        $this->group_id = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * Set emailField
     *
     * @param string $emailField
     *
     * @return Harvesters
     */
    public function setEmailField($emailField)
    {
        $this->email_field = $emailField;

        return $this;
    }

    /**
     * Get emailField
     *
     * @return string
     */
    public function getEmailField()
    {
        return $this->email_field;
    }

    /**
     * Set userField
     *
     * @param string $userField
     *
     * @return Harvesters
     */
    public function setUserField($userField)
    {
        $this->user_field = $userField;

        return $this;
    }

    /**
     * Get userField
     *
     * @return string
     */
    public function getUserField()
    {
        return $this->user_field;
    }

    /**
     * Set extensionPath
     *
     * @param string $extensionPath
     *
     * @return Harvesters
     */
    public function setExtensionPath($extensionPath)
    {
        $this->extension_path = $extensionPath;

        return $this;
    }

    /**
     * Get extensionPath
     *
     * @return string
     */
    public function getExtensionPath()
    {
        return $this->extension_path;
    }

    /**
     * Set published
     *
     * @param integer $published
     *
     * @return Harvesters
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

