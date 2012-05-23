<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Sales_Model_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     */
    public function testAfterCommitCallbackOrderGrid()
    {
        $collection = new Mage_Sales_Model_Resource_Order_Grid_Collection;
        $this->assertEquals(1, $collection->count());
        foreach ($collection as $order) {
            $this->assertInstanceOf('Mage_Sales_Model_Order', $order);
            $this->assertEquals('100000001', $order->getIncrementId());
        }
    }
}
