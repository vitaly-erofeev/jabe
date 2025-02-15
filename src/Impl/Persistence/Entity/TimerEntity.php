<?php

namespace Jabe\Impl\Persistence\Entity;

use Jabe\Impl\Calendar\{
    BusinessCalendarInterface,
    CycleBusinessCalendar
};
use Jabe\Impl\Cfg\{
    ProcessEngineConfigurationImpl,
    TransactionState
};
use Jabe\Impl\Context\Context;
use Jabe\Impl\Interceptor\{
    CommandContext,
    CommandExecutorInterface
};
use Jabe\Impl\JobExecutor\{
    RepeatingFailedJobListener,
    TimerDeclarationImpl,
    TimerEventJobHandler,
    TimerJobConfiguration
};
use Jabe\Impl\Util\ClassNameUtil;

class TimerEntity extends JobEntity
{
    public const TYPE = "timer";

    protected $repeat;

    protected $repeatOffset = 0;

    public function __construct($t = null)
    {
        if ($t instanceof TimerDeclarationImpl) {
            $this->repeat = $t->getRepeat();
        } elseif ($t instanceof TimerEntity) {
            $this->jobHandlerConfiguration = $t->jobHandlerConfiguration;
            $this->jobHandlerType = $t->jobHandlerType;
            $this->isExclusive = $t->isExclusive;
            $this->repeat = $t->repeat;
            $this->repeatOffset = $t->repeatOffset;
            $this->retries = $t->retries;
            $this->executionId = $t->executionId;
            $this->processInstanceId = $t->processInstanceId;
            $this->jobDefinitionId = $t->jobDefinitionId;
            $this->suspensionState = $t->suspensionState;
            $this->deploymentId = $t->deploymentId;
            $this->processDefinitionId = $t->processDefinitionId;
            $this->processDefinitionKey = $t->processDefinitionKey;
            $this->tenantId = $t->tenantId;
            $this->priority = $t->priority;
        }
    }

    protected function preExecute(CommandContext $commandContext, ...$args): void
    {
        if ($this->getJobHandler() instanceof TimerEventJobHandler) {
            $configuration = $this->getJobHandlerConfiguration();
            if ($this->repeat !== null && !$configuration->isFollowUpJobCreated()) {
                // this timer is a repeating timer and
                // a follow up timer job has not been scheduled yet

                $newDueDate = $this->calculateRepeat(...$args);

                if ($newDueDate !== null) {
                    // the listener is added to the transaction as SYNC on ROLLABCK,
                    // when it is necessary to schedule a new timer job invocation.
                    // If the transaction does not rollback, it is ignored.
                    $processEngineConfiguration = Context::getProcessEngineConfiguration();
                    $commandExecutor = $processEngineConfiguration->getCommandExecutorTxRequiresNew();
                    $listener = $this->createRepeatingFailedJobListener($commandExecutor);

                    $commandContext->getTransactionContext()->addTransactionListener(
                        TransactionState::ROLLED_BACK,
                        $listener
                    );

                    // create a new timer job
                    $this->createNewTimerJob($newDueDate, ...$args);
                }
            }
        }
    }

    protected function createRepeatingFailedJobListener(CommandExecutorInterface $commandExecutor): RepeatingFailedJobListener
    {
        return new RepeatingFailedJobListener($commandExecutor, $this->getId());
    }

    public function createNewTimerJob(?string $dueDate, ...$args): void
    {
        // create new timer job
        $newTimer = new TimerEntity($this);
        $newTimer->setDuedate($dueDate);
        Context::getCommandContext()
            ->getJobManager()
            ->schedule($newTimer, ...$args);
    }

    public function calculateRepeat(...$args): ?string
    {
        $businessCalendar = Context::getProcessEngineConfiguration()
            ->getBusinessCalendarManager()
            ->getBusinessCalendar(CycleBusinessCalendar::NAME);
        $date = $businessCalendar->resolveDuedate($this->repeat, null, $this->repeatOffset, ...$args);
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d H:i:s');
        }
        return null;
    }

    public function getRepeat(): ?string
    {
        return $this->repeat;
    }

    public function setRepeat(?string $repeat): void
    {
        $this->repeat = $repeat;
    }

    public function getRepeatOffset(): int
    {
        return $this->repeatOffset;
    }

    public function setRepeatOffset(int $repeatOffset): void
    {
        $this->repeatOffset = $repeatOffset;
    }

    public function getType(): ?string
    {
        return self::TYPE;
    }

    public function getPersistentState()
    {
        $persistentState = parent::getPersistentState();
        $persistentState["repeat"] = $this->repeat;
        return $persistentState;
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'revision' => $this->revision,
            'duedate' => $this->duedate,
            'lockOwner' => $this->lockOwner,
            'lockExpirationTime' => $this->lockExpirationTime,
            'executionId' => $this->executionId,
            'processInstanceId' => $this->processInstanceId,
            'isExclusive' => $this->isExclusive,
            'jobDefinitionId' => $this->jobDefinitionId,
            'jobHandlerType' => $this->jobHandlerType,
            'jobHandlerConfiguration' => $this->jobHandlerConfiguration,
            'exceptionByteArray' => serialize($this->exceptionByteArray),
            'exceptionByteArrayId' => $this->exceptionByteArrayId,
            'exceptionMessage' => $this->exceptionMessage,
            'failedActivityId' => $this->failedActivityId,
            'deploymentId' => $this->deploymentId,
            'priority' => $this->priority,
            'tenantId' => $this->tenantId,
            'repeat' => $this->repeat
        ];
    }

    public function __unserialize(array $data): void
    {
        parent::unserialize($data);
        $this->repeat = $data['repeat'];
    }

    public function __toString()
    {
        $className = ClassNameUtil::getClassNameWithoutPackage(get_class($this));
        return $className
                . "[repeat="  . $this->repeat
                . ", id=" . $this->id
                . ", revision=" . $this->revision
                . ", duedate=" . $this->duedate
                . ", repeatOffset=" . $this->repeatOffset
                . ", lockOwner=" . $this->lockOwner
                . ", lockExpirationTime=" . $this->lockExpirationTime
                . ", executionId=" . $this->executionId
                . ", processInstanceId=" . $this->processInstanceId
                . ", isExclusive=" . $this->isExclusive
                . ", retries=" . $this->retries
                . ", jobHandlerType=" . $this->jobHandlerType
                . ", jobHandlerConfiguration=" . $this->jobHandlerConfiguration
                . ", exceptionByteArray=" . $this->exceptionByteArray
                . ", exceptionByteArrayId=" . $this->exceptionByteArrayId
                . ", exceptionMessage=" . $this->exceptionMessage
                . ", deploymentId=" . $this->deploymentId
                . "]";
    }
}
