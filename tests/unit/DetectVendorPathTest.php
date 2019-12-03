<?php

use BigBIT\DIBootstrap\Bootstrap;
use BigBIT\DIBootstrap\Exceptions\VendorPathNotFoundException;


/**
 * Class DetectVendorPathTest
 */
class DetectVendorPathTest extends TestCase
{
    public function testDetection() {
        try {
            $ok = true;
            Bootstrap::detectVendorPath();
        }
        catch (\Throwable $t) {
            $ok = false;
        }

        $this->assertTrue($ok, 'vendor detected');
    }

    public function testInvalidDirDetection() {
        try {
            $ok = true;
            Bootstrap::detectVendorPath(sys_get_temp_dir());
        }
        catch (\Throwable $t) {
            $ok = false;
            $this->assertEquals(VendorPathNotFoundException::class, get_class($t), 'correct exception');
        }

        $this->assertFalse($ok, 'vendor not detected');
    }
}