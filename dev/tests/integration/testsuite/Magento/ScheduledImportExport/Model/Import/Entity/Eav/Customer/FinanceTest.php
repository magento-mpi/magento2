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
 * Test class for Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance
 */
class Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_FinanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Remove test website
     */
    protected function tearDown()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /** @var $testWebsite Magento_Core_Model_Website */
        $testWebsite = $objectManager->get('Magento_Core_Model_Registry')
            ->registry('Magento_ScheduledImportExport_Model_Website');
        if ($testWebsite) {
            // Clear test website info from application cache.
            Mage::app()->clearWebsiteCache($testWebsite->getId());
        }
    }

    /**
     * Test import data method
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance_all_cases.php
     * @magentoDataFixture Magento/ScheduledImportExport/_files/website.php
     *
     * @covers Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::_importData
     * @covers Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::_updateRewardPoints
     * @covers Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::_updateCustomerBalance
     * @covers Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::_getComment
     */
    public function testImportData()
    {
        /**
         * Try to get test website instance,
         * in this case test website will be added into protected property of Application instance class.
         */
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /** @var $testWebsite Magento_Core_Model_Website */
        $testWebsite = $objectManager->get('Magento_Core_Model_Registry')
            ->registry('Magento_ScheduledImportExport_Model_Website');
        Mage::app()->getWebsite($testWebsite->getId());

        // load websites to have ability get website code by id.
        $websiteCodes = array();
        /** @var $website Magento_Core_Model_Website */
        foreach (Mage::app()->getWebsites() as $website) {
            $websiteCodes[$website->getId()] = $website->getCode();
        }

        $userName = 'TestAdmin';
        $user = new Magento_Object(array(
            'username' => $userName
        ));
        /** @var $session Magento_Backend_Model_Auth_Session */
        $session = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Backend_Model_Auth_Session');
        $session->setUser($user);

        $pathToCsvFile = __DIR__ . '/../_files/customer_finance.csv';
        $expectedFinanceData = $this->_csvToArray(file_get_contents($pathToCsvFile));

        $source = new Magento_ImportExport_Model_Import_Source_Csv($pathToCsvFile);
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance');
        $model->setParameters(
            array('behavior' => Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE)
        );
        $model->setSource($source);
        $model->validateData();
        $model->importData();

        $rewardPointsKey =
            Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS;
        $customerBalanceKey =
            Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_CUSTOMER_BALANCE;

        $customerCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Customer_Model_Resource_Customer_Collection');
        /** @var $customer Magento_Customer_Model_Customer */
        foreach ($customerCollection as $customer) {
            $rewardCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->create('Magento_Reward_Model_Resource_Reward_Collection');
            $rewardCollection->addFieldToFilter('customer_id', $customer->getId());
            /** @var $rewardPoints Magento_Reward_Model_Reward */
            foreach ($rewardCollection as $rewardPoints) {
                $websiteCode = $websiteCodes[$rewardPoints->getWebsiteId()];
                $expected = $expectedFinanceData[$customer->getEmail()][$websiteCode][$rewardPointsKey];
                if ($expected < 0) {
                    $expected = 0;
                }
                $this->assertEquals(
                    $expected,
                    $rewardPoints->getPointsBalance(),
                    'Reward points value was not updated'
                );
            }

            $customerBalance = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->create('Magento_CustomerBalance_Model_Resource_Balance_Collection');
            $customerBalance->addFieldToFilter('customer_id', $customer->getId());
            /** @var $balance Magento_CustomerBalance_Model_Balance */
            foreach ($customerBalance as $balance) {
                $websiteCode = $websiteCodes[$balance->getWebsiteId()];
                $expected = $expectedFinanceData[$customer->getEmail()][$websiteCode][$customerBalanceKey];
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
     * Test import data method
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customers_for_finance_import_delete.php
     *
     * @covers Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::_importData
     * @covers Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::_deleteRewardPoints
     * @covers Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::_deleteCustomerBalance
     */
    public function testImportDataDelete()
    {
        /* clean up the database from prior tests before importing */
        $rewards  = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Reward_Model_Resource_Reward_Collection');
        foreach ($rewards as $reward) {
            $reward->delete();
        }

        $source = new Magento_ImportExport_Model_Import_Source_Csv(__DIR__ . '/../_files/customer_finance_delete.csv');
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance');
        $model->setParameters(
            array('behavior' => Magento_ImportExport_Model_Import::BEHAVIOR_DELETE)
        );
        $model->setSource($source);
        $model->validateData();
        $model->importData();

        $rewards  = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Reward_Model_Resource_Reward_Collection');
        $balances = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_CustomerBalance_Model_Resource_Balance_Collection');
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $expectedRewards = $objectManager->get('Magento_Core_Model_Registry')
            ->registry('_fixture/Magento_ScheduledImportExport_Customers_ExpectedRewards');
        /** @var $reward Magento_Reward_Model_Reward */
        foreach ($rewards as $reward) {
            $this->assertEquals(
                $reward->getPointsBalance(),
                $expectedRewards[$reward->getCustomerId()][$reward->getWebsiteId()]
            );
        }

        $expectedBalances = $objectManager->get('Magento_Core_Model_Registry')
            ->registry('_fixture/Magento_ScheduledImportExport_Customers_ExpectedBalances');
        /** @var $balance Magento_CustomerBalance_Model_Balance */
        foreach ($balances as $balance) {
            $this->assertEquals(
                $balance->getAmount(),
                $expectedBalances[$balance->getCustomerId()][$balance->getWebsiteId()]
            );
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
        $emailKey = Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL;
        $websiteKey = Magento_ScheduledImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE;

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
