<?php

namespace Jabe\Impl\JobExecutor;

use Jabe\Impl\Cfg\TransactionListenerInterface;
use Jabe\Impl\Interceptor\{
    CommandContext,
    CommandExecutorInterface
};

class RepeatingFailedJobListener implements TransactionListenerInterface
{
    protected $commandExecutor;
    protected $jobId;

    public function __construct(CommandExecutorInterface $commandExecutor, ?string $jobId)
    {
        $this->commandExecutor = $commandExecutor;
        $this->jobId = $jobId;
    }

    public function execute(CommandContext $commandContext, ...$args)
    {
        $cmd = new CreateNewTimerJobCommand($this->jobId);
        $this->commandExecutor->execute($cmd);
    }
}
