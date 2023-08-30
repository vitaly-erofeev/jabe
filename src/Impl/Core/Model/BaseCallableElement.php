<?php

namespace Jabe\Impl\Core\Model;

use Jabe\Delegate\VariableScopeInterface;
use Jabe\Impl\Core\Variable\Mapping\Value\ParameterValueProviderInterface;

class BaseCallableElement
{
    protected $definitionKeyValueProvider;
    protected $binding;
    protected $versionValueProvider;
    protected $versionTagValueProvider;
    protected $tenantIdProvider;
    protected $deploymentId;

    public function getDefinitionKey(VariableScopeInterface $variableScope): ?string
    {
        $result = $this->definitionKeyValueProvider->getValue($variableScope);

        if ($result !== null && !(is_string($result))) {
            throw new \Exception("Cannot cast '" . $result . "' to string");
        }

        return $result;
    }

    public function getDefinitionKeyValueProvider(): ParameterValueProviderInterface
    {
        return $this->definitionKeyValueProvider;
    }

    public function setDefinitionKeyValueProvider(ParameterValueProviderInterface $definitionKey): void
    {
        $this->definitionKeyValueProvider = $definitionKey;
    }

    public function getBinding(): ?string
    {
        return $this->binding;
    }

    public function setBinding(?string $binding): void
    {
        $this->binding = $binding;
    }

    public function isLatestBinding(): bool
    {
        return $this->binding === null || CallableElementBinding::LATEST == $this->binding;
    }

    public function isDeploymentBinding(): bool
    {
        return CallableElementBinding::DEPLOYMENT == $this->binding;
    }

    public function isVersionBinding(): bool
    {
        return CallableElementBinding::VERSION == $this->binding;
    }

    public function isVersionTagBinding(): bool
    {
        return CallableElementBinding::VERSION_TAG == $this->binding;
    }

    public function getVersion(VariableScopeInterface $variableScope): ?int
    {
        $result = $this->versionValueProvider->getValue($variableScope);

        if ($result !== null) {
            return intval($result);
        }

        return null;
    }

    public function getVersionValueProvider(): ParameterValueProviderInterface
    {
        return $this->versionValueProvider;
    }

    public function setVersionValueProvider(ParameterValueProviderInterface $version): void
    {
        $this->versionValueProvider = $version;
    }

    public function getVersionTag(VariableScopeInterface $variableScope): ?string
    {
        $result = $this->versionTagValueProvider->getValue($variableScope);

        if ($result !== null) {
            return strval($result);
        }

        return null;
    }

    public function getVersionTagValueProvider(): ParameterValueProviderInterface
    {
        return $this->versionTagValueProvider;
    }

    public function setVersionTagValueProvider(ParameterValueProviderInterface $version): void
    {
        $this->versionTagValueProvider = $version;
    }

    public function setTenantIdProvider(?ParameterValueProviderInterface $tenantIdProvider): void
    {
        $this->tenantIdProvider = $tenantIdProvider;
    }

    public function getDeploymentId(): ?string
    {
        return $this->deploymentId;
    }

    public function setDeploymentId(?string $deploymentId): void
    {
        $this->deploymentId = $deploymentId;
    }

    public function getDefinitionTenantId(VariableScopeInterface $variableScope, ?string $defaultTenantId = null): ?string
    {
        if ($this->tenantIdProvider !== null) {
            return $this->tenantIdProvider->getValue($variableScope);
        } else {
            return $defaultTenantId;
        }
    }

    public function getTenantIdProvider(): ParameterValueProviderInterface
    {
        return $this->tenantIdProvider;
    }

    /**
     * @return bool true if any of the references that specify the callable element are non-literal and need to be resolved with
     * potential side effects to determine the process or case definition that is to be called.
     */
    public function hasDynamicReferences(): bool
    {
        return ($this->tenantIdProvider !== null && $this->tenantIdProvider->isDynamic())
            || $this->definitionKeyValueProvider->isDynamic()
            || $this->versionValueProvider->isDynamic()
            || $this->versionTagValueProvider->isDynamic();
    }
}
