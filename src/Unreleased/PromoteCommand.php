<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Unreleased;

use Phly\KeepAChangelog\Common\CreateMilestoneOptionsTrait;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function date;

class PromoteCommand extends Command
{
    use CreateMilestoneOptionsTrait;

    private const DESCRIPTION = 'Give a name to an unreleased version.';

    private const HELP = <<<'EOH'
Renames the current Unreleased version to the <version> provided, and sets the
release date to today (unless the --date|-d option is provided).

If --create-milestone or --create-milestone-with-name are provided, a milestone
will be created for the repository as well.

EOH;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, string $name = 'unreleased:promote')
    {
        $this->dispatcher = $dispatcher;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(self::DESCRIPTION);
        $this->setHelp(self::HELP);

        $this->addArgument(
            'version',
            InputArgument::REQUIRED,
            'The version to promote the unreleased version to.'
        );

        $this->addOption(
            'date',
            'd',
            InputOption::VALUE_REQUIRED,
            'Specific release date to use',
            date('Y-m-d')
        );

        $this->injectMilestoneOptions($this);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $version = $input->getArgument('version');
        $event   = $this->dispatcher
            ->dispatch(new PromoteEvent(
                $input,
                $output,
                $this->dispatcher,
                $version,
                $input->getOption('date')
            ));

        if ($event->failed()) {
            return 1;
        }

        if (! $this->isMilestoneCreationRequested($input)) {
            return 0;
        }

        return $this
            ->triggerCreateMilestoneEvent(
                $this->getMilestoneName($input, $version),
                $output,
                $this->dispatcher
            )
            ->failed()
                ? 1
                : 0;
    }
}
