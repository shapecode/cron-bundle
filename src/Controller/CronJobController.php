<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Controller;

use Shapecode\Bundle\CronBundle\Command\CronRunCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

final readonly class CronJobController
{
    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    public function __invoke(): Response
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input  = new StringInput(CronRunCommand::NAME);
        $output = new BufferedOutput();

        $application->run($input, $output);

        return new Response($output->fetch());
    }
}
