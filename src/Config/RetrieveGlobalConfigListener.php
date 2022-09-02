<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Config;

use function sprintf;

/**
 * Parses the global config to populate the Config instance.
 *
 * The global configuration file is an INI file with the following format:
 *
 * <code>
 * [defaults]
 * changelog_file = changelog.md
 * provider = custom
 * remote = upstream
 *
 * [providers]
 * github[class] = Phly\KeepAChangelog\Provider\GitHub
 * github[url] = https://github.mwop.net
 * github[token] = this-is-a-token
 * custom[class] = Mwop\Git\Provider
 * custom[url] = https://git.mwop.net
 * custom[token] = this-is-a-token
 * gitlab[class] = Phly\KeepAChangelog\Provider\GitHub
 * gitlab[token] = this-is-a-token
 * </code>
 */
class RetrieveGlobalConfigListener extends AbstractConfigListener
{
    use LocateGlobalConfigTrait;

    protected function getConfigFile(): string
    {
        return sprintf('%s/keep-a-changelog.ini', $this->getConfigRoot());
    }
}
