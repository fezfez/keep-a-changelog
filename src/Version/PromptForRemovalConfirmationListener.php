<?php

/**
 * @see       https://github.com/phly/keep-a-changelog for the canonical source repository
 */

declare(strict_types=1);

namespace Phly\KeepAChangelog\Version;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class PromptForRemovalConfirmationListener
{
    public function __invoke(RemoveChangelogVersionEvent $event): void
    {
        $input = $event->input();
        if ($input->hasOption('force-removal') && $input->getOption('force-removal')) {
            // No need to prompt
            return;
        }

        $entry  = $event->changelogEntry();
        $output = $event->output();

        $output->writeln('<info>Found the following entry:</info>');
        $output->writeln($entry->contents);

        $question = new ConfirmationQuestion('Do you really want to delete this version ([y]es/[n]o)? ', false);

        if (! $this->getQuestionHelper()->ask($input, $output, $question)) {
            $event->abort();
            return;
        }
    }

    private function getQuestionHelper(): QuestionHelper
    {
        if ($this->questionHelper instanceof QuestionHelper) {
            return $this->questionHelper;
        }
        return new QuestionHelper();
    }

    /**
     * Provide an alternative question helper for use in prompting.
     *
     * For testing purposes only.
     *
     * @internal
     *
     * @var null|QuestionHelper
     */
    public $questionHelper;
}
