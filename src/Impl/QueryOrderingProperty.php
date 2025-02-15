<?php

namespace Jabe\Impl;

use Jabe\Task\{
    TaskInterface,
    TaskQueryInterface
};
use Jabe\Query\QueryPropertyInterface;

class QueryOrderingProperty
{

    public const RELATION_VARIABLE = "variable";
    public const RELATION_PROCESS_DEFINITION = "process-definition";
    public const RELATION_CASE_DEFINITION = "case-definition";
    public const RELATION_DEPLOYMENT = "deployment";

    protected $relation;
    protected $queryProperty;
    protected $direction;
    protected $relationConditions;

    public function __construct($queryPropertyOrRelation, $directionOrProperty)
    {
        if ($queryPropertyOrRelation !== null && $queryPropertyOrRelation instanceof QueryPropertyInterface) {
            $this->queryProperty = $queryPropertyOrRelation;
            $this->direction = $directionOrProperty;
        } elseif ($directionOrProperty instanceof QueryPropertyInterface) {
            $this->relation = $queryPropertyOrRelation;
            $this->queryProperty = $directionOrProperty;
        }
    }

    public function __serialize(): array
    {
        $relationConditions = [];
        foreach ($this->relationConditions as $cond) {
            $relationConditions[] = serialize($cond);
        }
        return [
            'relation' => $this->relation,
            'queryProperty' => serialize($this->queryProperty),
            'direction' => serialize($this->direction),
            'relationConditions' => $relationConditions
        ];
    }

    public function __unserialize(array $data): void
    {
        $relationConditions = [];
        foreach ($data['relationConditions'] as $cond) {
            $relationConditions[] = unserialize($cond);
        }
        $this->relation = $data['relation'];
        $this->queryProperty = unserialize($data['queryProperty']);
        $this->direction = unserialize($data['direction']);
        $this->relationConditions = $relationConditions;
    }

    public function getQueryProperty(): ?QueryPropertyInterface
    {
        return $this->queryProperty;
    }

    public function setQueryProperty(QueryPropertyInterface $queryProperty): void
    {
        $this->queryProperty = $queryProperty;
    }

    public function setDirection(Direction $direction): void
    {
        $this->direction = $direction;
    }

    public function getDirection(): ?Direction
    {
        return $this->direction;
    }

    public function getRelationConditions(): array
    {
        return $this->relationConditions;
    }

    public function setRelationConditions(array $relationConditions): void
    {
        $this->relationConditions = $relationConditions;
    }

    public function hasRelationConditions(): bool
    {
        return !empty($this->relationConditions) && !empty($this->relationConditions);
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(?string $relation): void
    {
        $this->relation = $relation;
    }

    /**
     * @return whether this ordering property is contained in the default fields
     * of the base entity (e.g. task.NAME_ is a contained property; LOWER(task.NAME_) or
     * variable.TEXT_ (given a task query) is not contained)
     */
    public function isContainedProperty(): bool
    {
        return $this->relation === null && $this->queryProperty->getFunction() === null;
    }

    public function __toString()
    {
        return "QueryOrderingProperty["
            . "relation=" . $this->relation
            . ", queryProperty=" . $this->queryProperty
            . ", direction=" . $this->direction
            . ", relationConditions=" . $this->getRelationConditionsString()
            . "]";
    }

    public function getRelationConditionsString(): ?string
    {
        $builder = "";
        $builder .= "[";
        if (!empty($this->relationConditions)) {
            for ($i = 0; $i < count($this->relationConditions); $i += 1) {
                if ($i > 0) {
                    $builder .= ",";
                }
                $builder .= $this->relationConditions[$i];
            }
        }
        $builder .= "]";
        return $builder;
    }
}
