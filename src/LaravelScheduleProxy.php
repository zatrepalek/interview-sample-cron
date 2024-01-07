<?php
declare(strict_types=1);

namespace SampleCron;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;

class LaravelScheduleProxy implements ScheduleInterface
{
    private Schedule $schedule;
    private LockProxy $lock;
    private ScheduleRunCommand $command;
    private Dispatcher $dispatcher;
    private Cache $cache;
    private ExceptionHandler $handler;

    public function __construct(
        Schedule $schedule,
        LockProxy $lock,
        ScheduleRunCommand $command,
        Dispatcher $dispatcher,
        Cache $cache,
        ExceptionHandler $handler
    )
    {
        $this->schedule = $schedule;
        $this->lock = $lock;
        $this->command = $command;
        $this->dispatcher = $dispatcher;
        $this->cache = $cache;
        $this->handler = $handler;
    }

    /**
     * Array of items: ['command' => 'foo', 'crontab' => '* * * * *']
     * @param array $commands
     * @return void
     */
    public function scheduleJobs(array $commands): void
    {
        foreach ($commands as $command) {
            $this->schedule->exec($command['command'])->cron($command['crontab']);
        }
    }

    public function runScheduledJobs(): void
    {
        if ($this->lock->acquire()) {
            try {
                $this->command->handle(
                    $this->schedule,
                    $this->dispatcher,
                    $this->cache,
                    $this->handler
                );
            } catch (\Throwable $e) {
                // TODO: log
            } finally {
                $this->lock->release();
            }
        }
    }
}
