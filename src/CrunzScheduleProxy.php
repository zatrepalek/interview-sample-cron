<?php
declare(strict_types=1);

namespace SampleCron;


use Crunz\Schedule;
use Crunz\Task\TaskException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrunzScheduleProxy implements ScheduleInterface
{
    private LockProxy $lock;
    private Command $command;
    private Schedule $schedule;
    private InputInterface $input;
    private OutputInterface $output;

    public function __construct(
        Schedule $schedule,
        LockProxy $lock,
        Command $command,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->schedule = $schedule;
        $this->lock = $lock;
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Array of items: ['command' => 'foo', 'crontab' => '* * * * *']
     * @param array $commands
     * @return void
     * @throws TaskException
     */
    public function scheduleJobs(array $commands): void
    {
        foreach ($commands as $command) {
            $this->schedule->run($command['command'])->cron($command['crontab']);
        }
    }

    public function runScheduledJobs(): void
    {
        if ($this->lock->acquire()) {
            try {
                $this->command->run($this->input, $this->output);
            } catch (\Throwable $e) {
                // TODO: log
            } finally {
                $this->lock->release();
            }
        }
    }
}
