<?php

namespace Shapecode\Bundle\CronBundle\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class CronStyle
 *
 * @package Shapecode\Bundle\CronBundle\Console\Style
 * @author  Nikita Loges
 */
class CronStyle extends SymfonyStyle
{

    /**
     * @param $message
     */
    public function info($message): void
    {
        $this->block($message, 'Info', 'fg=white;bg=blue', ' ', true);
    }

    /**
     * @param $message
     */
    public function notice($message): void
    {
        $this->block($message, 'Note', 'fg=black;bg=yellow', ' ', true);
    }
}
