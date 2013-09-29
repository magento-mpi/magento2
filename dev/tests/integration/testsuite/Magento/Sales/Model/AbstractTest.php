<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testAfterCommitCallbackOrderGrid()
    {
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Resource_Order_Grid_Collection');
        $this->assertEquals(1, $collection->count());
        foreach ($collection as $order) {
            $this->assertInstanceOf('Magento_Sales_Model_Order', $order);
            $this->assertEquals('100000001', $order->getIncrementId());
        }
    }

    public function testAfterCommitCallbackOrderGridNotInvoked()
    {
        $adapter = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Resource')
            ->getConnection('core_write');
        $this->assertEquals(0, $adapter->getTransactionLevel(), 'This test must be outside a transaction.');

        $localOrderModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Sales_Model_Order');
        $resource = $localOrderModel->getResource();
        $resource->beginTransaction();
        try {
            /** @var $order Magento_Sales_Model_Order */
            require __DIR__ . '/../_files/order.php';
            $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
                ->create('Magento_Sales_Model_Resource_Order_Grid_Collection');
            $this->assertEquals(0, $collection->count());
            $resource->rollBack();
        } catch (Exception $e) {
            $resource->rollBack();
            throw $e;
        }
    }
}
