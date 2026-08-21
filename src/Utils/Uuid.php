<?php

namespace ClinicFlow\Utils;

class Uuid {
    /**
     * Generate a UUIDv7 (time-ordered 128-bit UUID).
     */
    public static function uuidv7(): string {
        $msec = (int) (microtime(true) * 1000);
        $timeHex = str_pad(dechex($msec), 12, '0', STR_PAD_LEFT);

        $bytes = random_bytes(10);

        // set version to 0111 (7) in high bits of bytes[0]
        $bytes[0] = chr((ord($bytes[0]) & 0x0f) | 0x70);
        // set variant to 10xx in high bits of bytes[2]
        $bytes[2] = chr((ord($bytes[2]) & 0x3f) | 0x80);

        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($timeHex, 0, 8),
            substr($timeHex, 8, 4),
            substr($hex, 0, 4),
            substr($hex, 4, 4),
            substr($hex, 8, 12)
        );
    }

    /**
     * Check if a string is a valid UUID format.
     */
    public static function isValid(string $uuid): bool {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }
}
