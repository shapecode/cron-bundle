<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;

use function file_exists;

final class CommandHelper
{
    private ?string $phpExecutable = null;

    private ?string $consoleBin = null;

    private ?float $timeout;

    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel, ?float $timeout = null)
    {
        $this->timeout = $timeout;
        $this->kernel  = $kernel;
    }

    public function getConsoleBin(): string
    {
        if ($this->consoleBin !== null) {
            return $this->consoleBin;
        }

        $projectDir = $this->kernel->getProjectDir();

        $consolePath = $projectDir . '/bin/console';

        if (! file_exists($consolePath)) {
            throw new RuntimeException('Missing console binary');
        }

        $consoleBin = $consolePath;

        $this->consoleBin = $consoleBin;

        return $consoleBin;
    }

    public function getPhpExecutable(): string
    {
        if ($this->phpExecutable !== null) {
            return $this->phpExecutable;
        }

        $executableFinder = new PhpExecutableFinder();
        $php              = $executableFinder->find();

        if ($php === false) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }

        $this->phpExecutable = $php;

        return $php;
    }

    public function getTimeout(): ?float
    {
        return $this->timeout;
    }
}
