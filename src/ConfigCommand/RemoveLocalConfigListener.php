<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\ConfigCommand;

use function getcwd;
use function sprintf;

class RemoveLocalConfigListener extends AbstractRemoveConfigListener
{
    public function configRemovalRequested(RemoveConfigEvent $event): bool
    {
        return $event->removeLocal();
    }

    public function getConfigFile(): string
    {
        return sprintf('%s/.keep-a-changelog.ini', $this->configRoot ?: getcwd());
    }

    /**
     * Set a specific directory in which to look for the local config file.
     *
     * For testing purposes only.
     *
     * @internal
     *
     * @var null|string
     */
    public $configRoot;
}
