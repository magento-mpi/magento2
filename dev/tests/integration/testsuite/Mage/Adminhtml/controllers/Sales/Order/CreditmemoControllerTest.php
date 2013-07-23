<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Sales_Order_CreditmemoControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @magentoConfigFixture current_store cataloginventory/item_options/auto_return 1
     * @ magentoDataFixture Mage/Adminhtml/controllers/Sales/_files/order_info.php
     */
    public function testAddCommentAction()
    {
        /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('Mage_CatalogInventory_Model_Stock_Item');
        $stockItem->loadByProduct(1);
        $this->assertEquals(95, $stockItem->getStockQty());
        $stockItem = null;

        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('Mage_Sales_Model_Order');
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
        /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('Mage_CatalogInventory_Model_Stock_Item');
        $stockItem->loadByProduct(1);
        $this->assertEquals(95, $stockItem->getStockQty());
    }

}
