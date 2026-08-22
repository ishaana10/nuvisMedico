<?php

namespace ClinicFlow\Tests;

use PHPUnit\Framework\TestCase;
use ClinicFlow\Utils\Uuid;

class UuidTest extends TestCase {
    public function testUuidv7Generation(): void {
        $uuid1 = Uuid::uuidv7();
        $uuid2 = Uuid::uuidv7();

        $this->assertNotEquals($uuid1, $uuid2);
        $this->assertTrue(Uuid::isValid($uuid1));
        $this->assertTrue(Uuid::isValid($uuid2));
        $this->assertEquals(36, strlen($uuid1));
    }
}
