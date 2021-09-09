<?php

namespace BpmPlatform\Model\Xml\Impl\Type\Attribute;

use BpmPlatform\Model\Xml\Type\ModelElementTypeInterface;

class EnumAttribute extends AttributeImpl
{
    private $type;

    public function __construct(ModelElementTypeInterface $owningElementType, string $type)
    {
        parent::__construct($owningElementType);
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    protected function convertXmlValueToModelValue(?string $rawValue)
    {
        if ($rawValue != null) {
            $class = new ReflectionClass($this->type);
            $value = $class->getConstant($rawValue);
            return $value;
        } else {
            return null;
        }
    }

    /**
     * @param mixed $modelValue;
     */
    protected function convertModelValueToXmlValue($modelValue): string
    {
        $class = new \ReflectionClass($this->type);
        $constants = $class->getConstants();
        return array_search($modelValue, $constants);
    }
}
