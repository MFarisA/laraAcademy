<?php

namespace App\Interface\Access\Permission;

interface PermissionInterface
{
    public function label(): string;

    public static function group(): string;

    /**
     * @return array<mixed>
     */
    public static function toArray(): array;
}
