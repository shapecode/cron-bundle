<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Shapecode\Bundle\CronBundle\Repository\CronJobResultRepository;

use function sprintf;

#[ORM\Entity(repositoryClass: CronJobResultRepository::class)]
class CronJobResult extends AbstractEntity
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $runAt;

    #[ORM\Column(type: Types::FLOAT)]
    private float $runTime;

    #[ORM\Column(type: Types::INTEGER)]
    private int $statusCode;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $output;

    #[ORM\ManyToOne(targetEntity: CronJob::class, cascade: ['persist'], inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CronJob $cronJob;

    public function __construct(
        CronJob $cronJob,
        float $runTime,
        int $statusCode,
        ?string $output,
        DateTimeInterface $runAt,
    ) {
        $this->runTime    = $runTime;
        $this->statusCode = $statusCode;
        $this->output     = $output;
        $this->cronJob    = $cronJob;
        $this->runAt      = $runAt;
    }

    public function getRunAt(): DateTimeInterface
    {
        return $this->runAt;
    }

    public function getRunTime(): float
    {
        return $this->runTime;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function getCronJob(): CronJob
    {
        return $this->cronJob;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s - %s',
            $this->getCronJob()->getCommand(),
            $this->getRunAt()->format('d.m.Y H:i P')
        );
    }
}
