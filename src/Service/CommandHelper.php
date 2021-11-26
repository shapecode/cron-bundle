<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;

use function file_exists;
use function sprintf;

class CommandHelper
{
    private ?string $phpExecutable = null;

    private ?string $consoleBin = null;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly?float $timeout = null
    ) {
    }

    public function getConsoleBin(): string
    {
        if ($this->consoleBin === null) {
            $projectDir = $this->kernel->getProjectDir();

            $consolePath = sprintf('%s/bin/console', $projectDir);

            if (! file_exists($consolePath)) {
                throw new RuntimeException('Missing console binary');
            }

            $consoleBin = $consolePath;

            $this->consoleBin = $consoleBin;
        }

        return $this->consoleBin;
    }

    public function getPhpExecutable(): string
    {
        if ($this->phpExecutable === null) {
            $executableFinder = new PhpExecutableFinder();
            $php              = $executableFinder->find();

            if ($php === false) {
                throw new RuntimeException('Unable to find the PHP executable.');
            }

            $this->phpExecutable = $php;
        }

        return $this->phpExecutable;
    }

    public function getTimeout(): ?float
    {
        return $this->timeout;
    }
}
