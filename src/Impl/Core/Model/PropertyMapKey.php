<?php

namespace Jabe\Impl\Core\Model;

class PropertyMapKey
{
    protected $name;
    protected bool $allowOverwrite = true;

    public function __construct(?string $name, ?bool $allowOverwrite = true)
    {
        $this->name = $name;
        $this->allowOverwrite = $allowOverwrite;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function allowsOverwrite(): bool
    {
        return $this->allowOverwrite;
    }

    public function __toString()
    {
        return "PropertyMapKey [name=" . $this->name . "]";
    }
}
