<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Provider\Exception;

use InvalidArgumentException;
use Phly\KeepAChangelog\Exception\ExceptionInterface;
use Phly\KeepAChangelog\Provider\ProviderInterface;

use function gettype;
use function sprintf;

class InvalidUrlException extends InvalidArgumentException implements ExceptionInterface
{
    public static function forUrl(string $url, ProviderInterface $provider): self
    {
        return new self(sprintf(
            'The URL "%s" is invalid and cannot be used with provider of type %s',
            $url,
            gettype($provider)
        ));
    }
}
