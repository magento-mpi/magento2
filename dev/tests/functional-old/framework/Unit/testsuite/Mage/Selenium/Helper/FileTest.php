<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Selenium_Helper_FileTest extends Unit_PHPUnit_TestCase
{
    /**
     * Selenium FileHelper instance
     *
     * @var Mage_Selenium_Helper_File
     */
    private $_fileHelper;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_fileHelper = $this->_testConfig->getHelper('file');
    }

    /**
     * Testing Mage_Selenium_Helper_File::loadYamlFile()
     */
    public function testLoadYamlFile()
    {
        $customers = $this->_fileHelper->loadYamlFile(
            SELENIUM_TESTS_BASEDIR . '/fixture/default/core/Mage/UnitTest/data/UnitTestsData.yml'
        );
        $this->assertInternalType('array', $customers);
        $this->assertNotEmpty($customers);
        $this->assertGreaterThanOrEqual(3, count($customers));
        $this->assertArrayHasKey('unit_test_load_data', $customers);
        $this->assertArrayHasKey('unit_test_load_data_set_simple', $customers);
        $this->assertArrayHasKey('unit_test_load_data_set_recursive', $customers);

        $this->assertEmpty($this->_fileHelper->loadYamlFile(''));
        $this->assertEmpty($this->_fileHelper->loadYamlFile('some_file.yml'));
    }

    /**
     * Test Mage_Selenium_Helper_File::loadYamlFile() wrong file's type loading
     */
    public function testLoadYamlFileException()
    {
        $this->assertEquals(array(), $this->_fileHelper->loadYamlFile(SELENIUM_TESTS_BASEDIR . '/phpunit.xml'));
    }

    /**
     * Testing Mage_Selenium_Helper_File::loadYamlFiles()
     */
    public function testLoadYamlFiles()
    {
        $allYmlData = $this->_fileHelper->loadYamlFiles(
            SELENIUM_TESTS_BASEDIR . '/fixture/default/core/Mage/UnitTest/data/*.yml'
        );

        $this->assertInternalType('array', $allYmlData);
        $this->assertNotEmpty($allYmlData);
        $this->assertGreaterThanOrEqual(6, count($allYmlData));

        $this->assertEmpty($this->_fileHelper->loadYamlFiles(''));
        $this->assertEmpty($this->_fileHelper->loadYamlFiles('*.yml'));
    }
}
