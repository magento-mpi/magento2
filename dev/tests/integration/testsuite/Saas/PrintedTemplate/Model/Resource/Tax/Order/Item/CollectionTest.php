<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_PrintedTemplate_Model_Resource_Template_Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel(
            'Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection'
        );
    }

    /**
     * @magentoDataFixture Saas/PrintedTemplate/_files/order.php
     */
    public function testAddFilterByInvoice()
    {
        if (Magento_Test_Helper_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestIncomplete('MAGETWO-7075');
        }

        $order = Mage::getModel('Mage_Sales_Model_Order')->loadByIncrementId('100000001');
        $items = array();
        $orderItems = $order->getAllItems();
        foreach ($orderItems as $item) {
            $items[$item->getItemId()] = $item->getQtyOrdered();
        }

        $invoice = Mage::getModel('Mage_Sales_Model_Service_Order', array('order' => $order))
            ->prepareInvoice($items)->save();
        $collection = $this->_collection->addFilterByInvoice($invoice);
        foreach ($collection->getItems() as $key => $item) {
            $expectedId = $orderItems[$key]->getItemId();
            $this->assertEquals($expectedId, $item->getItemId());
        }

    }
}
