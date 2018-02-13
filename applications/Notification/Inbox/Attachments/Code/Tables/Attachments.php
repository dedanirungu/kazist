<?php

namespace Notification\Inbox\Attachments\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attachments
 *
 * @ORM\Table(name="notification_inbox_attachments", indexes={@ORM\Index(name="inbox_id_index", columns={"inbox_id"}), @ORM\Index(name="modified_by_index", columns={"modified_by"}), @ORM\Index(name="created_by_index", columns={"created_by"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Attachments extends \Kazist\Table\BaseTable
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
     * @var integer
     *
     * @ORM\Column(name="inbox_id", type="integer", length=11, nullable=false)
     */
    protected $inbox_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="file", type="integer", length=11, nullable=true)
     */
    protected $file;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    protected $date_created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    protected $date_modified;

    /**
     * @var integer
     *
     * @ORM\Column(name="modified_by", type="integer", length=11, nullable=true)
     */
    protected $modified_by;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", length=11, nullable=true)
     */
    protected $created_by;


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
     * Set inboxId
     *
     * @param integer $inboxId
     *
     * @return Attachments
     */
    public function setInboxId($inboxId)
    {
        $this->inbox_id = $inboxId;

        return $this;
    }

    /**
     * Get inboxId
     *
     * @return integer
     */
    public function getInboxId()
    {
        return $this->inbox_id;
    }

    /**
     * Set file
     *
     * @param integer $file
     *
     * @return Attachments
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return integer
     */
    public function getFile()
    {
        return $this->file;
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
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->date_modified;
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
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }
    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        // Add your code here
    }
}

