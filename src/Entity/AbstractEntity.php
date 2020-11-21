<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractEntity
{
    /**
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var string|int|null
     */
    protected $id;

    /** @ORM\Column(type="datetime") */
    protected DateTimeInterface $createdAt;

    /** @ORM\Column(type="datetime") */
    protected DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        if ($this->id !== null) {
            return (int) $this->id;
        }

        return $this->id;
    }

    public function setId(?int $id = null): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }
}
