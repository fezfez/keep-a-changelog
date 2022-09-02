<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Common;

use function file_exists;
use function file_put_contents;
use function is_string;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

/**
 * Mix this in to classes that call Editor::spawnEditor to allow unit testing.
 *
 * This trait accomplishes three things:
 *
 * - Managing how temporary files are created.
 * - Managing how to remove temporary files.
 * - Allowing mocking of Editor operations.
 *
 * During unit testing, classes that use this trait should:
 *
 * - Provide a value for the $mockTempFile property. This should generally
 *   resolve to a real file. The file can be permanent; unlinkTempFile() WILL
 *   NOT remove any file specified in the property.
 * - Provide a mock Editor instance to the $editor property. This allows
 *   alternating status return values during testing, without having
 *   side-effects.
 */
trait EditSpawnerTrait
{
    protected function getEditor(): Editor
    {
        if ($this->editor instanceof Editor) {
            return $this->editor;
        }

        return new Editor();
    }

    /**
     * Creates a temporary file with the changelog contents.
     */
    private function createTempFileWithContents(string $contents): string
    {
        if (is_string($this->mockTempFile)) {
            return $this->mockTempFile;
        }

        $filename = sprintf('%s.md', uniqid('KAC', true));
        $path     = sprintf('%s/%s', sys_get_temp_dir(), $filename);

        file_put_contents($path, $contents);

        return $path;
    }

    /**
     * Removes the temp file generated by createTempFileWithContents
     */
    private function unlinkTempFile(string $filename): void
    {
        if (is_string($this->mockTempFile) || ! file_exists($filename)) {
            return;
        }

        unlink($filename);
    }

    /**
     * Provide an Editor instance to use.
     *
     * For testing purposes only.
     *
     * @internal
     *
     * @var null|Editor
     */
    public $editor;

    /**
     * Provide a mock temporary filename.
     *
     * For testing purposes only.
     *
     * @internal
     *
     * @var null|string
     */
    public $mockTempFile;
}
