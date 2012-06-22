<?php

/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_Helper_FileTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $this->assertInstanceOf('Mage_Selenium_Helper_File', $fileHelper);
    }

    /**
     * @covers Mage_Selenium_Helper_File::loadYamlFile
     * @depends test__construct
     */
    public function testLoadYamlFile()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $filePath = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'fixture' . DIRECTORY_SEPARATOR . 'default'
                . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Mage' . DIRECTORY_SEPARATOR . 'Customer'
                . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'Customers.yml';
        $customers = $fileHelper->loadYamlFile($filePath);

        $this->assertInternalType('array', $customers);
        $this->assertNotEmpty($customers);
        $this->assertGreaterThanOrEqual(5, count($customers));
        $this->assertArrayHasKey('customer_account_register', $customers);
        $this->assertArrayHasKey('generic_customer_account', $customers);
        $this->assertArrayHasKey('all_fields_customer_account', $customers);
        $this->assertArrayHasKey('generic_address', $customers);
        $this->assertArrayHasKey('all_fields_address', $customers);
        $this->assertArrayHasKey('first_name', $customers['customer_account_register']);

        $this->assertFalse($fileHelper->loadYamlFile(''));
        $this->assertFalse($fileHelper->loadYamlFile('some_file.yml'));
    }

    /**
     * @covers Mage_Selenium_Helper_File::loadYamlFile
     * @depends test__construct
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadYamlFileException()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $this->assertFalse($fileHelper->loadYamlFile(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'phpunit.xml'));
    }

    /**
     * @covers Mage_Selenium_Helper_File::loadYamlFiles
     * @depends test__construct
     */
    public function testLoadYamlFiles()
    {
        $fileHelper = new Mage_Selenium_Helper_File($this->_config);
        $filePath = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'fixture' . DIRECTORY_SEPARATOR . 'default'
                . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*'
                . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . '*.yml';
        $allYmlData = $fileHelper->loadYamlFiles($filePath);

        $this->assertInternalType('array', $allYmlData);
        $this->assertNotEmpty($allYmlData);
        $this->assertGreaterThanOrEqual(25, count($allYmlData));

        $this->assertEmpty($fileHelper->loadYamlFiles(''));
        $this->assertEmpty($fileHelper->loadYamlFiles('*.yml'));
    }
}