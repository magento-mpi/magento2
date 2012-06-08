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
 * Test collection Enterprise_ImportExport_Model_Resource_Customer_Collection
 *
 * @magentoDataFixture Enterprise/ImportExport/_files/customer_finance.php
 * @magentoConfigFixture modules/Enterprise_Reward/active          1
 * @magentoConfigFixture modules/Enterprise_CustomerBalance/active 1
 */
class Enterprise_ImportExport_Model_Resource_Customer_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test join with reward points
     */
    public function testJoinWithRewardPoints()
    {
        /** @var $collection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');
        $collection->joinWithRewardPoints();
        $items = $collection->getItems();
        $this->assertCount(1, $items);

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = reset($items);
        $key = Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COL_REWARD_POINTS;
        $this->assertEquals(50, $customer->getData($key));
    }

    /**
     * Test join with customer balance
     */
    public function testJoinWithCustomerBalance()
    {
        /** @var $collection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $collection = Mage::getModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');
        $collection->joinWithCustomerBalance();
        $items = $collection->getItems();
        $this->assertCount(1, $items);

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = reset($items);
        $key = Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COL_CUSTOMER_BALANCE;
        $this->assertEquals(100, $customer->getData($key));
    }
}
