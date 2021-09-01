<?php

namespace BpmPlatform\Model\Bpmn\Impl\Instance;

use BpmPlatform\Model\Xml\ModelBuilder;
use BpmPlatform\Model\Xml\Instance\ModelElementInstanceInterface;
use BpmPlatform\Model\Xml\Impl\Instance\ModelTypeInstanceContext;
use BpmPlatform\Model\Xml\Type\ModelTypeInstanceProviderInterface;
use BpmPlatform\Model\Xml\Impl\Util\StringUtil;
use BpmPlatform\Model\Bpmn\Builder\UserTaskBuilder;
use BpmPlatform\Model\Bpmn\Impl\BpmnModelConstants;
use BpmPlatform\Model\Bpmn\Instance\{
    RenderingInterface,
    TaskInterface,
    UserTaskInterface
};

class UserTaskImpl extends TaskImpl implements UserTaskInterface
{
    protected static $implementationAttribute;
    protected static $renderingCollection;
    protected static $assigneeAttribute;
    protected static $candidateGroupsAttribute;
    protected static $candidateUsersAttribute;
    protected static $dueDateAttribute;
    protected static $followUpDateAttribute;
    protected static $formHandlerClassAttribute;
    protected static $formKeyAttribute;
    protected static $priorityAttribute;

    public function __construct(ModelTypeInstanceContext $instanceContext)
    {
        parent::__construct($instanceContext);
    }

    public static function registerType(ModelBuilder $modelBuilder): void
    {
        $typeBuilder = $modelBuilder->defineType(
            UserTaskInterface::class,
            BpmnModelConstants::BPMN_ELEMENT_USER_TASK
        )
        ->namespaceUri(BpmnModelConstants::BPMN20_NS)
        ->extendsType(TaskInterface::class)
        ->instanceProvider(
            new class extends ModelTypeInstanceProviderInterface
            {
                public function newInstance(ModelTypeInstanceContext $instanceContext): ModelElementInstanceInterface
                {
                    return new UserTaskImpl($instanceContext);
                }
            }
        );

        self::$implementationAttribute = $typeBuilder->stringAttribute(
            BpmnModelConstants::BPMN_ATTRIBUTE_IMPLEMENTATION
        )
        ->defaultValue("##unspecified")
        ->build();

        $sequenceBuilder = $typeBuilder->sequence();

        self::$renderingCollection = $sequenceBuilder->elementCollection(RenderingInterface::class)
        ->build();

        self::$camundaAssigneeAttribute = $typeBuilder->stringAttribute(BpmnModelConstants::ATTRIBUTE_ASSIGNEE)
        ->namespace(BpmnModelConstants::NS)
        ->build();

        self::$camundaCandidateGroupsAttribute = $typeBuilder->stringAttribute(
            BpmnModelConstants::ATTRIBUTE_CANDIDATE_GROUPS
        )
        ->namespace(BpmnModelConstants::NS)
        ->build();

        self::$camundaCandidateUsersAttribute = $typeBuilder->stringAttribute(
            BpmnModelConstants::ATTRIBUTE_CANDIDATE_USERS
        )
        ->namespace(BpmnModelConstants::NS)
        ->build();

        self::$camundaDueDateAttribute = $typeBuilder->stringAttribute(BpmnModelConstants::ATTRIBUTE_DUE_DATE)
        ->namespace(BpmnModelConstants::NS)
        ->build();

        self::$camundaFollowUpDateAttribute = $typeBuilder->stringAttribute(
            BpmnModelConstants::ATTRIBUTE_FOLLOW_UP_DATE
        )
        ->namespace(BpmnModelConstants::NS)
        ->build();

        self::$camundaFormHandlerClassAttribute = $typeBuilder->stringAttribute(
            BpmnModelConstants::ATTRIBUTE_FORM_HANDLER_CLASS
        )
        ->namespace(BpmnModelConstants::NS)
        ->build();

        self::$camundaFormKeyAttribute = $typeBuilder->stringAttribute(BpmnModelConstants::ATTRIBUTE_FORM_KEY)
        ->namespace(BpmnModelConstants::NS)
        ->build();

        self::$camundaPriorityAttribute = $typeBuilder->stringAttribute(BpmnModelConstants::ATTRIBUTE_PRIORITY)
        ->namespace(BpmnModelConstants::NS)
        ->build();

        $typeBuilder->build();
    }

    public function builder(): UserTaskBuilder
    {
        return new UserTaskBuilder($this->modelInstance, $this);
    }

    public function getImplementation(): string
    {
        return self::$implementationAttribute->getValue($this);
    }

    public function setImplementation(string $implementation): void
    {
        self::$implementationAttribute->setValue($this, $implementation);
    }

    public function getRenderings(): array
    {
        return self::$renderingCollection->get($this);
    }

    public function getAssignee(): string
    {
        return self::$assigneeAttribute->getValue($this);
    }

    public function setAssignee(string $assignee): void
    {
        self::$assigneeAttribute->setValue($this, $assignee);
    }

    public function getCandidateGroups(): string
    {
        return self::$candidateGroupsAttribute->getValue($this);
    }

    public function setCandidateGroups(string $candidateGroups): void
    {
        self::$candidateGroupsAttribute->setValue($this, $candidateGroups);
    }

    public function getCandidateGroupsList(): array
    {
        $candidateGroups = self::$candidateGroupsAttribute->getValue($this);
        return StringUtil::splitCommaSeparatedList($candidateGroups);
    }

    public function setCandidateGroupsList(array $candidateGroupsList): void
    {
        $candidateGroups = StringUtil::joinCommaSeparatedList($candidateGroupsList);
        self::$candidateGroupsAttribute->setValue($this, $candidateGroups);
    }

    public function getCandidateUsers(): array
    {
        $candidateUsers = self::$candidateUsersAttribute->getValue($this);
        return StringUtil::splitCommaSeparatedList($candidateUsers);
    }

    public function setCandidateUsers(string $candidateUsers): void
    {
        self::$candidateUsersAttribute->setValue($this, $candidateUsers);
    }

    public function getCandidateUsersList(): array
    {
        $candidateUsers = self::$candidateUsersAttribute->getValue($this);
        return StringUtil::splitCommaSeparatedList($candidateUsers);
    }

    public function setCandidateUsersList(array $candidateUsersList): void
    {
        $candidateUsers = StringUtil::joinCommaSeparatedList($candidateUsersList);
        self::$candidateUsersAttribute->setValue($this, $candidateUsers);
    }

    public function getDueDate(): string
    {
        return self::$dueDateAttribute->getValue($this);
    }

    public function setDueDate(string $dueDate): void
    {
        self::$dueDateAttribute->setValue($this, $dueDate);
    }

    public function getFollowUpDate(): string
    {
        return self::$followUpDateAttribute->getValue($this);
    }

    public function setFollowUpDate(string $followUpDate): void
    {
        self::$dueFollowUpAttribute->setValue($this, $followUpDate);
    }

    public function getFormHandlerClass(): string
    {
        return self::$formHandlerClassAttribute->getValue($this);
    }

    public function setFormHandlerClass(string $formHandlerClass): void
    {
        self::$formHandlerClassAttribute->setValue($this, $formHandlerClass);
    }

    public function getFormKey(): string
    {
        return self::$formKeyAttribute->getValue($this);
    }

    public function setFormKey(string $formKey): void
    {
        self::$formKeyAttribute->setValue($this, $formKey);
    }

    public function getPriority(): string
    {
        return self::$priorityAttribute->getValue($this);
    }

    public function setPriority(string $priority): void
    {
        self::$priorityAttribute->setValue($this, $priority);
    }
}