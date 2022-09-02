<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\ConfigCommand;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends Command
{
    private const DESCRIPTION = 'Remove a configuration file or files.';

    private const HELP = <<<'EOH'
Allows you to remove one or both of the global and local configuration files
($XDG_CONFIG_HOME/keep-a-changelog.ini and ./.keep-a-changelog.ini,
respectively). The command will prompt for confirmation before removing any
files.
EOH;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher, ?string $name = null)
    {
        $this->dispatcher = $dispatcher;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription(self::DESCRIPTION);
        $this->setHelp(self::HELP);
        $this->addOption(
            'global',
            'g',
            InputOption::VALUE_NONE,
            'Edit the global configuration file ($XDG_CONFIG_HOME/keep-a-changelog.ini)'
        );
        $this->addOption(
            'local',
            'l',
            InputOption::VALUE_NONE,
            'Edit the local configuration file (./.keep-a-changelog.ini)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->dispatcher
                ->dispatch(new RemoveConfigEvent(
                    $input,
                    $output,
                    $input->getOption('local') ?: false,
                    $input->getOption('global') ?: false
                ))
                ->failed()
                    ? 1
                    : 0;
    }
}
