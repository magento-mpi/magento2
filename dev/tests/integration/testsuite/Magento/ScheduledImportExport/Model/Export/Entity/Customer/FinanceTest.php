<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoConfigFixture current_store magento_reward/general/is_enabled            1
 * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  1
 */
class Magento_ScheduledImportExport_Model_Export_Entity_Customer_FinanceTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->reinitStores();
    }

    /**
     * Test export data
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance.php
     */
    public function testExport()
    {
        $customerFinance = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ScheduledImportExport_Model_Export_Entity_Customer_Finance');
        $customerFinance->setWriter(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ImportExport_Model_Export_Adapter_Csv'));
        $customerFinance->setParameters(array());
        $csvExportString = $customerFinance->export();

        // get data from CSV file
        list($csvHeader, $csvData) = $this->_getCsvData($csvExportString);
        $this->assertCount(
            count(
                Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                    ->get('Magento_Core_Model_StoreManagerInterface')->getWebsites()
            ),
            $csvData
        );

        // prepare correct header
        $correctHeader = $customerFinance->getPermanentAttributes();
        $attributeCollection = $customerFinance->getAttributeCollection();
        foreach ($customerFinance->filterAttributeCollection($attributeCollection) as $attribute) {
            /** @var $attribute Magento_Eav_Model_Entity_Attribute */
            $correctHeader[] = $attribute->getAttributeCode();
        }

        sort($csvHeader);
        sort($correctHeader);
        $this->assertEquals($correctHeader, $csvHeader);

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $websites = $objectManager->get('Magento_Core_Model_StoreManagerInterface')->getWebsites();
        /** @var $website Magento_Core_Model_Website */
        foreach ($websites as $website) {
            $websiteCode = $website->getCode();
            // CSV data
            $csvCustomerData = $this->_getRecordByFinanceWebsite($csvData, $websiteCode);
            $this->assertNotNull($csvCustomerData, 'Customer data for website "' . $websiteCode . '" must exist.');

            // prepare correct data
            $correctCustomerData = array(
                Magento_ScheduledImportExport_Model_Export_Entity_Customer_Finance::COLUMN_EMAIL
                    => $objectManager->get('Magento_Core_Model_Registry')->registry('customer_finance_email'),
                Magento_ScheduledImportExport_Model_Export_Entity_Customer_Finance::COLUMN_WEBSITE
                    => Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                        ->get('Magento_Core_Model_StoreManagerInterface')->getStore()->getWebsite()->getCode(),
                Magento_ScheduledImportExport_Model_Export_Entity_Customer_Finance::COLUMN_FINANCE_WEBSITE
                    => $websiteCode,
                Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_Collection::
                    COLUMN_CUSTOMER_BALANCE
                    => $objectManager->get('Magento_Core_Model_Registry')->registry('customer_balance_' . $websiteCode),
                Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS
                    => $objectManager->get('Magento_Core_Model_Registry')
                        ->registry('reward_point_balance_' . $websiteCode),
            );

            asort($csvCustomerData);
            asort($correctCustomerData);
            $this->assertEquals($correctCustomerData, $csvCustomerData);
        }
    }

    /**
     * Test method testGetAttributeCollection
     */
    public function testGetAttributeCollection()
    {
        $customerFinance = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ScheduledImportExport_Model_Export_Entity_Customer_Finance');
        $attributeCollection = $customerFinance->getAttributeCollection();

        $this->assertInstanceOf(
            'Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_Collection',
            $attributeCollection
        );
    }

    /**
     * Get CSV header and data from string as array (header, data)
     *
     * @param string $csvString
     * @return array
     */
    protected function _getCsvData($csvString)
    {
        list($csvHeaderString, $csvDataString) = explode("\n", $csvString, 2);
        $csvHeader = str_getcsv($csvHeaderString);

        $csvData = explode("\n", $csvDataString);
        foreach ($csvData as $key => $csvRecordString) {
            $csvRecordString = trim($csvRecordString);
            if ($csvRecordString) {
                $csvRecord = str_getcsv($csvRecordString);
                $csvRecord = array_combine($csvHeader, $csvRecord);
                $csvData[$key] = $csvRecord;
            } else {
                unset($csvData[$key]);
            }
        }

        return array($csvHeader, $csvData);
    }

    /**
     * Get record by finance data
     *
     * @param array $records
     * @param string $website
     * @return array|null
     */
    protected function _getRecordByFinanceWebsite(array $records, $website)
    {
        $financeWebsiteKey = Magento_ScheduledImportExport_Model_Export_Entity_Customer_Finance::COLUMN_FINANCE_WEBSITE;
        foreach ($records as $record) {
            if ($record[$financeWebsiteKey] == $website) {
                return $record;
            }
        }
        return null;
    }
}
