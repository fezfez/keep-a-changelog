<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Exception;

use InvalidArgumentException;
use Phly\KeepAChangelog\Provider;

use function gettype;
use function implode;
use function sprintf;

class InvalidProviderException extends InvalidArgumentException implements ExceptionInterface
{
    public static function forProvider(string $provider, array $allowedProviders): self
    {
        return new self(sprintf(
            'Unknown provider "%s"; must be one of (%s)',
            $provider,
            implode(', ', $allowedProviders)
        ));
    }

    public static function forIncompleteProvider(Provider\ProviderInterface $provider): self
    {
        return new self(sprintf(
            'Provider %s does not implement %s and thus cannot be used to determine where to push tags;'
            . ' please implement %s',
            gettype($provider),
            Provider\ProviderNameProviderInterface::class,
            Provider\ProviderNameProviderInterface::class
        ));
    }

    public static function forInvalidProviderDomain(string $domain): self
    {
        return new self(sprintf(
            'Domain "%s" is invalid',
            $domain
        ));
    }
}
