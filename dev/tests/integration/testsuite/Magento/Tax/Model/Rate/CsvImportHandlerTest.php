<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Rate_CsvImportHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tax_Model_Rate_CsvImportHandler
     */
    protected $_importHandler;

    protected function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_importHandler = $objectManager->create('Magento_Tax_Model_Rate_CsvImportHandler');
    }

    protected function tearDown()
    {
        $this->_importHandler = null;
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testImportFromCsvFileWithCorrectData()
    {
        $importFileName = __DIR__ . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'correct_rates_import_file.csv';
        $this->_importHandler->importFromCsvFile(array('tmp_name' => $importFileName));

        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        // assert that both tax rates, specified in import file, have been imported correctly
        $importedRuleCA = $objectManager->create('Magento_Tax_Model_Calculation_Rate')
            ->loadByCode('US-CA-*-Rate Import Test');
        $this->assertNotEmpty($importedRuleCA->getId());
        $this->assertEquals(8.25, (float)$importedRuleCA->getRate());
        $this->assertEquals('US', $importedRuleCA->getTaxCountryId());

        $importedRuleFL = $objectManager->create('Magento_Tax_Model_Calculation_Rate')
            ->loadByCode('US-FL-*-Rate Import Test');
        $this->assertNotEmpty($importedRuleFL->getId());
        $this->assertEquals(15, (float)$importedRuleFL->getRate());
        $this->assertEquals('US', $importedRuleFL->getTaxCountryId());
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage One of the countries has invalid code.
     */
    public function testImportFromCsvFileThrowsExceptionWhenCountryCodeIsInvalid()
    {
        $importFileName = __DIR__ . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'rates_import_file_incorrect_country.csv';
        $this->_importHandler->importFromCsvFile(array('tmp_name' => $importFileName));
    }
}
