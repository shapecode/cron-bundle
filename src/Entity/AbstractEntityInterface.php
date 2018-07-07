<?php

namespace Shapecode\Bundle\CronBundle\Entity;

/**
 * Class AbstractEntityInterface
 *
 * @package Shapecode\Bundle\CronBundle\Entity
 * @author  Nikita Loges
 */
interface AbstractEntityInterface
{

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param integer|null $id
     */
    public function setId(?int $id = null): void;

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void;

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime;
}
