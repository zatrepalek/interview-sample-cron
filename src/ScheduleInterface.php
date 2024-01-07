<?php
declare(strict_types=1);

namespace SampleCron;

interface ScheduleInterface
{
    /**
     * Array of items: ['command' => 'foo', 'crontab' => '* * * * *']
     * @param array $commands
     * @return void
     */
    public function scheduleJobs(array $commands): void;
    public function runScheduledJobs(): void;
}
