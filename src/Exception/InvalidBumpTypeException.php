<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Exception;

use InvalidArgumentException;
use Phly\KeepAChangelog\BumpCommand;

use function sprintf;

class InvalidBumpTypeException extends InvalidArgumentException implements ExceptionInterface
{
    public static function forType(string $type): self
    {
        return new self(sprintf(
            'Invalid bump type "%1$s"; must be one of %2$s::BUMP_BUGFIX, %2$s::BUMP_MINOR, or %2$s::BUMP_MAJOR',
            $type,
            BumpCommand::class
        ));
    }
}
