<?php

namespace Jabe\Impl\Cmd;

use Jabe\Impl\Core\Variable\Scope\AbstractVariableScope;
use Jabe\Impl\Interceptor\{
    CommandInterface,
    CommandContext
};
use Jabe\Impl\Persistence\Entity\ExecutionEntity;
use Jabe\Impl\Pvm\Runtime\{
    CallbackInterface,
    PvmExecutionImpl
};

abstract class AbstractVariableCmd implements CommandInterface
{
    protected $commandContext;
    protected $entityId;
    protected $isLocal;
    protected bool $preventLogUserOperation = false;

    public function __construct(?string $entityId, ?bool $isLocal = false)
    {
        $this->entityId = $entityId;
        $this->isLocal = $isLocal;
    }

    public function __serialize(): array
    {
        return [
            'entityId' => $this->entityId,
            'isLocal' => $this->isLocal
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->entityId = $data['entityId'];
        $this->isLocal = $data['isLocal'];
    }

    public function disableLogUserOperation(): AbstractVariableCmd
    {
        $this->preventLogUserOperation = true;
        return $this;
    }

    public function execute(CommandContext $commandContext, ...$args)
    {
        $this->commandContext = $commandContext;

        $scope = $this->getEntity();

        $this->executeOperation($scope);

        $this->onSuccess($scope);

        if (!$this->preventLogUserOperation) {
            $this->logVariableOperation($scope);
        }

        return null;
    }

    abstract protected function getEntity(): AbstractVariableScope;

    abstract protected function getContextExecution(): ExecutionEntity;

    abstract protected function logVariableOperation(AbstractVariableScope $scope): void;

    abstract protected function executeOperation(AbstractVariableScope $scope): void;

    abstract protected function getLogEntryOperation(): ?string;

    protected function onSuccess(AbstractVariableScope $scope): void
    {
        $contextExecution = $this->getContextExecution();
        if ($contextExecution !== null) {
            $contextExecution->dispatchDelayedEventsAndPerformOperation(null);
        }
    }

    public function isRetryable(): bool
    {
        return false;
    }
}
