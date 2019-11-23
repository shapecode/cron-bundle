<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

class CronStyle extends SymfonyStyle
{
    public function info(string $message) : void
    {
        $this->block($message, 'Info', 'fg=white;bg=blue', ' ', true);
    }

    public function notice(string $message) : void
    {
        $this->block($message, 'Note', 'fg=black;bg=yellow', ' ', true);
    }
}
