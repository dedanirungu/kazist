<?php

namespace Users\Groups\Roles\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Roles
 *
 * @ORM\Table(name="users_groups_roles")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Roles extends \Kazist\Table\BaseTable {

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
     * @ORM\Column(name="role_id", type="integer", length=11, nullable=false)
     */
    protected $role_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="group_id", type="integer", length=11, nullable=false)
     */
    protected $group_id;

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
     * Set role_id
     *
     * @param integer $roleId
     * @return Roles
     */
    public function setRoleId($roleId) {
        $this->role_id = $roleId;

        return $this;
    }

    /**
     * Get role_id
     *
     * @return integer 
     */
    public function getRoleId() {
        return $this->role_id;
    }

    /**
     * Set group_id
     *
     * @param integer $groupId
     * @return Roles
     */
    public function setGroupId($groupId) {
        $this->group_id = $groupId;

        return $this;
    }

    /**
     * Get group_id
     *
     * @return integer 
     */
    public function getGroupId() {
        return $this->group_id;
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

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        // Add your code here
    }

}
