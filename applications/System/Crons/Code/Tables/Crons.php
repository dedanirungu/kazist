<?php

namespace System\Crons\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Crons
 *
 * @ORM\Table(name="system_crons", indexes={@ORM\Index(name="subset_id_index", columns={"subset_id"}), @ORM\Index(name="controller_index", columns={"controller"}), @ORM\Index(name="function_index", columns={"function"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Crons extends \Kazist\Table\BaseTable
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
     * @ORM\Column(name="subset_id", type="integer", length=11, nullable=true)
     */
    protected $subset_id;

    /**
     * @var string
     *
     * @ORM\Column(name="unique_name", type="string", length=255, nullable=false)
     */
    protected $unique_name;

    /**
     * @var string
     *
     * @ORM\Column(name="controller", type="string", length=255, nullable=false)
     */
    protected $controller;

    /**
     * @var string
     *
     * @ORM\Column(name="function", type="string", length=255, nullable=false)
     */
    protected $function;

    /**
     * @var string
     *
     * @ORM\Column(name="minute", type="string", length=255, nullable=true)
     */
    protected $minute;

    /**
     * @var string
     *
     * @ORM\Column(name="hour", type="string", length=255, nullable=true)
     */
    protected $hour;

    /**
     * @var string
     *
     * @ORM\Column(name="day_of_month", type="string", length=255, nullable=true)
     */
    protected $day_of_month;

    /**
     * @var string
     *
     * @ORM\Column(name="month", type="string", length=255, nullable=true)
     */
    protected $month;

    /**
     * @var string
     *
     * @ORM\Column(name="day_of_week", type="string", length=255, nullable=true)
     */
    protected $day_of_week;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=255, nullable=true)
     */
    protected $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="completed_running", type="integer", length=11, nullable=true)
     */
    protected $completed_running;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_run_time", type="datetime", nullable=true)
     */
    protected $next_run_time;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_new", type="integer", length=11, nullable=true)
     */
    protected $is_new;

    /**
     * @var string
     *
     * @ORM\Column(name="locked_key", type="string", length=255)
     */
    protected $locked_key;

    /**
     * @var string
     *
     * @ORM\Column(name="extension_path", type="string", length=255, nullable=false)
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
     * Set subsetId
     *
     * @param integer $subsetId
     *
     * @return Crons
     */
    public function setSubsetId($subsetId)
    {
        $this->subset_id = $subsetId;

        return $this;
    }

    /**
     * Get subsetId
     *
     * @return integer
     */
    public function getSubsetId()
    {
        return $this->subset_id;
    }

    /**
     * Set uniqueName
     *
     * @param string $uniqueName
     *
     * @return Crons
     */
    public function setUniqueName($uniqueName)
    {
        $this->unique_name = $uniqueName;

        return $this;
    }

    /**
     * Get uniqueName
     *
     * @return string
     */
    public function getUniqueName()
    {
        return $this->unique_name;
    }

    /**
     * Set controller
     *
     * @param string $controller
     *
     * @return Crons
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set function
     *
     * @param string $function
     *
     * @return Crons
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * Get function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set minute
     *
     * @param string $minute
     *
     * @return Crons
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;

        return $this;
    }

    /**
     * Get minute
     *
     * @return string
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Set hour
     *
     * @param string $hour
     *
     * @return Crons
     */
    public function setHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * Get hour
     *
     * @return string
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set dayOfMonth
     *
     * @param string $dayOfMonth
     *
     * @return Crons
     */
    public function setDayOfMonth($dayOfMonth)
    {
        $this->day_of_month = $dayOfMonth;

        return $this;
    }

    /**
     * Get dayOfMonth
     *
     * @return string
     */
    public function getDayOfMonth()
    {
        return $this->day_of_month;
    }

    /**
     * Set month
     *
     * @param string $month
     *
     * @return Crons
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return string
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set dayOfWeek
     *
     * @param string $dayOfWeek
     *
     * @return Crons
     */
    public function setDayOfWeek($dayOfWeek)
    {
        $this->day_of_week = $dayOfWeek;

        return $this;
    }

    /**
     * Get dayOfWeek
     *
     * @return string
     */
    public function getDayOfWeek()
    {
        return $this->day_of_week;
    }

    /**
     * Set year
     *
     * @param string $year
     *
     * @return Crons
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set completedRunning
     *
     * @param integer $completedRunning
     *
     * @return Crons
     */
    public function setCompletedRunning($completedRunning)
    {
        $this->completed_running = $completedRunning;

        return $this;
    }

    /**
     * Get completedRunning
     *
     * @return integer
     */
    public function getCompletedRunning()
    {
        return $this->completed_running;
    }

    /**
     * Set nextRunTime
     *
     * @param \DateTime $nextRunTime
     *
     * @return Crons
     */
    public function setNextRunTime($nextRunTime)
    {
        $this->next_run_time = $nextRunTime;

        return $this;
    }

    /**
     * Get nextRunTime
     *
     * @return \DateTime
     */
    public function getNextRunTime()
    {
        return $this->next_run_time;
    }

    /**
     * Set isNew
     *
     * @param integer $isNew
     *
     * @return Crons
     */
    public function setIsNew($isNew)
    {
        $this->is_new = $isNew;

        return $this;
    }

    /**
     * Get isNew
     *
     * @return integer
     */
    public function getIsNew()
    {
        return $this->is_new;
    }

    /**
     * Set lockedKey
     *
     * @param string $lockedKey
     *
     * @return Crons
     */
    public function setLockedKey($lockedKey)
    {
        $this->locked_key = $lockedKey;

        return $this;
    }

    /**
     * Get lockedKey
     *
     * @return string
     */
    public function getLockedKey()
    {
        return $this->locked_key;
    }

    /**
     * Set extensionPath
     *
     * @param string $extensionPath
     *
     * @return Crons
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
     * @return Crons
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

