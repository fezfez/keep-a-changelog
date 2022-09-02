<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace PhlyTest\KeepAChangelog\Common;

use Phly\KeepAChangelog\Common\ChangelogEntry;
use Phly\KeepAChangelog\Common\ChangelogParser;
use Phly\KeepAChangelog\Exception;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function is_string;
use function iterator_to_array;

class ChangelogParserTest extends TestCase
{
    protected function setUp(): void
    {
        $this->changelog = file_get_contents(__DIR__ . '/../_files/CHANGELOG.md');
        $this->parser    = new ChangelogParser();
    }

    public function testRaisesExceptionIfNoMatchingEntryForVersionFound()
    {
        $this->expectException(Exception\ChangelogNotFoundException::class);
        $this->parser->findChangelogForVersion($this->changelog, '3.0.0');
    }

    public function testRaisesExceptionIfMatchingEntryFoundButInvalidDateFormatSet()
    {
        $changelogWithInvalidReleaseDate = file_get_contents(__DIR__ . '/../_files/CHANGELOG-INVALID-DATE.md');
        $this->expectException(Exception\ChangelogMissingDateException::class);
        $this->parser->findChangelogForVersion($changelogWithInvalidReleaseDate, '1.1.0');
    }

    public function testRaisesExceptionIfUnableToIsolateChangelog()
    {
        $this->expectException(Exception\InvalidChangelogFormatException::class);
        $this->parser->findChangelogForVersion(file_get_contents(__DIR__ . '/../_files/CHANGELOG-INVALID.md'), '0.1.0');
    }

    public function testReturnsDiscoveredChangelogWhenDiscovered()
    {
        $expected  = <<<'EOF'
### Added

- Added a new feature.

### Changed

- Made some changes.

### Deprecated

- Nothing was deprecated.

### Removed

- Nothing was removed.

### Fixed

- Fixed some bugs.

EOF;
        $changelog = $this->parser->findChangelogForVersion($this->changelog, '1.1.0');

        $this->assertEquals($expected, $changelog);
    }

    public function testReturnsDiscoveredChangelogForUnreleasedVersionWhenDiscovered()
    {
        $expected  = <<<'EOF'
### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

EOF;
        $changelog = $this->parser->findChangelogForVersion($this->changelog, '2.0.0');

        $this->assertEquals($expected, $changelog);
    }

    public function testRecognizedSingleVersionChangelog()
    {
        $changelog = $this->parser->findChangelogForVersion(
            file_get_contents(__DIR__ . '/../_files/CHANGELOG-SINGLE-VERSION.md'),
            '0.1.0'
        );

        $this->assertTrue(is_string($changelog));
    }

    public function testRetrievingDateRaisesExceptionIfNoMatchingEntryForVersionFound()
    {
        $this->expectException(Exception\ChangelogNotFoundException::class);
        $this->parser->findReleaseDateForVersion($this->changelog, '3.0.0');
    }

    public function testRetrievingDateRaisesExceptionIfMatchingEntryFoundButInvalidDateFormatPresent()
    {
        $changelogWithInvalidReleaseDate = file_get_contents(__DIR__ . '/../_files/CHANGELOG-INVALID-DATE.md');
        $this->expectException(Exception\ChangelogMissingDateException::class);
        $this->parser->findReleaseDateForVersion($changelogWithInvalidReleaseDate, '1.1.0');
    }

    public function testCanRetrieveDateForReleasedVersions()
    {
        $date = $this->parser->findReleaseDateForVersion($this->changelog, '1.1.0');
        $this->assertSame('2018-03-23', $date);
    }

    public function testCanRetrieveDateForUnreleasedVersion()
    {
        $date = $this->parser->findReleaseDateForVersion($this->changelog, '2.0.0');
        $this->assertSame('TBD', $date);
    }

    public function testCanRetrieveInformationOnAllVersions()
    {
        $expected = [
            '2.0.0' => 'TBD',
            '1.1.0' => '2018-03-23',
            '0.1.0' => '2018-03-23',
        ];

        $actual = iterator_to_array($this->parser->findAllVersions(__DIR__ . '/../_files/CHANGELOG.md'));

        $this->assertSame($expected, $actual);
    }

    public function testListingAllVersionsIncludesLinkedVersions()
    {
        $expected = [
            '2.0.0' => 'TBD',
            '1.1.0' => '2019-06-05',
            '0.1.0' => '2018-03-23',
        ];

        $actual = iterator_to_array($this->parser->findAllVersions(__DIR__ . '/../_files/CHANGELOG-WITH-LINKS.md'));

        $this->assertSame($expected, $actual);
    }

    public function expectedDataForLinkedVersions(): iterable
    {
        yield '2.0.0' => ['2.0.0', 'TBD'];
        yield '1.1.0' => ['1.1.0', '2019-06-05'];
        yield '0.1.0' => ['0.1.0', '2018-03-23'];
    }

    /**
     * @dataProvider expectedDataForLinkedVersions
     */
    public function testCanRetrieveDateForLinkedVersions(string $version, string $expectedDate)
    {
        $changelog = file_get_contents(__DIR__ . '/../_files/CHANGELOG-WITH-LINKS.md');

        $actual = $this->parser->findReleaseDateForVersion($changelog, $version);

        $this->assertSame($expectedDate, $actual);
    }

    public function expectedContentsForLinkedVersions(): iterable
    {
        $changelog = file_get_contents(__DIR__ . '/../_files/CHANGELOG-WITH-LINKS.md');

        $v2 = <<<'EOC'
### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

EOC;

        $v1 = <<<'EOC'
### Added

- Added a new feature.

### Changed

- Made some changes.

### Deprecated

- Nothing was deprecated.

### Removed

- Nothing was removed.

### Fixed

- Fixed some bugs.

EOC;

        $v0 = <<<'EOC'
### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

EOC;

        yield '2.0.0' => ['2.0.0', $v2];
        yield '1.1.0' => ['1.1.0', $v1];
        yield '0.1.0' => ['0.1.0', $v0];
    }

    /**
     * @dataProvider expectedContentsForLinkedVersions
     */
    public function testCanRetrieveChangelogForLinkedVersions(string $version, string $expectedContents)
    {
        $changelog = file_get_contents(__DIR__ . '/../_files/CHANGELOG-WITH-LINKS.md');

        $actual = $this->parser->findChangelogForVersion($changelog, $version);

        $this->assertSame($expectedContents, $actual);
    }

    public function testCanFetchLinks()
    {
        $expectedContents = <<<'EOC'
[2.0.0]: https://example.com/compare/1.1.0...develop
[1.1.0]: https://example.com/releases/1.1.0/
[0.1.0]: https://example.com/releases/0.1.0/

EOC;

        $links = $this->parser->findLinks(__DIR__ . '/../_files/CHANGELOG-WITH-LINKS.md');

        $this->assertInstanceOf(ChangelogEntry::class, $links);
        $this->assertSame($expectedContents, $links->contents);
        $this->assertSame(70, $links->index);
        $this->assertSame(3, $links->length);
    }

    public function testCorrectlyIdentifiesUnreleasedVersion(): void
    {
        $changelog = __DIR__ . '/../_files/CHANGELOG-WITH-UNRELEASED-SECTION.md';

        $expected = [
            'Unreleased' => '',
            '1.1.0'      => '2018-03-23',
            '0.1.0'      => '2018-03-23',
        ];

        $actual = iterator_to_array($this->parser->findAllVersions($changelog));

        $this->assertSame($expected, $actual);
    }

    public function unreleasedVariants(): iterable
    {
        yield 'unreleased' => ['unreleased'];
        yield 'UNRELEASED' => ['UNRELEASED'];
        yield 'Unreleased' => ['Unreleased'];
    }

    /**
     * @dataProvider unreleasedVariants
     */
    public function testCorrectlyReturnsUnreleasedVersion(string $versionName): void
    {
        $changelogContents = file_get_contents(__DIR__ . '/../_files/CHANGELOG-WITH-UNRELEASED-SECTION.md');
        $expected          = <<<'EOF'
### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

EOF;
        $changelog         = $this->parser->findChangelogForVersion($changelogContents, $versionName);

        $this->assertEquals($expected, $changelog);
    }
}
