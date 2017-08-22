<?php

namespace System\Listeners\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Listeners
 *
 * @ORM\Table(name="system_listeners")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Listeners extends \Kazist\Table\BaseTable {

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
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255, nullable=false)
     */
    protected $class;

    /**
     * @var string
     *
     * @ORM\Column(name="extension_path", type="string", length=255, nullable=false)
     */
    protected $extension_path;

    /**
     * @var integer
     *
     * @ORM\Column(name="published", type="integer", length=11)
     */
    protected $published;
    /**
     * @var integer
     *
     * @ORM\Column(name="ordering", type="integer", length=11)
     */
    protected $ordering;

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
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Listeners
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Listeners
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return Listeners
     */
    public function setClass($class) {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * Set extension_path
     *
     * @param string $extensionPath
     * @return Listeners
     */
    public function setExtensionPath($extensionPath) {
        $this->extension_path = $extensionPath;

        return $this;
    }

    /**
     * Get extension_path
     *
     * @return string 
     */
    public function getExtensionPath() {
        return $this->extension_path;
    }

    /**
     * Set published
     *
     * @param integer $published
     * @return Listeners
     */
    public function setPublished($published) {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return integer 
     */
    public function getPublished() {
        return $this->published;
    }
    /**
     * Set ordering
     *
     * @param integer $ordering
     * @return Listeners
     */
    public function setOrdering($ordering) {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get published
     *
     * @return integer 
     */
    public function getOrdering() {
        return $this->ordering;
    }

    /**
     * Get created_by
     *
     * @return integer 
     */
    public function getCreatedBy() {
        return $this->created_by;
    }

    /**
     * Get date_created
     *
     * @return \DateTime 
     */
    public function getDateCreated() {
        return $this->date_created;
    }

    /**
     * Get modified_by
     *
     * @return integer 
     */
    public function getModifiedBy() {
        return $this->modified_by;
    }

    /**
     * Get date_modified
     *
     * @return \DateTime 
     */
    public function getDateModified() {
        return $this->date_modified;
    }

}
