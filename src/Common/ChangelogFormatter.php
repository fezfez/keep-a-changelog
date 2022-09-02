<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Common;

use function preg_replace_callback;
use function sprintf;
use function str_repeat;
use function strlen;

class ChangelogFormatter
{
    public function format(string $changelog): string
    {
        return preg_replace_callback(
            '/^\#\#\# (?<heading>Added|Changed|Deprecated|Removed|Fixed)/m',
            static function (array $matches) {
                return sprintf(
                    "%s\n%s",
                    $matches['heading'],
                    str_repeat('-', strlen($matches['heading']))
                );
            },
            $changelog
        );
    }
}
