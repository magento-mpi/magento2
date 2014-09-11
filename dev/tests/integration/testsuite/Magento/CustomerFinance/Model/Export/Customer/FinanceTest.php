<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerFinance\Model\Export\Customer;
use Magento\CustomerFinance\Model\Resource\Customer\Attribute\Finance\Collection as FinanceAttributeCollection;

/**
 * @magentoConfigFixture current_store magento_reward/general/is_enabled            1
 * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  1
 */
class FinanceTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->reinitStores();
    }

    /**
     * Test export data
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance.php
     */
    public function testExport()
    {
        $customerFinance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Model\Export\Customer\Finance'
        );
        $customerFinance->setWriter(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                'Magento\ImportExport\Model\Export\Adapter\Csv'
            )
        );
        $customerFinance->setParameters(array());
        $csvExportString = $customerFinance->export();

        // get data from CSV file
        list($csvHeader, $csvData) = $this->_getCsvData($csvExportString);
        $this->assertCount(
            count(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\Framework\StoreManagerInterface'
                )->getWebsites()
            ),
            $csvData
        );

        // prepare correct header
        $correctHeader = $customerFinance->getPermanentAttributes();
        $attributeCollection = $customerFinance->getAttributeCollection();
        foreach ($customerFinance->filterAttributeCollection($attributeCollection) as $attribute) {
            /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
            $correctHeader[] = $attribute->getAttributeCode();
        }

        sort($csvHeader);
        sort($correctHeader);
        $this->assertEquals($correctHeader, $csvHeader);

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $websites = $objectManager->get('Magento\Framework\StoreManagerInterface')->getWebsites();
        /** @var $website \Magento\Store\Model\Website */
        foreach ($websites as $website) {
            $websiteCode = $website->getCode();
            // CSV data
            $csvCustomerData = $this->_getRecordByFinanceWebsite($csvData, $websiteCode);
            $this->assertNotNull($csvCustomerData, 'Customer data for website "' . $websiteCode . '" must exist.');

            // prepare correct data
            $correctCustomerData = array(
                Finance::COLUMN_EMAIL => $objectManager->get(
                    'Magento\Framework\Registry'
                )->registry(
                    'customer_finance_email'
                ),
                Finance::COLUMN_WEBSITE => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\Framework\StoreManagerInterface'
                )->getStore()->getWebsite()->getCode(),
                Finance::COLUMN_FINANCE_WEBSITE => $websiteCode,
                FinanceAttributeCollection::COLUMN_CUSTOMER_BALANCE => $objectManager->get(
                    'Magento\Framework\Registry'
                )->registry(
                    'customer_balance_' . $websiteCode
                ),
                FinanceAttributeCollection::COLUMN_REWARD_POINTS => $objectManager->get(
                    'Magento\Framework\Registry'
                )->registry(
                    'reward_point_balance_' . $websiteCode
                )
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
        $customerFinance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Model\Export\Customer\Finance'
        );
        $attributeCollection = $customerFinance->getAttributeCollection();

        $this->assertInstanceOf(
            'Magento\CustomerFinance\Model\Resource\Customer\Attribute\Finance\Collection',
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
        $financeWebsiteKey = Finance::COLUMN_FINANCE_WEBSITE;
        foreach ($records as $record) {
            if ($record[$financeWebsiteKey] == $website) {
                return $record;
            }
        }
        return null;
    }
}
