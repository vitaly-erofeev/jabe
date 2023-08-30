<?php

namespace Jabe\Runtime;

interface UpdateProcessInstanceSuspensionStateBuilderInterface
{
    /**
     * <p>
     * Activates the provided process instances.
     * </p>
     *
     * <p>
     * If you have a process instance hierarchy, activating one process instance
     * from the hierarchy will not activate other process instances from that
     * hierarchy.
     * </p>
     *
     * @throws ProcessEngineException
     *           If no such processDefinition can be found.
     * @throws AuthorizationException
     *           if the user has none of the following:
     *           <li>ProcessInstancePermissions#SUSPEND permission on Resources#PROCESS_INSTANCE</li>
     *           <li>ProcessDefinitionPermissions#SUSPEND_INSTANCE permission on Resources#PROCESS_DEFINITION</li>
     *           <li>Permissions#UPDATE permission on Resources#PROCESS_INSTANCE</li>
     *           <li>Permissions#UPDATE_INSTANCE permission on Resources#PROCESS_DEFINITION</li>
     * @throws BadUserRequestException
     *           When the affected instances count exceeds the maximum results limit. A maximum results
     *           limit can be specified with the process engine configuration property
     *           <code>queryMaxResultsLimit</code> (default Integer#MAX_VALUE).
     *           Please use the batch operation
     *           UpdateProcessInstancesSuspensionStateBuilder#activateAsync() instead.
     */
    public function activate(): void;

    /**
     * <p>
     * Suspends the provided process instances. This means that the execution is
     * stopped, so the <i>token state</i> will not change. However, actions that
     * do not change token state, like setting/removing variables, etc. will
     * succeed.
     * </p>
     *
     * <p>
     * Tasks belonging to the suspended process instance will also be suspended.
     * This means that any actions influencing the tasks' lifecycles will fail,
     * such as
     * <ul>
     * <li>claiming</li>
     * <li>completing</li>
     * <li>delegation</li>
     * <li>changes in task assignees, owners, etc.</li>
     * </ul>
     * Actions that only change task properties will succeed, such as changing
     * variables or adding comments.
     * </p>
     *
     * <p>
     * If a process instance is in state suspended, the engine will also not
     * execute jobs (timers, messages) associated with this instance.
     * </p>
     *
     * <p>
     * If you have a process instance hierarchy, suspending one process instance
     * from the hierarchy will not suspend other process instances from that
     * hierarchy.
     * </p>
     *
     * @throws ProcessEngineException
     *           If no such processDefinition can be found.
     * @throws AuthorizationException
     *            if the user has none of the following:
     *           <li>ProcessInstancePermissions#SUSPEND permission on Resources#PROCESS_INSTANCE</li>
     *           <li>ProcessDefinitionPermissions#SUSPEND_INSTANCE permission on Resources#PROCESS_DEFINITION</li>
     *           <li>Permissions#UPDATE permission on Resources#PROCESS_INSTANCE</li>
     *           <li>Permissions#UPDATE_INSTANCE permission on Resources#PROCESS_DEFINITION</li>
     * @throws BadUserRequestException
     *           When the affected instances count exceeds the maximum results limit. A maximum results
     *           limit can be specified with the process engine configuration property
     *           <code>queryMaxResultsLimit</code> (default Integer#MAX_VALUE).
     *           Please see the batch operation
     *           UpdateProcessInstancesSuspensionStateBuilder#suspendAsync() instead.
     */
    public function suspend(): void;
}
