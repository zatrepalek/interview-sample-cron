<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Sample mutex from Symfony
$store = new \Symfony\Component\Lock\Store\FlockStore('/tmp/SampleCron');
$factory = new \Symfony\Component\Lock\LockFactory($store);
$lock = $factory->createLock('cron');

// mutex proxy
// currently single thread execution
// possible updates - proxy can utilize LockFactory and create different locks e.g. using command flag/name/command group
// ... e.g. these commands can run in parallel; this one has to run exclusively...
$mutex = new \SampleCron\LockProxy($lock);

$schedule = new \SampleCron\OutputScheduleProxy($mutex);

// Laravel
// Define as service in Laravel (\Illuminate\Console\Scheduling\Schedule constructor requires Illuminate\Container\Container anyway)
// $schedule = new \SampleCron\LaravelScheduleProxy(...);

// crunz
// Reuse Symfony Command from crunz ($this->getApplication->find('schedule:run'))
// $schedule = new \SampleCron\CrunzScheduleProxy(...);

$schedule->scheduleJobs([
    ['command' => 'date', 'crontab' => '* * * * *'],
    ['command' => 'echo "Test"', 'crontab' => '30 12 * 5-6,9 Mon,Fri'],
]);
$schedule->runScheduledJobs();

