<?php
declare(strict_types=1);

namespace SampleCron;

interface LockInterface
{
    public function acquire(): bool;
    public function release(): void;
}
