<?php

namespace Jabe\Impl\Core\Operation;

use Jabe\Delegate\{
    BaseDelegateExecutionInterface,
    DelegateListenerInterface
};
use Jabe\Impl\Core\Instance\CoreExecution;
use Jabe\Impl\Core\Model\CoreModelElement;
use Jabe\Impl\Pvm\PvmException;

abstract class AbstractEventAtomicOperation implements CoreAtomicOperationInterface
{
    public function isAsync(CoreExecution $execution): bool
    {
        return false;
    }

    public function execute(CoreExecution $execution, ...$args): void
    {
        $scope = $this->getScope($execution);
        $listeners = $execution->hasFailedOnEndListeners() ? $this->getBuiltinListeners($scope) : $this->getListeners($scope, $execution);
        $listenerIndex = $execution->getListenerIndex();

        if ($listenerIndex == 0) {
            $execution = $this->eventNotificationsStarted($execution, ...$args);
        }
        if (!$this->isSkipNotifyListeners($execution)) {
            if (count($listeners) > $listenerIndex) {
                $execution->setEventName($this->getEventName());
                $execution->setEventSource($scope);
                $listener = $listeners[$listenerIndex];
                $execution->setListenerIndex($listenerIndex + 1);

                try {
                    $execution->invokeListener($listener);
                } catch (\Throwable $ex) {
                    $this->eventNotificationsFailed($execution, $ex);
                    // do not continue listener invocation once a listener has failed
                    return;
                }
                $execution->performOperationSync($this);
            } else {
                $this->resetListeners($execution);
                $act = strval($execution->getActivity());
                $this->eventNotificationsCompleted($execution);
            }
        } else {
            $this->eventNotificationsCompleted($execution);
        }
    }

    protected function resetListeners(CoreExecution $execution): void
    {
        $execution->setListenerIndex(0);
        $execution->setEventName(null);
        $execution->setEventSource(null);
    }

    protected function getListeners(CoreModelElement $scope, CoreExecution $execution): array
    {
        if ($execution->isSkipCustomListeners()) {
            return $scope->getBuiltInListeners($this->getEventName());
        } else {
            return $scope->getListeners($this->getEventName());
        }
    }

    protected function isSkipNotifyListeners(CoreExecution $execution): bool
    {
        return false;
    }

    protected function eventNotificationsStarted(CoreExecution $execution, ...$args): CoreExecution
    {
        // do nothing
        return $execution;
    }

    abstract protected function getScope(CoreExecution $execution): CoreModelElement;
    abstract public function getEventName(): ?string;
    abstract protected function eventNotificationsCompleted(CoreExecution $execution): void;

    protected function eventNotificationsFailed(CoreExecution $execution, \Exception $exception): void
    {
        $t =  $exception->getTrace()[0];
        throw new PvmException(sprintf("couldn't execute event listener: %s, %s.%s.%s", $exception->getMessage(), $t["file"], $t["function"], $t["line"]), $exception);
    }
}
