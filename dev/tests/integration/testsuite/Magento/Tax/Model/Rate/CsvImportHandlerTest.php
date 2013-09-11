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
     * @var \Magento\Tax\Model\Rate\CsvImportHandler
     */
    protected $_importHandler;

    protected function setUp()
    {
        $this->_importHandler = Mage::getModel('Magento\Tax\Model\Rate\CsvImportHandler');
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

        // assert that both tax rates, specified in import file, have been imported correctly
        $importedRuleCA = Mage::getModel('Magento\Tax\Model\Calculation\Rate')->loadByCode('US-CA-*-Rate Import Test');
        $this->assertNotEmpty($importedRuleCA->getId());
        $this->assertEquals(8.25, (float)$importedRuleCA->getRate());
        $this->assertEquals('US', $importedRuleCA->getTaxCountryId());

        $importedRuleFL = Mage::getModel('Magento\Tax\Model\Calculation\Rate')->loadByCode('US-FL-*-Rate Import Test');
        $this->assertNotEmpty($importedRuleFL->getId());
        $this->assertEquals(15, (float)$importedRuleFL->getRate());
        $this->assertEquals('US', $importedRuleFL->getTaxCountryId());
    }

    /**
     * @magentoDbIsolation enabled
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage One of the countries has invalid code.
     */
    public function testImportFromCsvFileThrowsExceptionWhenCountryCodeIsInvalid()
    {
        $importFileName = __DIR__ . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'rates_import_file_incorrect_country.csv';
        $this->_importHandler->importFromCsvFile(array('tmp_name' => $importFileName));
    }
}
