<?php

namespace Users\Permission\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission
 *
 * @ORM\Table(name="users_permission", indexes={@ORM\Index(name="subset_id_index", columns={"subset_id"}), @ORM\Index(name="role_id_index", columns={"role_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Permission extends \Kazist\Table\BaseTable {

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
     * @ORM\Column(name="subset_id", type="integer", length=11, nullable=false)
     */
    protected $subset_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="role_id", type="integer", length=11, nullable=false)
     */
    protected $role_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_add", type="integer", length=11, nullable=true)
     */
    protected $can_add;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_view", type="integer", length=11, nullable=true)
     */
    protected $can_view;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_write", type="integer", length=11, nullable=true)
     */
    protected $can_write;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_delete", type="integer", length=11, nullable=true)
     */
    protected $can_delete;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_viewown", type="integer", length=11, nullable=true)
     */
    protected $can_viewown;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_writeown", type="integer", length=11, nullable=true)
     */
    protected $can_writeown;

    /**
     * @var integer
     *
     * @ORM\Column(name="can_deleteown", type="integer", length=11, nullable=true)
     */
    protected $can_deleteown;

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
     * Set subset_id
     *
     * @param integer $subsetId
     * @return Permission
     */
    public function setSubsetId($subsetId) {
        $this->subset_id = $subsetId;

        return $this;
    }

    /**
     * Get subset_id
     *
     * @return integer 
     */
    public function getSubsetId() {
        return $this->subset_id;
    }

    /**
     * Set role_id
     *
     * @param integer $roleId
     * @return Permission
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
     * Set can_add
     *
     * @param integer $canAdd
     * @return Permission
     */
    public function setCanAdd($canAdd) {
        $this->can_add = $canAdd;

        return $this;
    }

    /**
     * Get can_add
     *
     * @return integer 
     */
    public function getCanAdd() {
        return $this->can_add;
    }

    /**
     * Set can_view
     *
     * @param integer $canView
     * @return Permission
     */
    public function setCanView($canView) {
        $this->can_view = $canView;

        return $this;
    }

    /**
     * Get can_view
     *
     * @return integer 
     */
    public function getCanView() {
        return $this->can_view;
    }

    /**
     * Set can_write
     *
     * @param integer $canWrite
     * @return Permission
     */
    public function setCanWrite($canWrite) {
        $this->can_write = $canWrite;

        return $this;
    }

    /**
     * Get can_write
     *
     * @return integer 
     */
    public function getCanWrite() {
        return $this->can_write;
    }

    /**
     * Set can_delete
     *
     * @param integer $canDelete
     * @return Permission
     */
    public function setCanDelete($canDelete) {
        $this->can_delete = $canDelete;

        return $this;
    }

    /**
     * Get can_delete
     *
     * @return integer 
     */
    public function getCanDelete() {
        return $this->can_delete;
    }

    /**
     * Set can_viewown
     *
     * @param integer $canViewown
     * @return Permission
     */
    public function setCanViewown($canViewown) {
        $this->can_viewown = $canViewown;

        return $this;
    }

    /**
     * Get can_viewown
     *
     * @return integer 
     */
    public function getCanViewown() {
        return $this->can_viewown;
    }

    /**
     * Set can_writeown
     *
     * @param integer $canWriteown
     * @return Permission
     */
    public function setCanWriteown($canWriteown) {
        $this->can_writeown = $canWriteown;

        return $this;
    }

    /**
     * Get can_writeown
     *
     * @return integer 
     */
    public function getCanWriteown() {
        return $this->can_writeown;
    }

    /**
     * Set can_deleteown
     *
     * @param integer $canDeleteown
     * @return Permission
     */
    public function setCanDeleteown($canDeleteown) {
        $this->can_deleteown = $canDeleteown;

        return $this;
    }

    /**
     * Get can_deleteown
     *
     * @return integer 
     */
    public function getCanDeleteown() {
        return $this->can_deleteown;
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
