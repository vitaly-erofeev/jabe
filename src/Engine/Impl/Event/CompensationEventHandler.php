<?php

namespace Jabe\Engine\Impl\Event;

use Jabe\Engine\ProcessEngineException;
use Jabe\Engine\Impl\Bpmn\Helper\CompensationUtil;
use Jabe\Engine\Impl\Interceptor\CommandContext;
use Jabe\Engine\Impl\Persistence\Entity\{
    EventSubscriptionEntity,
    ExecutionEntity
};
use Jabe\Engine\Impl\Pvm\Delegate\CompositeActivityBehaviorInterface;
use Jabe\Engine\Impl\Pvm\Runtime\AtomicOperation;
use Jabe\Engine\Impl\Util\EnsureUtil;

class CompensationEventHandler implements EventHandlerInterface
{
    public function getEventHandlerType(): string
    {
        return EventType::compensate()->name();
    }

    public function handleEvent(
        EventSubscriptionEntity $eventSubscription,
        $payload,
        $localPayload,
        ?string $businessKey,
        CommandContext $commandContext
    ): void {
        $eventSubscription->delete();

        $configuration = $eventSubscription->getConfiguration();
        EnsureUtil::ensureNotNull(
            "Compensating execution not set for compensate event subscription with id " . $eventSubscription->getId(),
            "configuration",
            $configuration
        );

        $compensatingExecution = $commandContext->getExecutionManager()->findExecutionById($configuration);

        $compensationHandler = $eventSubscription->getActivity();

        // activate execution
        $compensatingExecution->setActive(true);

        if ($compensatingExecution->getActivity()->getActivityBehavior() instanceof CompositeActivityBehaviorInterface) {
            $compensatingExecution->getParent()->setActivityInstanceId($compensatingExecution->getActivityInstanceId());
        }

        if ($compensationHandler->isScope() && !$compensationHandler->isCompensationHandler()) {
            // descend into scope:
            $eventsForThisScope = $compensatingExecution->getCompensateEventSubscriptions();
            CompensationUtil::throwCompensationEvent($eventsForThisScope, $compensatingExecution, false);
        } else {
            try {
                if ($compensationHandler->isSubProcessScope() && $compensationHandler->isTriggeredByEvent()) {
                    $compensatingExecution->executeActivity($compensationHandler);
                } else {
                    // since we already have a scope execution, we don't need to create another one
                    // for a simple scoped compensation handler
                    $compensatingExecution->setActivity($compensationHandler);
                    $compensatingExecution->performOperation(AtomicOperation::activityStart());
                }
            } catch (\Exception $e) {
                throw new ProcessEngineException("Error while handling compensation event ", $e);
            }
        }
    }
}
