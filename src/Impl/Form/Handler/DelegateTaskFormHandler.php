<?php

namespace Jabe\Impl\Form\Handler;

use Jabe\Form\TaskFormDataInterface;
use Jabe\Impl\Context\Context;
use Jabe\Impl\Persistence\Entity\{
    DeploymentEntity,
    TaskEntity
};

class DelegateTaskFormHandler extends DelegateFormHandler implements TaskFormHandlerInterface
{
    public function __construct(TaskFormHandlerInterface $formHandler, DeploymentEntity $deployment)
    {
        parent::__construct($formHandler, $deployment->getId());
    }

    public function createTaskForm(TaskEntity $task): TaskFormDataInterface
    {
        $formHandler = $this->formHandler;
        return $this->performContextSwitch(function () use ($formHandler, $task) {
            $invocation = new CreateTaskFormInvocation($formHandler, $task);
            Context::getProcessEngineConfiguration()
                ->getDelegateInterceptor()
                ->handleInvocation($invocation);
            return $invocation->getInvocationResult();
        });
    }

    public function getFormHandler(): FormHandlerInterface
    {
        return $this->formHandler;
    }
}
