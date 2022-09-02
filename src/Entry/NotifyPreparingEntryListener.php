<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Entry;

use function sprintf;
use function ucwords;

class NotifyPreparingEntryListener
{
    public function __invoke(AddChangelogEntryEvent $event): void
    {
        $event->output()->writeln(sprintf(
            '<info>Preparing entry for %s section</info>',
            ucwords($event->entryType())
        ));
    }
}
