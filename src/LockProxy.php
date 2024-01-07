<?php
declare(strict_types=1);

namespace SampleCron;

class LockProxy implements LockInterface
{
    /** @var \Symfony\Component\Lock\LockInterface */
    private \Symfony\Component\Lock\LockInterface $lock;

    public function __construct(\Symfony\Component\Lock\LockInterface $lock)
    {
        $this->lock = $lock;
    }

    public function acquire(): bool
    {
        return $this->lock->acquire(true);
    }

    public function release(): void
    {
        $this->lock->release();
    }
}
