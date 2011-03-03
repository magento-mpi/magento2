<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium unit tests
 * @subpackage  Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    public function  __construct() {
        parent::__construct();
        $this->_fileHelper = new Mage_Selenium_Helper_File($this->_config);
    }

    /**
     * Testing Mage_Selenium_Helper_File::loadYamlFile()
     */
    public function test_loadYamlFile()
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
    public function test_loadYamlFileException()
    {
        $this->assertFalse($this->_fileHelper->loadYamlFile(SELENIUM_TESTS_BASEDIR.DIRECTORY_SEPARATOR.'phpunit.xml'));
    }

    /**
     * Testing Mage_Selenium_Helper_File::loadYamlFiles()
     */
    public function test_loadYamlFiles()
    {
        $all_yml_data = $this->_fileHelper->loadYamlFiles(SELENIUM_TESTS_BASEDIR.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'*.yml');

        $this->assertInternalType('array', $all_yml_data);
        $this->assertNotEmpty($all_yml_data);
        $this->assertGreaterThanOrEqual(25, count($all_yml_data));

        $this->assertEmpty($this->_fileHelper->loadYamlFiles(''));
        $this->assertEmpty($this->_fileHelper->loadYamlFiles('*.yml'));
    }

}
