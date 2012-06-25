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
 * Test class for Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance
 */
class Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_FinanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that method returns correct class instance
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::_getAttributeCollection()
     */
    public function testGetAttributeCollection()
    {
        $customerFinance = new Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance();
        $method = new ReflectionMethod($customerFinance, '_getAttributeCollection');
        $method->setAccessible(true);

        $this->assertInstanceOf('Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection',
            $method->invoke($customerFinance)
        );
    }

    /**
     * Test import data method
     *
     * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance_all_cases.php
     * @magentoDataFixture Enterprise/ImportExport/_files/website.php
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::_importData()
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::_updateRewardPoints()
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::_updateCustomerBalance()
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::_getComment()
     */
    public function testImportData()
    {
        /**
         * Try to get test website instance,
         * in this case test website will be added into protected property of Application instance class.
         */
        /** @var $testWebsite Mage_Core_Model_Website */
        $testWebsite = Mage::registry('Enterprise_ImportExport_Model_Website');
        Mage::app()->getWebsite($testWebsite->getId());

        // load websites to have ability get website code by id.
        $websiteCodes = array();
        /** @var $website Mage_Core_Model_Website */
        foreach (Mage::app()->getWebsites() as $website) {
            $websiteCodes[$website->getId()] = $website->getCode();
        }

        $userName = 'TestAdmin';
        $user = new Varien_Object(array(
            'username' => $userName
        ));
        /** @var $session Mage_Backend_Model_Auth_Session */
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $session->setUser($user);

        $pathToCsvFile = __DIR__ . '/../_files/customer_finance.csv';
        $expectedFinanceData = $this->_csvToArray(file_get_contents($pathToCsvFile));

        $source = new Mage_ImportExport_Model_Import_Adapter_Csv($pathToCsvFile);
        $model = new Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance();
        $model->setSource($source);
        $model->validateData();
        $model->importData();

        /** @var $customers Mage_Customer_Model_Resource_Customer_Collection */
        $customers = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection');
        /** @var $customer Mage_Customer_Model_Customer */
        foreach ($customers as $customer) {
            /** @var $rewardCollection Enterprise_Reward_Model_Resource_Reward_Collection */
            $rewardCollection = Mage::getResourceModel('Enterprise_Reward_Model_Resource_Reward_Collection');
            $rewardCollection->addFieldToFilter('customer_id', $customer->getId());
            /** @var $rewardPoints Enterprise_Reward_Model_Reward */
            foreach ($rewardCollection as $rewardPoints) {
                $websiteCode = $websiteCodes[$rewardPoints->getWebsiteId()];
                $expected = $expectedFinanceData[$customer->getEmail()][$websiteCode]['reward_points'];
                if ($expected < 0) {
                    $expected = 0;
                }
                $this->assertEquals(
                    $expected,
                    $rewardPoints->getPointsBalance(),
                    'Reward points value was not updated'
                );
            }

            /** @var $customerBalance Enterprise_CustomerBalance_Model_Resource_Balance_Collection */
            $customerBalance = Mage::getResourceModel('Enterprise_CustomerBalance_Model_Resource_Balance_Collection');
            $customerBalance->addFieldToFilter('customer_id', $customer->getId());
            /** @var $balance Enterprise_CustomerBalance_Model_Balance */
            foreach ($customerBalance as $balance) {
                $websiteCode = $websiteCodes[$balance->getWebsiteId()];
                $expected = $expectedFinanceData[$customer->getEmail()][$websiteCode]['store_credit'];
                if ($expected < 0) {
                    $expected = 0;
                }
                $this->assertEquals(
                    $expected,
                    $balance->getAmount(),
                    'Customer balance value was not updated'
                );
            }
        }
    }

    /**
     * Export CSV finance data to array
     *
     * @param string $content
     * @return array
     */
    protected function _csvToArray($content)
    {
        $emailKey = Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_EMAIL;
        $websiteKey = Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE;

        $header = array();
        $data = array();
        $lines = str_getcsv($content, "\n");
        foreach ($lines as $index => $line) {
            if ($index == 0) {
                $header = str_getcsv($line);
            } else {
                $row = array_combine($header, str_getcsv($line));
                if (!isset($data[$row[$emailKey]])) {
                    $data[$row[$emailKey]] = array();
                }
                $data[$row[$emailKey]][$row[$websiteKey]] = $row;
            }
        }
        return $data;
    }
}
