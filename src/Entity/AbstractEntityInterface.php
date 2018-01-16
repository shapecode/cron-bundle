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
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * @param integer|null $id
     */
    public function setId($id = null);

    /**
     * Set created
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updated
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
