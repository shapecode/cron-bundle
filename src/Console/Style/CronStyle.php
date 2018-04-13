<?php

namespace Shapecode\Bundle\CronBundle\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CronStyle
 *
 * @package Shapecode\Bundle\CronBundle\Console\Style
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class CronStyle extends SymfonyStyle
{

    /**
     * @param $message
     */
    public function info($message)
    {
        $this->block($message, 'Info', 'fg=white;bg=blue', ' ', true);
    }

    /**
     * @param $message
     */
    public function notice($message)
    {
        $this->block($message, 'Note', 'fg=black;bg=yellow', ' ', true);
    }
}
