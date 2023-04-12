<?php

namespace Jabe\Impl\Cmd;

use Jabe\Impl\Context\Context;
use Jabe\Impl\Interceptor\{
    CommandInterface,
    CommandContext
};
use Jabe\Impl\Persistence\Entity\TaskEntity;
use Jabe\Impl\Util\EnsureUtil;
use Jabe\Variable\Impl\VariableMapImpl;

class GetTaskVariablesCmd implements CommandInterface, \Serializable
{
    protected $taskId;
    protected $variableNames = [];
    protected $isLocal;
    protected $deserializeValues;

    public function __construct(?string $taskId, array $variableNames, bool $isLocal, bool $deserializeValues)
    {
        $this->taskId = $taskId;
        $this->variableNames = $variableNames;
        $this->isLocal = $isLocal;
        $this->deserializeValues = $deserializeValues;
    }

    public function serialize()
    {
        return json_encode([
            'taskId' => $this->taskId,
            'variableNames' => $this->variableNames,
            'isLocal' => $this->isLocal,
            'deserializeValues' => $this->deserializeValues
        ]);
    }

    public function unserialize($data)
    {
        $json = json_decode($data);
        $this->taskId = $json->taskId;
        $this->variableNames = $json->variableNames;
        $this->isLocal = $json->isLocal;
        $this->deserializeValues = $json->deserializeValues;
    }

    public function execute(CommandContext $commandContext, ...$args)
    {
        EnsureUtil::ensureNotNull("taskId", "taskId", $this->taskId);

        $task = Context::getCommandContext()
            ->getTaskManager()
            ->findTaskById($this->taskId);

        EnsureUtil::ensureNotNull("task " . $this->taskId . " doesn't exist", "task", $task);

        $this->checkGetTaskVariables($task, $commandContext);

        $variables = new VariableMapImpl();

        // collect variables from task
        $task->collectVariables($variables, $this->variableNames, $this->isLocal, $this->deserializeValues);

        return $variables;
    }

    protected function checkGetTaskVariables(TaskEntity $task, CommandContext $commandContext): void
    {
        foreach ($commandContext->getProcessEngineConfiguration()->getCommandCheckers() as $checker) {
            $checker->checkReadTaskVariable($task);
        }
    }

    public function isRetryable(): bool
    {
        return false;
    }
}