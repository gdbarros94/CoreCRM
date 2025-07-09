<?php

namespace Core;

use Ramsey\Uuid\Uuid;

/**
 * Class UuidHandler
 *
 * This class provides methods for generating and validating UUIDs (Universally Unique Identifiers).
 * It utilizes the Ramsey UUID library to handle UUID operations.
 *
 * @package Core
 */
class UuidHandler
{
    /**
     * Generates a version 4 (random) UUID.
     *
     * @return string The generated UUID as a string.
     */
    public static function generateV4(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * Checks if a given UUID is valid.
     *
     * @param string $uuid The UUID to check.
     *
     * @return bool True if the UUID is valid, false otherwise.
     */
    public static function isValid(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }
}