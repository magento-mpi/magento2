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
namespace Magento\Sales\Model;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testAfterCommitCallbackOrderGrid()
    {
        $collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Grid\Collection');
        $this->assertEquals(1, $collection->count());
        foreach ($collection as $order) {
            $this->assertInstanceOf('Magento\Sales\Model\Order', $order);
            $this->assertEquals('100000001', $order->getIncrementId());
        }
    }

    public function testAfterCommitCallbackOrderGridNotInvoked()
    {
        $adapter = \Mage::getResourceSingleton('Magento\Core\Model\Resource')->getConnection('write');
        $this->assertEquals(0, $adapter->getTransactionLevel(), 'This test must be outside a transaction.');

        $localOrderModel = \Mage::getModel('Magento\Sales\Model\Order');
        $resource = $localOrderModel->getResource();
        $resource->beginTransaction();
        try {
            /** @var $order \Magento\Sales\Model\Order */
            require __DIR__ . '/../_files/order.php';
            $collection = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Grid\Collection');
            $this->assertEquals(0, $collection->count());
            $resource->rollBack();
        } catch (\Exception $e) {
            $resource->rollBack();
            throw $e;
        }
    }
}
