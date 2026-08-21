<?php

namespace ClinicFlow\Tests;

use PHPUnit\Framework\TestCase;
use ClinicFlow\Utils\Encryption;

class EncryptionTest extends TestCase {
    public function testEncryptionAndDecryption(): void {
        $plain = "Subjective: Patient reports severe cough and fever for 3 days.";
        $encrypted = Encryption::encrypt($plain);

        $this->assertNotEquals($plain, $encrypted);
        $this->assertStringStartsWith('enc_gcm:', $encrypted);

        $decrypted = Encryption::decrypt($encrypted);
        $this->assertEquals($plain, $decrypted);
    }

    public function testPlaintextFallback(): void {
        $plain = "Legacy unencrypted notes string";
        $decrypted = Encryption::decrypt($plain);
        $this->assertEquals($plain, $decrypted);
    }

    public function testEmptyHandling(): void {
        $this::assertNull(Encryption::encrypt(null));
        $this::assertNull(Encryption::decrypt(null));
        $this::assertEquals('', Encryption::encrypt(''));
        $this::assertEquals('', Encryption::decrypt(''));
    }
}
