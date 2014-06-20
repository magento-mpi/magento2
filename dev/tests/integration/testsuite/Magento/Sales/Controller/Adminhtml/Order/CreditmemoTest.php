<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order;

/**
 * @magentoAppArea adminhtml
 */
class CreditmemoTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoConfigFixture current_store cataloginventory/item_options/auto_return 1
     * @magentoDataFixture Magento/Sales/_files/order_info.php
     */
    public function testAddCommentAction()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\CatalogInventory\Model\Stock\Status $status */
        $status = $objectManager->get('Magento\CatalogInventory\Model\Stock\Status');
        $status->updateStatus(1);
        /** @var \Magento\CatalogInventory\Model\Stock\Item $stockItem */
        $stockItem = $objectManager->create('Magento\CatalogInventory\Model\Stock\Item');
        $stockItem->loadByProduct(1);
        $this->assertEquals(95, $stockItem->getStockQty());
        $stockItem = null;

        /** @var \Magento\Sales\Model\Order $order */
        $order = $objectManager->create('Magento\Sales\Model\Order');
        $order->load('100000001', 'increment_id');

        $items = $order->getCreditmemosCollection()->getItems();
        $creditmemo = array_shift($items);
        $comment = 'Test Comment 02';

        $this->getRequest()->setParam('creditmemo_id', $creditmemo->getId());
        $this->getRequest()->setPost('comment', array('comment' => $comment));
        $this->dispatch('backend/sales/order_creditmemo/addComment/id/' . $creditmemo->getId());

        $html = $this->getResponse()->getBody();

        $this->assertContains($comment, $html);
        /** @var \Magento\CatalogInventory\Model\Stock\Item $stockItem */
        $stockItem = $objectManager->create('Magento\CatalogInventory\Model\Stock\Item');
        $stockItem->loadByProduct(1);
        $this->assertEquals(95, $stockItem->getStockQty());
    }
}
