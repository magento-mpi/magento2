<?php

namespace Magento\Test\Tools\Composer\Helper;

use Magento\TestFramework\Helper\ObjectManager;

/**
 * Class ConverterTest
 * @package Magento\Test\Tools\Composer\Helper
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Converter
     *
     * @var \Magento\Tools\Composer\Helper\Converter
     */
    protected $converter;

    /**
     * Initial Setup
     * @return void
     */
    protected function setUp()
    {
        $objectManagerHelper = new ObjectManager($this);
        $this->converter = $objectManagerHelper->getObject('\Magento\Tools\Composer\Helper\Converter');
    }

    /**
     * Test Name to Vendor Package
     * @return void
     */
    public function testnametoVendorPackage()
    {
        $inputs = array("Vendor_Package", "Vendor/Package", "Vendor\\Package", "Vendor".DIRECTORY_SEPARATOR."Package");
        foreach ($inputs as $input) {
            $output = $this->converter->nametoVendorPackage($input);
            $this->assertEquals($output, "Vendor/Package");
        }
        $inputs = array("VendorPackage", "Vendor/Packager/Somethingelse");
        foreach ($inputs as $input) {
            try{
                $output = $this->converter->nametoVendorPackage($input);
                echo $output;
                $this->fail("It should not have reached here. It should have threw an exception");
            } catch(\Exception $ex){
                $this->assertEquals($ex->getCode(), 1);
                $this->assertTrue($this->startsWith($ex->getMessage(), "Not a valid name:"));
            }
        }
    }

    /**
     * Test Vendor Package to Name
     * @return void
     */
    public function testvendorPackagetoName()
    {
        $inputs = array("Vendor/Package", "Vendor\Package", "Vendor\\Package", "Vendor".DIRECTORY_SEPARATOR."Package");
        foreach ($inputs as $input) {
            $output = $this->converter->vendorPackagetoName($input);
            $this->assertEquals($output, "Vendor_Package");
        }

        $inputs = array("Vendor_Package", "Vendor/Packager/Somethingelse");
        foreach ($inputs as $input) {
            try{
                $output = $this->converter->vendorPackagetoName($input);
                echo $output;
                $this->fail("It should not have reached here. It should have threw an exception");
            } catch(\Exception $ex){
                $this->assertEquals($ex->getCode(), 1);
                $this->assertTrue($this->startsWith($ex->getMessage(), "Not a valid vendorPackage:"));
            }
        }

    }

    /**
     * Helper method for starts with
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }
}