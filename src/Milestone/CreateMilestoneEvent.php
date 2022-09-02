<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Milestone;

use Phly\KeepAChangelog\Provider\Milestone;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function sprintf;

class CreateMilestoneEvent extends AbstractMilestoneProviderEvent
{
    /** @var string */
    private $description;

    /** @var string */
    private $title;

    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        EventDispatcherInterface $dispatcher
    ) {
        $this->input       = $input;
        $this->output      = $output;
        $this->dispatcher  = $dispatcher;
        $this->title       = $input->getArgument('title');
        $this->description = $input->getArgument('description') ?? '';
    }

    public function isPropagationStopped(): bool
    {
        return $this->failed;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function milestoneCreated(Milestone $milestone): void
    {
        $this->output()->writeln(sprintf(
            '<info>Created milestone (%d) %s: %s</info>',
            $milestone->id(),
            $milestone->title(),
            $milestone->description() ?: '(no description)'
        ));
    }

    public function errorCreatingMilestone(Throwable $e): void
    {
        $this->failed = true;

        if ((int) $e->getCode() === 401) {
            $this->reportAuthenticationException($e);
            return;
        }

        $this->reportStandardException($e);
    }

    private function reportStandardException(Throwable $e): void
    {
        $output = $this->output();

        $output->writeln('<error>Error creating milestone!</error>');
        $output->writeln('An error occurred when attempting to create the milestone:');
        $output->writeln('');
        $output->writeln('Error Message: ' . $e->getMessage());
    }

    private function reportAuthenticationException(Throwable $e): void
    {
        $output = $this->output();

        $output->writeln('<error>Invalid credentials</error>');
        $output->writeln('The credentials associated with your Git provider are invalid.');
    }
}
