<?php

namespace BpmPlatform\Model\Xml\TestModel\Instance;

use BpmPlatform\Model\Xml\ModelBuilder;
use BpmPlatform\Model\Xml\Impl\Instance\{
    ModelElementInstanceImpl,
    ModelTypeInstanceContext
};
use BpmPlatform\Model\Xml\Type\ModelTypeInstanceProviderInterface;
use BpmPlatform\Model\Xml\TestModel\TestModelConstants;

class ChildRelationshipDefinition extends RelationshipDefinition
{
    public function __construct(ModelTypeInstanceContext $instanceContext)
    {
        parent::__construct($instanceContext);
    }

    public static function registerType(ModelBuilder $modelBuilder): void
    {
        $typeBuilder = $modelBuilder->defineType(
            ChildRelationshipDefinition::class,
            TestModelConstants::TYPE_NAME_CHILD_RELATIONSHIP_DEFINITION
        )
        ->namespaceUri(TestModelConstants::MODEL_NAMESPACE)
        ->extendsType(RelationshipDefinition::class)
        ->instanceProvider(
            new class extends ModelTypeInstanceProviderInterface
            {
                public function newInstance(ModelTypeInstanceContext $instanceContext): ChildRelationshipDefinition
                {
                    return new ChildRelationshipDefinition($instanceContext);
                }
            }
        );

        $typeBuilder->build();
    }
}