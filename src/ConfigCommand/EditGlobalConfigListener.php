<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\ConfigCommand;

use Phly\KeepAChangelog\Config\LocateGlobalConfigTrait;

use function sprintf;

class EditGlobalConfigListener extends AbstractEditConfigListener
{
    use LocateGlobalConfigTrait;

    public function configEditRequested(EditConfigEvent $event): bool
    {
        return $event->editGlobal();
    }

    public function getConfigFile(): string
    {
        return sprintf('%s/keep-a-changelog.ini', $this->getConfigRoot());
    }
}
