<?php

namespace Jabe\Variable\Impl\Value;

use Jabe\Variable\Value\TypedValueInterface;
use Jabe\Variable\Type\ValueTypeInterface;

class AbstractTypedValue implements TypedValueInterface
{
    protected $value;

    protected $type;

    protected bool $isTransient = false;

    public function __construct($value, ValueTypeInterface $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getType(): ?ValueTypeInterface
    {
        return $this->type;
    }

    public function __toString()
    {
        return sprintf("Value '%s' of type '%s', isTransient=%s", $this->value, $this->type, $this->isTransient);
    }

    public function __serialize(): array
    {
        return [
            'value' => $this->value,
            'type' => serialize($this->type),
            'isTransient' => $this->isTransient
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->value = $data['value'];
        $this->type = unserialize($data['type']);
        $this->isTransient = $data['isTransient'];
    }

    public function isTransient(): bool
    {
        return $this->isTransient;
    }

    public function setTransient(bool $isTransient): void
    {
        $this->isTransient = $isTransient;
    }
}
