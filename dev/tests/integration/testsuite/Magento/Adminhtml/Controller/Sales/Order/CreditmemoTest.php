<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Controller\Sales\Order;

/**
 * @magentoAppArea adminhtml
 */
class CreditmemoTest extends \Magento\Backend\Utility\Controller
{
    /**
     * @magentoConfigFixture current_store cataloginventory/item_options/auto_return 1
     * @magentoDataFixture Magento/Adminhtml/controllers/Sales/_files/order_info.php
     */
    public function testAddCommentAction()
    {
        /** @var $stockItem \Magento\CatalogInventory\Model\Stock\Item */
        $stockItem = \Mage::getModel('Magento\CatalogInventory\Model\Stock\Item');
        $stockItem->loadByProduct(1);
        $this->assertEquals(95, $stockItem->getStockQty());
        $stockItem = null;

        /** @var $order \Magento\Sales\Model\Order */
        $order = \Mage::getModel('Magento\Sales\Model\Order');
        $order->load('100000001', 'increment_id');

        $items = $order->getCreditmemosCollection()->getItems();
        $creditmemo = array_shift($items);
        $comment = 'Test Comment 02';

        $this->getRequest()->setParam('creditmemo_id', $creditmemo->getId());
        $this->getRequest()->setPost('comment', array(
            'comment' => $comment));
        $this->dispatch('backend/admin/sales_order_creditmemo/addComment/id/' . $creditmemo->getId());

        $html = $this->getResponse()->getBody();

        $this->assertContains($comment, $html);
        /** @var $stockItem \Magento\CatalogInventory\Model\Stock\Item */
        $stockItem = \Mage::getModel('Magento\CatalogInventory\Model\Stock\Item');
        $stockItem->loadByProduct(1);
        $this->assertEquals(95, $stockItem->getStockQty());
    }

}
