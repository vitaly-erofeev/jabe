<?php

namespace BpmPlatform\Engine\Impl\Bpmn\Behavior;

use BpmPlatform\Engine\Impl\Bpmn\Parser\ConditionalEventDefinition;
use BpmPlatform\Engine\Impl\Persistence\Entity\ExecutionEntity;
use BpmPlatform\Engine\Impl\Pvm\Delegate\{
    ActivityBehaviorInterface,
    ActivityExecutionInterface
};
use BpmPlatform\Engine\Impl\Pvm\Process\ActivityImpl;

class EventBasedGatewayActivityBehavior extends FlowNodeActivityBehavior
{
    public function execute(ActivityExecutionInterface $execution): void
    {
        // If conditional events exist after the event based gateway they should be evaluated.
        // If a condition is satisfied the event based gateway should be left,
        // otherwise the event based gateway is a wait state
        $eventBasedGateway = $execution->getActivity();
        foreach ($eventBasedGateway->getEventActivities() as $act) {
            $activityBehavior = $act->getActivityBehavior();
            if ($activityBehavior instanceof ConditionalEventBehaviorInterface) {
                $conditionalEventBehavior = $activityBehavior;
                $conditionalEventDefinition = $conditionalEventBehavior->getConditionalEventDefinition();
                if ($conditionalEventDefinition->tryEvaluate($execution)) {
                    $execution->executeEventHandlerActivity($conditionalEventDefinition->getConditionalActivity());
                    return;
                }
            }
        }
    }
}