<?php

declare(strict_types=1);

namespace Shapecode\Bundle\CronBundle\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

final class CronStyle extends SymfonyStyle
{
    /** @param string|array<mixed> $message */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function info($message): void
    {
        $this->block($message, 'Info', 'fg=white;bg=blue', ' ', true);
    }

    /** @param string|list<string> $message */
    public function notice($message): void
    {
        $this->block($message, 'Note', 'fg=black;bg=yellow', ' ', true);
    }
}
