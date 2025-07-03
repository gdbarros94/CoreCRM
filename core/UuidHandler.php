<?php

namespace Core;

use Ramsey\Uuid\Uuid;

class UuidHandler
{
    public static function generateV4(): string
    {
        return Uuid::uuid4()->toString();
    }

    public static function isValid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }
}