<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

final class CronStyle extends SymfonyStyle
{
    /** @phpstan-param string|array<mixed> $message */
    public function info($message): void
    {
        $this->block($message, 'Info', 'fg=white;bg=blue', ' ', true);
    }

    /** @param string|list<string> $message */
    public function notice(string|array $message): void
    {
        $this->block($message, 'Note', 'fg=black;bg=yellow', ' ', true);
    }
}
