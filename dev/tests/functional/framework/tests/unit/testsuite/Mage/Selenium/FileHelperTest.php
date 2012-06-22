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

/**
 * Unit test for File helper
 */
class Mage_Selenium_Helper_FileTest extends Mage_PHPUnit_TestCase
{
    /**
     * Selenium FileHelper instance
     *
     * @var Mage_Selenium_Helper_File
     */
    protected $_fileHelper = null;

    public function  __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_fileHelper = new Mage_Selenium_Helper_File($this->_config);
    }

    /**
     * Testing Mage_Selenium_Helper_File::loadYamlFile()
     */
    public function testLoadYamlFile()
    {
        $customers = $this->_fileHelper->loadYamlFile(SELENIUM_TESTS_BASEDIR.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'Customers.yml');

        $this->assertInternalType('array', $customers);
        $this->assertNotEmpty($customers);
        $this->assertGreaterThanOrEqual(5, count($customers));
        $this->assertArrayHasKey('customer_account_register', $customers);
        $this->assertArrayHasKey('generic_customer_account', $customers);
        $this->assertArrayHasKey('all_fields_customer_account', $customers);
        $this->assertArrayHasKey('generic_address', $customers);
        $this->assertArrayHasKey('all_fields_address', $customers);
        $this->assertArrayHasKey('first_name', $customers['customer_account_register']);

        $this->assertFalse($this->_fileHelper->loadYamlFile(''));
        $this->assertFalse($this->_fileHelper->loadYamlFile('some_file.yml'));
    }

    /**
     * Test Mage_Selenium_Helper_File::loadYamlFile() wrong file's type loading
     *
     * @expectedException InvalidArgumentException
     */
    public function testLoadYamlFileException()
    {
        $this->assertFalse($this->_fileHelper->loadYamlFile(SELENIUM_TESTS_BASEDIR.DIRECTORY_SEPARATOR.'phpunit.xml'));
    }

    /**
     * Testing Mage_Selenium_Helper_File::loadYamlFiles()
     */
    public function testLoadYamlFiles()
    {
        $allYmlData = $this->_fileHelper->loadYamlFiles(SELENIUM_TESTS_BASEDIR.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'*.yml');

        $this->assertInternalType('array', $allYmlData);
        $this->assertNotEmpty($allYmlData);
        $this->assertGreaterThanOrEqual(25, count($allYmlData));

        $this->assertEmpty($this->_fileHelper->loadYamlFiles(''));
        $this->assertEmpty($this->_fileHelper->loadYamlFiles('*.yml'));
    }

}
