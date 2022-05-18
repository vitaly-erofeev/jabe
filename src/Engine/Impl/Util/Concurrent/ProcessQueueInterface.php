<?php

namespace Jabe\Engine\Impl\Util\Concurrent;

interface ProcessQueueInterface
{
    public function poll(int $timeout, string $unit, InterruptibleProcess $process);

    public function take(InterruptibleProcess $process);

    public function drainTo(&$c, int $maxElements = null): int;
}
