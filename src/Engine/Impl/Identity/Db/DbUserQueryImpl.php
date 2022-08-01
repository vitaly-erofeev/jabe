<?php

namespace Jabe\Engine\Impl\Identity\Db;

use Jabe\Engine\Impl\{
    Page,
    UserQueryImpl
};
use Jabe\Engine\Impl\Interceptor\{
    CommandContext,
    CommandExecutorInterface
};

class DbUserQueryImpl extends UserQueryImpl
{
    public function __construct(CommandExecutorInterface $commandExecutor = null)
    {
        parent::__construct($commandExecutor);
    }

    public function executeCount(CommandContext $commandContext): int
    {
        $this->checkQueryOk();
        $identityProvider = $this->getIdentityProvider($commandContext);
        return $identityProvider->findUserCountByQueryCriteria($this);
    }

    public function executeList(CommandContext $commandContext, Page $page): array
    {
        $this->checkQueryOk();
        $identityProvider = $this->getIdentityProvider($commandContext);
        return $identityProvider->findUserByQueryCriteria($this);
    }

    private function getIdentityProvider(CommandContext $commandContext): DbReadOnlyIdentityServiceProvider
    {
        return $commandContext->getReadOnlyIdentityProvider();
    }
}
