<?php

namespace ClinicFlow\Utils;

use RuntimeException;

class Encryption {
    private const CIPHER = 'aes-256-gcm';
    private const PREFIX = 'enc_gcm:';

    public static function getKey(?string $customKey = null): string {
        if ($customKey && strlen($customKey) >= 16) {
            return hash('sha256', $customKey, true);
        }

        if (function_exists('getAppConfig')) {
            $cfg = getAppConfig();
            if (!empty($cfg['app_key'])) {
                return hash('sha256', $cfg['app_key'], true);
            }
        }

        $envKey = getenv('APP_KEY') ?: 'clinicflow_default_secret_encryption_key_2025!';
        return hash('sha256', $envKey, true);
    }

    public static function encrypt(?string $plaintext, ?string $customKey = null): ?string {
        if ($plaintext === null || $plaintext === '') {
            return $plaintext;
        }

        if (str_starts_with($plaintext, self::PREFIX)) {
            return $plaintext; // Already encrypted
        }

        $key = self::getKey($customKey);
        $iv = random_bytes(12); // 96-bit IV for AES-GCM
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        if ($ciphertext === false) {
            throw new RuntimeException("Encryption failed.");
        }

        return self::PREFIX . base64_encode($iv) . ':' . base64_encode($tag) . ':' . base64_encode($ciphertext);
    }

    public static function decrypt(?string $ciphertext, ?string $customKey = null): ?string {
        if ($ciphertext === null || $ciphertext === '') {
            return $ciphertext;
        }

        if (!str_starts_with($ciphertext, self::PREFIX)) {
            return $ciphertext; // Plaintext fallback
        }

        $payload = substr($ciphertext, strlen(self::PREFIX));
        $parts = explode(':', $payload);

        if (count($parts) !== 3) {
            return $ciphertext;
        }

        $iv = base64_decode($parts[0], true);
        $tag = base64_decode($parts[1], true);
        $data = base64_decode($parts[2], true);

        if ($iv === false || $tag === false || $data === false) {
            return $ciphertext;
        }

        $key = self::getKey($customKey);

        $plaintext = openssl_decrypt(
            $data,
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return $plaintext !== false ? $plaintext : $ciphertext;
    }
}
