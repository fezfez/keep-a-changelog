<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Version;

use Phly\KeepAChangelog\Common\ChangelogParser;
use Phly\KeepAChangelog\Exception\ChangelogMissingDateException;

use function file_get_contents;

class VerifyVersionHasReleaseDateListener
{
    public function __invoke(TagReleaseEvent $event): void
    {
        $parser = new ChangelogParser();

        try {
            $parser->findReleaseDateForVersion(
                file_get_contents($event->config()->changelogFile()),
                $event->version(),
                true
            );
        } catch (ChangelogMissingDateException $e) {
            $event->changelogMissingDate();
        }
    }
}
