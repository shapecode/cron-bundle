<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use DateTime;

interface AbstractEntityInterface
{
    public function getId(): ?int;

    public function setId(?int $id = null): void;

    public function setCreatedAt(DateTime $createdAt): void;

    public function getCreatedAt(): DateTime;

    public function setUpdatedAt(DateTime $updatedAt): void;

    public function getUpdatedAt(): DateTime;
}
