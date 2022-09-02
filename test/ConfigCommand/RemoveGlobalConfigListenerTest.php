<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace PhlyTest\KeepAChangelog\ConfigCommand;

use Phly\KeepAChangelog\ConfigCommand\AbstractRemoveConfigListener;
use Phly\KeepAChangelog\ConfigCommand\RemoveGlobalConfigListener;
use Prophecy\Prophecy\ObjectProphecy;

use function file_exists;
use function sprintf;
use function sys_get_temp_dir;
use function touch;
use function unlink;

class RemoveGlobalConfigListenerTest extends AbstractRemoveConfigListenerTestCase
{
    /** @var null|string */
    private $tempFile;

    public function getListener(): AbstractRemoveConfigListener
    {
        $configRoot     = sys_get_temp_dir();
        $this->tempFile = sprintf('%s/keep-a-changelog.ini', $configRoot);
        touch($this->tempFile);

        $listener             = new RemoveGlobalConfigListener();
        $listener->configRoot = $configRoot;

        return $listener;
    }

    public function getListenerWithFileNotFound(): AbstractRemoveConfigListener
    {
        $configRoot     = sys_get_temp_dir();
        $this->tempFile = sprintf('%s/keep-a-changelog.ini', $configRoot);

        $listener             = new RemoveGlobalConfigListener();
        $listener->configRoot = $configRoot;

        return $listener;
    }

    public function getListenerWithUnlinkableFile(): AbstractRemoveConfigListener
    {
        $configRoot     = sys_get_temp_dir();
        $this->tempFile = sprintf('%s/keep-a-changelog.ini', $configRoot);
        touch($this->tempFile);

        $unlink = function (string $filename): bool {
            return false;
        };

        $listener             = new RemoveGlobalConfigListener();
        $listener->configRoot = $configRoot;
        $listener->unlink     = $unlink;

        return $listener;
    }

    public function configureEventToRemove(ObjectProphecy $event): void
    {
        $event->removeGlobal()->willReturn(true);
    }

    public function configureEventToSkipRemove(ObjectProphecy $event): void
    {
        $event->removeGlobal()->willReturn(false);
    }

    protected function setUp(): void
    {
        $this->tempFile = null;
        parent::setUp();
    }

    protected function tearDown(): void
    {
        if ($this->tempFile && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        $this->tempFile = null;
    }
}
