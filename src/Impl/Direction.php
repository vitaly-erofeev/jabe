<?php

namespace Jabe\Impl;

class Direction
{
    private static $ASCENDING;
    private static $DESCENDING;

    private $name;

    public function __construct(?string $name)
    {
        $this->name = $name;
    }

    public function __serialize(): array
    {
        return [
            'name' => $this->name
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->name = $data['name'];
    }

    public static function ascending(): Direction
    {
        if (self::$ASCENDING === null) {
            self::$ASCENDING = new Direction("asc");
        }
        return self::$ASCENDING;
    }

    public static function descending(): Direction
    {
        if (self::$DESCENDING === null) {
            self::$DESCENDING = new Direction("desc");
        }
        return self::$DESCENDING;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function __toString()
    {
        return "Direction["
            . "name=" . $this->name
            . "]";
    }

    public static function findByName(?string $directionName): ?Direction
    {
        switch ($directionName) {
            case "asc":
                return self::ascending();
            case "desc":
                return self::descending();
            default:
                return null;
        }
    }
}
