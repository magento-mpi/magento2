<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Composer\Helper;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class ZipTest
 * @package Magento\Test\Tools\Composer\Helper
 */
class ZipTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Zip
     *
     * @var \Magento\Tools\Composer\Helper\Zip
     */
    protected $zip;

    /**
     * Intial Setup
     * @return void
     */
    protected function setUp()
    {
        $objectManagerHelper = new ObjectManager($this);
        $this->zip = $objectManagerHelper->getObject('\Magento\Tools\Composer\Helper\Zip');
    }

    /**
     * Test Zip
     * @return void
     */
    public function testZip()
    {
        $source = __DIR__ . '/../_files/lib';
        $destination = __DIR__ . '/../_files/_packages';
        try {
            if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
            }
        }
        catch (\Exception $ex) {
        }

        $noOfZips = $this->zip->Zip($source, $destination . "/" . "library.zip");
        $this->assertEquals(sizeof($noOfZips), 1);

    }
}