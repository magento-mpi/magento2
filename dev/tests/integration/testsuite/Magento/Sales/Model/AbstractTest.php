<?php
/**
 * {license_notice}
 *
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
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Resource\Order\Grid\Collection'
        );
        $this->assertEquals(1, $collection->count());
        foreach ($collection as $order) {
            $this->assertInstanceOf('Magento\Sales\Model\Order', $order);
            $this->assertEquals('100000001', $order->getIncrementId());
        }
    }

    public function testAfterCommitCallbackOrderGridNotInvoked()
    {
        $adapter = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\Resource'
        )->getConnection(
            'core_write'
        );
        $this->assertEquals(0, $adapter->getTransactionLevel(), 'This test must be outside a transaction.');

        $localOrderModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Order'
        );
        $resource = $localOrderModel->getResource();
        $resource->beginTransaction();
        try {
            /** @var $order \Magento\Sales\Model\Order */
            require __DIR__ . '/../_files/order.php';
            $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                'Magento\Sales\Model\Resource\Order\Grid\Collection'
            );
            $this->assertEquals(0, $collection->count());
            $resource->rollBack();
        } catch (\Exception $e) {
            $resource->rollBack();
            throw $e;
        }
    }
}
