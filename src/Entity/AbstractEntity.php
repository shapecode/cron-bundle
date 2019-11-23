<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractEntity implements AbstractEntityInterface
{
    /**
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * @inheritdoc
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId(?int $id = null) : void
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(DateTime $createdAt) : void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt() : DateTime
    {
        if (empty($this->createdAt)) {
            $this->setCreatedAt(new DateTime());
        }

        return $this->createdAt;
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(DateTime $updatedAt) : void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt() : DateTime
    {
        if (empty($this->updatedAt)) {
            $this->setUpdatedAt(new DateTime());
        }

        return $this->updatedAt;
    }
}
