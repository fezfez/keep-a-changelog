<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\ConfigCommand;

use Phly\KeepAChangelog\Config\LocateGlobalConfigTrait;

use function sprintf;

class CreateGlobalConfigListener extends AbstractCreateConfigListener
{
    use LocateGlobalConfigTrait;

    private const TEMPLATE = <<<'EOT'
[defaults]
changelog_file = %s
provider = github
remote = origin

[providers]
github[class] = Phly\KeepAChangelog\Provider\GitHub
github[token] = token-should-be-provided-here
gitlab[class] = Phly\KeepAChangelog\Provider\GitLab
gitlab[token] = token-should-be-provided-here

EOT;

    public function configCreateRequested(CreateConfigEvent $event): bool
    {
        return $event->createGlobal();
    }

    public function getConfigFileName(): string
    {
        return sprintf('%s/keep-a-changelog.ini', $this->getConfigRoot());
    }

    public function getConfigTemplate(): string
    {
        return self::TEMPLATE;
    }
}
