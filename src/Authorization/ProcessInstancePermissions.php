<?php

namespace Jabe\Authorization;

/**
 * The set of built-in Permissions for Resources::PROCESS_INSTANCE.
 */
class ProcessInstancePermissions implements PermissionInterface
{
    use PermissionTrait;

    private static $NONE;

    public static function none(): PermissionInterface
    {
        if (self::$NONE === null) {
            self::$NONE = new ProcessInstancePermissions("NONE", 0);
        }
        return self::$NONE;
    }

    private static $ALL;

    public static function all(): PermissionInterface
    {
        if (self::$ALL === null) {
            self::$ALL = new ProcessInstancePermissions("ALL", PHP_INT_MAX);
        }
        return self::$ALL;
    }

    private static $READ;

    public static function read(): PermissionInterface
    {
        if (self::$READ === null) {
            self::$READ = new ProcessInstancePermissions("READ", 2);
        }
        return self::$READ;
    }

    private static $UPDATE;

    public static function update(): PermissionInterface
    {
        if (self::$UPDATE === null) {
            self::$UPDATE = new ProcessInstancePermissions("UPDATE", 4);
        }
        return self::$UPDATE;
    }

    private static $CREATE;

    public static function create(): PermissionInterface
    {
        if (self::$CREATE === null) {
            self::$CREATE = new ProcessInstancePermissions("CREATE", 8);
        }
        return self::$CREATE;
    }

    private static $DELETE;

    public static function delete(): PermissionInterface
    {
        if (self::$DELETE === null) {
            self::$DELETE = new ProcessInstancePermissions("DELETE", 16);
        }
        return self::$DELETE;
    }

    private static $RETRY_JOB;

    public static function retryJob(): PermissionInterface
    {
        if (self::$RETRY_JOB === null) {
            self::$RETRY_JOB = new ProcessInstancePermissions("RETRY_JOB", 32);
        }
        return self::$RETRY_JOB;
    }

    private static $SUSPEND;

    public static function suspend(): PermissionInterface
    {
        if (self::$SUSPEND === null) {
            self::$SUSPEND = new ProcessInstancePermissions("SUSPEND", 64);
        }
        return self::$SUSPEND;
    }

    private static $UPDATE_VARIABLE;

    public static function updateVariable(): PermissionInterface
    {
        if (self::$UPDATE_VARIABLE === null) {
            self::$UPDATE_VARIABLE = new ProcessInstancePermissions("UPDATE_VARIABLE", 128);
        }
        return self::$UPDATE_VARIABLE;
    }

    private static $RESOURCES;

    public static function resources(): array
    {
        if (self::$RESOURCES === null) {
            self::$RESOURCES = [ Resources::processInstance() ];
        }
        return self::$RESOURCES;
    }

    private function __construct(?string $name, int $id)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function getTypes(): array
    {
        return self::resources();
    }
}
