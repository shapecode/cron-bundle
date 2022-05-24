<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

final class CronJobController
{
    public function __construct(
        private readonly KernelInterface $kernel
    ) {
    }

    public function __invoke(): Response
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input  = new StringInput('shapecode:cron:run');
        $output = new BufferedOutput();

        $application->run($input, $output);

        return new Response($output->fetch());
    }
}
