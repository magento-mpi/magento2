<?php

namespace Magento\Test\Tools\Composer\Helper;

use Magento\TestFramework\Helper\ObjectManager;

class ZipTest extends \PHPUnit_Framework_TestCase {
    protected $zip;

    protected function setUp()
    {
        $objectManagerHelper = new ObjectManager($this);$this->zip = $objectManagerHelper->getObject('\Magento\Tools\Composer\Helper\Zip');
    }

    public function testZip(){
        $source = __DIR__ . '/../_files/lib';
        $destination = __DIR__ . '/../_files/_packages';
        try {
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }
            } catch(\Exception $ex){
        }

        $noOfZips = $this->zip->Zip($source, $destination . "/" . "library.zip");
        $this->assertEquals(sizeof($noOfZips), 1);

    }
}