<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer\Helper;

/**
 * Class ZipTest
 * @package Magento\Test\Tools\Composer\Helper
 */
class ZipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Intial Setup
     * @return void
     */

    protected function setUp()
    {
        $destination = str_replace('\\', '/', realpath(__DIR__ . '/..') . '/_files/_packages');
        try {
            if (file_exists($destination . "/" . "library.zip")) {
                unlink($destination . "/" . "library.zip");
            }
        }
        catch (\Exception $ex) {
        }
    }

    /**
     * Test Zip
     * @return void
     */
    public function testZip()
    {
        $source = str_replace('\\', '/', realpath(__DIR__ . '/..' . '/_files/app'));
        $destination = str_replace('\\', '/', realpath(__DIR__ . '/..') . '/_files/_packages');

        try {
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
        }
        catch (\Exception $ex) {
        }

        $noOfZips = \Magento\Tools\Composer\Helper\Zip::Zip($source, $destination . "/" . "library.zip", array());
        $this->assertTrue(file_exists($destination . "/" . "library.zip"));
        $this->assertEquals(sizeof($noOfZips), 1);
    }

    /**
     * Test Zip
     * @return void
     */

    public function testZipExclude()
    {
        $source = str_replace('\\', '/', realpath(__DIR__ . '/..' . '/_files/app'));
        $destination = str_replace('\\', '/', realpath(__DIR__ . '/..') . '/_files/_packages');
        try {
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
        }
        catch (\Exception $ex) {
        }

        $exclude = array(
            realpath(__DIR__ . '/..') . '/_files/app/code/Magento/OtherModule'
        );

        \Magento\Tools\Composer\Helper\Zip::Zip($source, $destination . "/" . "library.zip", $exclude);
        $this->assertTrue(file_exists($destination . "/" . "library.zip"));

        $za = new \ZipArchive();

        $za->open($destination . "/" . "library.zip");

        $found = false;

        for ($i = 0; $i < $za->numFiles; $i++) {
            $stat = $za->statIndex($i);
            if (in_array($source . "/" . $stat['name'], $exclude)) {
                $found = true;
            }
        }

        $this->assertFalse($found);
    }
}