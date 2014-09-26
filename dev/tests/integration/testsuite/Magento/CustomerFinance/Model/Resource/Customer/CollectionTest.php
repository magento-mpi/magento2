<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerFinance\Model\Resource\Customer;
use \Magento\CustomerFinance\Model\Resource\Customer\Attribute\Finance\Collection as FinanceAttributeCollection;
/**
 * Test collection \Magento\CustomerFinance\Model\Resource\Customer\Collection
 *
 * @magentoConfigFixture current_store magento_reward/general/is_enabled            1
 * @magentoConfigFixture current_store customer/magento_customerbalance/is_enabled  1
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Remove not used websites
     */
    protected function tearDown()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->reinitStores();
    }

    /**
     * Test join with reward points
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance.php
     */
    public function testJoinWithRewardPoints()
    {
        /** @var $collection \Magento\CustomerFinance\Model\Resource\Customer\Collection */
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Model\Resource\Customer\Collection'
        );
        $collection->joinWithRewardPoints();
        $items = $collection->getItems();
        $this->assertCount(1, $items);

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = reset($items);
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $website \Magento\Store\Model\Website */
        $websites = $objectManager->get('Magento\Framework\StoreManagerInterface')->getWebsites();
        foreach ($websites as $website) {
            $key = $website->getCode() . '_' . FinanceAttributeCollection::COLUMN_REWARD_POINTS;
            $rewardPoints = $customer->getData($key);
            $this->assertNotEmpty($rewardPoints);
            $this->assertEquals(
                $objectManager->get('Magento\Framework\Registry')
                    ->registry('reward_point_balance_' . $website->getCode()),
                $rewardPoints
            );
        }
    }

    /**
     * Test join with customer balance
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance.php
     */
    public function testJoinWithCustomerBalance()
    {
        /** @var $collection \Magento\CustomerFinance\Model\Resource\Customer\Collection */
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Model\Resource\Customer\Collection'
        );
        $collection->joinWithCustomerBalance();
        $items = $collection->getItems();
        $this->assertCount(1, $items);

        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = reset($items);
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $websites = $objectManager->get('Magento\Framework\StoreManagerInterface')->getWebsites();
        /** @var $website \Magento\Store\Model\Website */
        foreach ($websites as $website) {
            $key = $website->getCode() . '_' . FinanceAttributeCollection::COLUMN_CUSTOMER_BALANCE;
            $customerBalance = $customer->getData($key);
            $this->assertNotEmpty($customerBalance);
            $this->assertEquals(
                $objectManager->get('Magento\Framework\Registry')->registry('customer_balance_' . $website->getCode()),
                $customerBalance
            );
        }
    }

    /**
     * Test filter with reward points and customer balance
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance_all_cases.php
     */
    public function testFilterWithRewardPointsAndCustomerBalance()
    {
        /** @var $collection \Magento\CustomerFinance\Model\Resource\Customer\Collection */
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Model\Resource\Customer\Collection'
        );
        $collection->joinWithCustomerBalance()->joinWithRewardPoints();
        $items = $collection->getItems();
        $this->assertCount(3, $items);

        $emails = array();
        foreach ($items as $item) {
            $emails[] = $item->getEmail();
        }
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_rp_cb'),
            $emails
        );
        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_rp'),
            $emails
        );
        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_cb'),
            $emails
        );
        $this->assertNotContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email'),
            $emails
        );
    }

    /**
     * Test filter only with reward points
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance_all_cases.php
     */
    public function testFilterWithRewardPoints()
    {
        /** @var $collection \Magento\CustomerFinance\Model\Resource\Customer\Collection */
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Model\Resource\Customer\Collection'
        );
        $collection->joinWithRewardPoints();
        $items = $collection->getItems();
        $this->assertCount(2, $items);

        $emails = array();
        foreach ($items as $item) {
            $emails[] = $item->getEmail();
        }
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_rp_cb'),
            $emails
        );
        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_rp'),
            $emails
        );
        $this->assertNotContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_cb'),
            $emails
        );
        $this->assertNotContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email'),
            $emails
        );
    }

    /**
     * Test filter only with customer balance
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance_all_cases.php
     */
    public function testFilterWithCustomerBalance()
    {
        /** @var $collection \Magento\CustomerFinance\Model\Resource\Customer\Collection */
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Model\Resource\Customer\Collection'
        );
        $collection->joinWithCustomerBalance();
        $items = $collection->getItems();
        $this->assertCount(2, $items);

        $emails = array();
        foreach ($items as $item) {
            $emails[] = $item->getEmail();
        }
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_rp_cb'),
            $emails
        );
        $this->assertNotContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_rp'),
            $emails
        );
        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_cb'),
            $emails
        );
        $this->assertNotContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email'),
            $emails
        );
    }

    /**
     * Test filter only without customer balance and reward points
     *
     * @magentoDataFixture Magento/ScheduledImportExport/_files/customer_finance_all_cases.php
     */
    public function testFilterWithoutRewardPointsAndCustomerBalance()
    {
        /** @var $collection \Magento\CustomerFinance\Model\Resource\Customer\Collection */
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CustomerFinance\Model\Resource\Customer\Collection'
        );
        $items = $collection->getItems();
        $this->assertCount(4, $items);

        $emails = array();
        foreach ($items as $item) {
            $emails[] = $item->getEmail();
        }

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_rp_cb'),
            $emails
        );
        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_rp'),
            $emails
        );
        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email_cb'),
            $emails
        );
        $this->assertContains(
            $objectManager->get('Magento\Framework\Registry')->registry('customer_finance_email'),
            $emails
        );
    }
}
