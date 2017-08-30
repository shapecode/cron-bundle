<?php

namespace Shapecode\Bundle\CronBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CronJobController
 *
 * @package Shapecode\Bundle\CronBundle\Controller
 * @author  Nikita Loges
 */
class CronJobController extends Controller
{

    /**
     *
     */
    public function runAction()
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'shapecode:cron:run'
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        return new Response($content);
    }
}
