<?php

namespace Notification\Gateways\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gateways
 *
 * @ORM\Table(name="notification_gateways", indexes={@ORM\Index(name="created_by_index", columns={"created_by"}), @ORM\Index(name="modified_by_index", columns={"modified_by"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Gateways extends \Kazist\Table\BaseTable
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="smtp_auth", type="integer", length=11, nullable=true)
     */
    protected $smtp_auth;

    /**
     * @var string
     *
     * @ORM\Column(name="smtp_secure", type="string", length=255, nullable=true)
     */
    protected $smtp_secure;

    /**
     * @var string
     *
     * @ORM\Column(name="smtp_host", type="string", length=255, nullable=true)
     */
    protected $smtp_host;

    /**
     * @var string
     *
     * @ORM\Column(name="smtp_username", type="string", length=255, nullable=true)
     */
    protected $smtp_username;

    /**
     * @var string
     *
     * @ORM\Column(name="smtp_password", type="string", length=255, nullable=true)
     */
    protected $smtp_password;

    /**
     * @var string
     *
     * @ORM\Column(name="smtp_port", type="string", length=255, nullable=true)
     */
    protected $smtp_port;

    /**
     * @var string
     *
     * @ORM\Column(name="from_email", type="string", length=255, nullable=true)
     */
    protected $from_email;

    /**
     * @var string
     *
     * @ORM\Column(name="from_name", type="string", length=255, nullable=true)
     */
    protected $from_name;

    /**
     * @var string
     *
     * @ORM\Column(name="sendmail_command", type="string", length=255, nullable=true)
     */
    protected $sendmail_command;

    /**
     * @var string
     *
     * @ORM\Column(name="auto_reply_to", type="string", length=255, nullable=true)
     */
    protected $auto_reply_to;

    /**
     * @var integer
     *
     * @ORM\Column(name="mail_debuger", type="integer", length=11, nullable=true)
     */
    protected $mail_debuger;

    /**
     * @var integer
     *
     * @ORM\Column(name="debug_exit", type="integer", length=11, nullable=true)
     */
    protected $debug_exit;

    /**
     * @var integer
     *
     * @ORM\Column(name="use_template", type="integer", length=11, nullable=true)
     */
    protected $use_template;

    /**
     * @var integer
     *
     * @ORM\Column(name="sql_limit", type="integer", length=11, nullable=true)
     */
    protected $sql_limit;

    /**
     * @var integer
     *
     * @ORM\Column(name="anti_flood", type="integer", length=11, nullable=true)
     */
    protected $anti_flood;

    /**
     * @var integer
     *
     * @ORM\Column(name="throttler", type="integer", length=11, nullable=true)
     */
    protected $throttler;

    /**
     * @var integer
     *
     * @ORM\Column(name="incoming", type="integer", length=11, nullable=true)
     */
    protected $incoming;

    /**
     * @var string
     *
     * @ORM\Column(name="incoming_port", type="string", length=255, nullable=true)
     */
    protected $incoming_port;

    /**
     * @var string
     *
     * @ORM\Column(name="incoming_url", type="string", length=255, nullable=true)
     */
    protected $incoming_url;

    /**
     * @var integer
     *
     * @ORM\Column(name="incoming_sslvalidate", type="integer", length=11, nullable=true)
     */
    protected $incoming_sslvalidate;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_html", type="integer", length=11, nullable=true)
     */
    protected $is_html;

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
     * @return Gateways
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
     * Set type
     *
     * @param string $type
     *
     * @return Gateways
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set smtpAuth
     *
     * @param integer $smtpAuth
     *
     * @return Gateways
     */
    public function setSmtpAuth($smtpAuth)
    {
        $this->smtp_auth = $smtpAuth;

        return $this;
    }

    /**
     * Get smtpAuth
     *
     * @return integer
     */
    public function getSmtpAuth()
    {
        return $this->smtp_auth;
    }

    /**
     * Set smtpSecure
     *
     * @param string $smtpSecure
     *
     * @return Gateways
     */
    public function setSmtpSecure($smtpSecure)
    {
        $this->smtp_secure = $smtpSecure;

        return $this;
    }

    /**
     * Get smtpSecure
     *
     * @return string
     */
    public function getSmtpSecure()
    {
        return $this->smtp_secure;
    }

    /**
     * Set smtpHost
     *
     * @param string $smtpHost
     *
     * @return Gateways
     */
    public function setSmtpHost($smtpHost)
    {
        $this->smtp_host = $smtpHost;

        return $this;
    }

    /**
     * Get smtpHost
     *
     * @return string
     */
    public function getSmtpHost()
    {
        return $this->smtp_host;
    }

    /**
     * Set smtpUsername
     *
     * @param string $smtpUsername
     *
     * @return Gateways
     */
    public function setSmtpUsername($smtpUsername)
    {
        $this->smtp_username = $smtpUsername;

        return $this;
    }

    /**
     * Get smtpUsername
     *
     * @return string
     */
    public function getSmtpUsername()
    {
        return $this->smtp_username;
    }

    /**
     * Set smtpPassword
     *
     * @param string $smtpPassword
     *
     * @return Gateways
     */
    public function setSmtpPassword($smtpPassword)
    {
        $this->smtp_password = $smtpPassword;

        return $this;
    }

    /**
     * Get smtpPassword
     *
     * @return string
     */
    public function getSmtpPassword()
    {
        return $this->smtp_password;
    }

    /**
     * Set smtpPort
     *
     * @param string $smtpPort
     *
     * @return Gateways
     */
    public function setSmtpPort($smtpPort)
    {
        $this->smtp_port = $smtpPort;

        return $this;
    }

    /**
     * Get smtpPort
     *
     * @return string
     */
    public function getSmtpPort()
    {
        return $this->smtp_port;
    }

    /**
     * Set fromEmail
     *
     * @param string $fromEmail
     *
     * @return Gateways
     */
    public function setFromEmail($fromEmail)
    {
        $this->from_email = $fromEmail;

        return $this;
    }

    /**
     * Get fromEmail
     *
     * @return string
     */
    public function getFromEmail()
    {
        return $this->from_email;
    }

    /**
     * Set fromName
     *
     * @param string $fromName
     *
     * @return Gateways
     */
    public function setFromName($fromName)
    {
        $this->from_name = $fromName;

        return $this;
    }

    /**
     * Get fromName
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * Set sendmailCommand
     *
     * @param string $sendmailCommand
     *
     * @return Gateways
     */
    public function setSendmailCommand($sendmailCommand)
    {
        $this->sendmail_command = $sendmailCommand;

        return $this;
    }

    /**
     * Get sendmailCommand
     *
     * @return string
     */
    public function getSendmailCommand()
    {
        return $this->sendmail_command;
    }

    /**
     * Set autoReplyTo
     *
     * @param string $autoReplyTo
     *
     * @return Gateways
     */
    public function setAutoReplyTo($autoReplyTo)
    {
        $this->auto_reply_to = $autoReplyTo;

        return $this;
    }

    /**
     * Get autoReplyTo
     *
     * @return string
     */
    public function getAutoReplyTo()
    {
        return $this->auto_reply_to;
    }

    /**
     * Set mailDebuger
     *
     * @param integer $mailDebuger
     *
     * @return Gateways
     */
    public function setMailDebuger($mailDebuger)
    {
        $this->mail_debuger = $mailDebuger;

        return $this;
    }

    /**
     * Get mailDebuger
     *
     * @return integer
     */
    public function getMailDebuger()
    {
        return $this->mail_debuger;
    }

    /**
     * Set debugExit
     *
     * @param integer $debugExit
     *
     * @return Gateways
     */
    public function setDebugExit($debugExit)
    {
        $this->debug_exit = $debugExit;

        return $this;
    }

    /**
     * Get debugExit
     *
     * @return integer
     */
    public function getDebugExit()
    {
        return $this->debug_exit;
    }

    /**
     * Set useTemplate
     *
     * @param integer $useTemplate
     *
     * @return Gateways
     */
    public function setUseTemplate($useTemplate)
    {
        $this->use_template = $useTemplate;

        return $this;
    }

    /**
     * Get useTemplate
     *
     * @return integer
     */
    public function getUseTemplate()
    {
        return $this->use_template;
    }

    /**
     * Set sqlLimit
     *
     * @param integer $sqlLimit
     *
     * @return Gateways
     */
    public function setSqlLimit($sqlLimit)
    {
        $this->sql_limit = $sqlLimit;

        return $this;
    }

    /**
     * Get sqlLimit
     *
     * @return integer
     */
    public function getSqlLimit()
    {
        return $this->sql_limit;
    }

    /**
     * Set antiFlood
     *
     * @param integer $antiFlood
     *
     * @return Gateways
     */
    public function setAntiFlood($antiFlood)
    {
        $this->anti_flood = $antiFlood;

        return $this;
    }

    /**
     * Get antiFlood
     *
     * @return integer
     */
    public function getAntiFlood()
    {
        return $this->anti_flood;
    }

    /**
     * Set throttler
     *
     * @param integer $throttler
     *
     * @return Gateways
     */
    public function setThrottler($throttler)
    {
        $this->throttler = $throttler;

        return $this;
    }

    /**
     * Get throttler
     *
     * @return integer
     */
    public function getThrottler()
    {
        return $this->throttler;
    }

    /**
     * Set incoming
     *
     * @param integer $incoming
     *
     * @return Gateways
     */
    public function setIncoming($incoming)
    {
        $this->incoming = $incoming;

        return $this;
    }

    /**
     * Get incoming
     *
     * @return integer
     */
    public function getIncoming()
    {
        return $this->incoming;
    }

    /**
     * Set incomingPort
     *
     * @param string $incomingPort
     *
     * @return Gateways
     */
    public function setIncomingPort($incomingPort)
    {
        $this->incoming_port = $incomingPort;

        return $this;
    }

    /**
     * Get incomingPort
     *
     * @return string
     */
    public function getIncomingPort()
    {
        return $this->incoming_port;
    }

    /**
     * Set incomingUrl
     *
     * @param string $incomingUrl
     *
     * @return Gateways
     */
    public function setIncomingUrl($incomingUrl)
    {
        $this->incoming_url = $incomingUrl;

        return $this;
    }

    /**
     * Get incomingUrl
     *
     * @return string
     */
    public function getIncomingUrl()
    {
        return $this->incoming_url;
    }

    /**
     * Set incomingSslvalidate
     *
     * @param integer $incomingSslvalidate
     *
     * @return Gateways
     */
    public function setIncomingSslvalidate($incomingSslvalidate)
    {
        $this->incoming_sslvalidate = $incomingSslvalidate;

        return $this;
    }

    /**
     * Get incomingSslvalidate
     *
     * @return integer
     */
    public function getIncomingSslvalidate()
    {
        return $this->incoming_sslvalidate;
    }

    /**
     * Set isHtml
     *
     * @param integer $isHtml
     *
     * @return Gateways
     */
    public function setIsHtml($isHtml)
    {
        $this->is_html = $isHtml;

        return $this;
    }

    /**
     * Get isHtml
     *
     * @return integer
     */
    public function getIsHtml()
    {
        return $this->is_html;
    }

    /**
     * Set published
     *
     * @param integer $published
     *
     * @return Gateways
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

