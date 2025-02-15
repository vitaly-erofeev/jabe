<?php

namespace Jabe\Impl\Cmd;

use Jabe\BadUserRequestException;
use Jabe\History\UserOperationLogEntryInterface;
use Jabe\Impl\Interceptor\{
    CommandInterface,
    CommandContext
};
use Jabe\Impl\Persistence\Entity\PropertyChange;
use Jabe\Impl\Util\EnsureUtil;

class ResolveIncidentCmd implements CommandInterface
{
    protected $incidentId;

    public function __construct(?string $incidentId)
    {
        EnsureUtil::ensureNotNull(BadUserRequestException::class, "incidentId", $incidentId);
        $this->incidentId = $incidentId;
    }

    public function execute(CommandContext $commandContext, ...$args)
    {
        $incident = $commandContext->getIncidentManager()->findIncidentById($this->incidentId);

        EnsureUtil::ensureNotNull(
            "Cannot find an incident with id '" . $this->incidentId . "'",
            "incident",
            $incident
        );

        if ($incident->getIncidentType() == "failedJob" || $incident->getIncidentType() == "failedExternalTask") {
            throw new BadUserRequestException("Cannot resolve an incident of type " . $incident->getIncidentType());
        }

        EnsureUtil::ensureNotNull(BadUserRequestException::class, "executionId", $incident->getExecutionId());
        $execution = $commandContext->getExecutionManager()->findExecutionById($incident->getExecutionId());

        EnsureUtil::ensureNotNull("Cannot find an execution for an incident with id '" . $this->incidentId . "'", "execution", $execution);

        foreach ($commandContext->getProcessEngineConfiguration()->getCommandCheckers() as $checker) {
            $checker->checkUpdateProcessInstance($execution);
        }

        $commandContext->getOperationLogManager()->logProcessInstanceOperation(
            UserOperationLogEntryInterface::OPERATION_TYPE_RESOLVE,
            $execution->getProcessInstanceId(),
            $execution->getProcessDefinitionId(),
            null,
            [new PropertyChange("incidentId", null, $this->incidentId)],
            null
        );

        $execution->resolveIncident($this->incidentId);
        return null;
    }

    public function isRetryable(): bool
    {
        return false;
    }
}
