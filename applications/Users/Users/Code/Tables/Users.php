<?php

namespace Users\Users\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users_users", indexes={@ORM\Index(name="inviter_id_index", columns={"inviter_id"}), @ORM\Index(name="country_id_index", columns={"country_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Users extends \Kazist\Table\BaseTable
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
     * @ORM\Column(name="inviter_id", type="integer", length=11, nullable=true)
     */
    protected $inviter_id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_admin", type="integer", length=11, nullable=true)
     */
    protected $is_admin;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     */
    protected $gender;

    /**
     * @var integer
     *
     * @ORM\Column(name="country_id", type="integer", length=11, nullable=true)
     */
    protected $country_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="location_id", type="integer", length=11, nullable=true)
     */
    protected $location_id;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=255, nullable=true)
     */
    protected $town;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_iso", type="string", length=255, nullable=true)
     */
    protected $phone_iso;

    /**
     * @var integer
     *
     * @ORM\Column(name="phone_code", type="integer", length=11, nullable=true)
     */
    protected $phone_code;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    protected $address;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer", length=11, nullable=true)
     */
    protected $points;

    /**
     * @var integer
     *
     * @ORM\Column(name="avatar", type="integer", length=11, nullable=true)
     */
    protected $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=true)
     */
    protected $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="verification", type="string", length=255, nullable=true)
     */
    protected $verification;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_verified", type="integer", length=11, nullable=true)
     */
    protected $is_verified;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordering", type="integer", length=11, nullable=true)
     */
    protected $ordering;

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
     * @var \DateTime
     *
     * @ORM\Column(name="last_date_active", type="datetime", nullable=true)
     */
    protected $last_date_active;


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
     * Set inviterId
     *
     * @param integer $inviterId
     *
     * @return Users
     */
    public function setInviterId($inviterId)
    {
        $this->inviter_id = $inviterId;

        return $this;
    }

    /**
     * Get inviterId
     *
     * @return integer
     */
    public function getInviterId()
    {
        return $this->inviter_id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Users
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
     * Set isAdmin
     *
     * @param integer $isAdmin
     *
     * @return Users
     */
    public function setIsAdmin($isAdmin)
    {
        $this->is_admin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return integer
     */
    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Users
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Users
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
     *
     * @return Users
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

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Users
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     *
     * @return Users
     */
    public function setCountryId($countryId)
    {
        $this->country_id = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * Set locationId
     *
     * @param integer $locationId
     *
     * @return Users
     */
    public function setLocationId($locationId)
    {
        $this->location_id = $locationId;

        return $this;
    }

    /**
     * Get locationId
     *
     * @return integer
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Set town
     *
     * @param string $town
     *
     * @return Users
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Users
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phoneIso
     *
     * @param string $phoneIso
     *
     * @return Users
     */
    public function setPhoneIso($phoneIso)
    {
        $this->phone_iso = $phoneIso;

        return $this;
    }

    /**
     * Get phoneIso
     *
     * @return string
     */
    public function getPhoneIso()
    {
        return $this->phone_iso;
    }

    /**
     * Set phoneCode
     *
     * @param integer $phoneCode
     *
     * @return Users
     */
    public function setPhoneCode($phoneCode)
    {
        $this->phone_code = $phoneCode;

        return $this;
    }

    /**
     * Get phoneCode
     *
     * @return integer
     */
    public function getPhoneCode()
    {
        return $this->phone_code;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Users
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return Users
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set avatar
     *
     * @param integer $avatar
     *
     * @return Users
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return integer
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Users
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set verification
     *
     * @param string $verification
     *
     * @return Users
     */
    public function setVerification($verification)
    {
        $this->verification = $verification;

        return $this;
    }

    /**
     * Get verification
     *
     * @return string
     */
    public function getVerification()
    {
        return $this->verification;
    }

    /**
     * Set isVerified
     *
     * @param integer $isVerified
     *
     * @return Users
     */
    public function setIsVerified($isVerified)
    {
        $this->is_verified = $isVerified;

        return $this;
    }

    /**
     * Get isVerified
     *
     * @return integer
     */
    public function getIsVerified()
    {
        return $this->is_verified;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering
     *
     * @return Users
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get ordering
     *
     * @return integer
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set published
     *
     * @param integer $published
     *
     * @return Users
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
     * Set lastDateActive
     *
     * @param \DateTime $lastDateActive
     *
     * @return Users
     */
    public function setLastDateActive($lastDateActive)
    {
        $this->last_date_active = $lastDateActive;

        return $this;
    }

    /**
     * Get lastDateActive
     *
     * @return \DateTime
     */
    public function getLastDateActive()
    {
        return $this->last_date_active;
    }
    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        // Add your code here
    }
}

