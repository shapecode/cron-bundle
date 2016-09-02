<?php

namespace Shapecode\Bundle\CronBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEntity
 *
 * @package Shapecode\Bundle\CronBundle\Entity
 * @author  Nikita Loges
 */
abstract class AbstractEntity
{

    /**
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId($id = null)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        if (empty($this->createdAt)) {
            $this->setCreatedAt(new \DateTime());
        }

        return $this->createdAt;
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        if (empty($this->updatedAt)) {
            $this->setUpdatedAt(new \DateTime());
        }

        return $this->updatedAt;
    }

    /**
     * @inheritdoc
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setCreatedAtAndUpdatedAtValue()
    {
        if (empty($this->updatedAt)) {
            $this->setUpdatedAt(new \DateTime());
        }
        if (empty($this->createdAt)) {
            $this->setCreatedAt(new \DateTime());
        }
    }
}
