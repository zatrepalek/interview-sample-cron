<?php
declare(strict_types=1);

namespace SampleCron;

class OutputScheduleProxy implements ScheduleInterface
{
    private array $commands = [];
    private LockProxy $lock;

    public function __construct(LockProxy $lock)
    {
        $this->lock = $lock;
    }

    /**
     * Array of items: ['command' => 'foo', 'crontab' => '* * * * *']
     * @param array $commands
     * @return void
     */
    public function scheduleJobs(array $commands): void
    {
        $this->commands = $commands;
    }

    public function runScheduledJobs(): void
    {
        if ($this->lock->acquire()) {
            try {
                foreach ($this->commands as $command) {
                    echo $command['crontab'] . " " . $command['command'] . PHP_EOL;
                }
            } catch (\Throwable $e) {
                // TODO: log
            } finally {
                $this->lock->release();
            }
        }
    }
}
