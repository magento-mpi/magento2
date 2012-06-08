<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoConfigFixture modules/Enterprise_Reward/active          1
 * @magentoConfigFixture modules/Enterprise_CustomerBalance/active 1
 */
class Enterprise_ImportExport_Model_Export_Entity_V2_Customer_FinanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test export data
     *
     * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance.php
     */
    public function testExport()
    {
        $validWriters = Mage_ImportExport_Model_Config::getModels(Mage_ImportExport_Model_Export::CONFIG_KEY_FORMATS);

        /** @var $customerFinance Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance */
        $customerFinance = Mage::getModel('Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance');
        $customerFinance->setWriter(Mage::getModel($validWriters['csv']['model']));
        $csvExportString = $customerFinance->export();

        // fixture contains only one customer record
        list($csvHeaderString, $csvDataString) = explode("\n", $csvExportString, 2);
        $csvHeader = explode(',', $csvHeaderString);
        $csvData = explode(',', $csvDataString);
        $csvData = array_combine($csvHeader, $csvData);

        // prepare correct header
        $correctHeader = $customerFinance->getPermanentAttributes();
        $attributeCollection = $customerFinance->getAttributeCollection();
        foreach ($customerFinance->filterAttributeCollection($attributeCollection) as $attribute) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute */
            $correctHeader[] = $attribute->getAttributeCode();
        }

        sort($csvHeader);
        sort($correctHeader);
        $this->assertEquals($correctHeader, $csvHeader);

        // prepare correct data
        $correctData = array();
        $correctData[Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance::COL_EMAIL] = 'test@test.com';
        $correctData[Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance::COL_WEBSITE]
            = Mage::app()->getStore()->getWebsite()->getCode();
        $key = Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COL_CUSTOMER_BALANCE;
        $correctData[$key] = 100;
        $key = Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COL_REWARD_POINTS;
        $correctData[$key] = 50;

        asort($csvData);
        asort($correctData);
        $this->assertEquals($correctHeader, $csvHeader);
    }

    /**
     * Test method testGetAttributeCollection
     */
    public function testGetAttributeCollection()
    {
        /** @var $customerFinance Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance */
        $customerFinance = Mage::getModel('Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance');
        $attributeCollection = $customerFinance->getAttributeCollection();

        $this->assertInstanceOf(
            'Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection',
            $attributeCollection
        );
    }
}
