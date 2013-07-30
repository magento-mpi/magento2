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

class Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_PrintedTemplate_Model_Resource_Template_Collection
     */
    protected $_collection;

    /**
     * Set up collection
     */
    protected function setUp()
    {
        $this->_collection = Mage::getResourceModel(
            'Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping_Collection'
        );
    }

    /**
     * Tear down
     */
    /**
     * @magentoConfigFixture current_store design/theme/full_name mage_demo
     * @magentoDataFixture Saas/PrintedTemplate/_files/order.php
     */
    public function testFilterByOrder()
    {
        $this->markTestIncomplete('MAGETWO-7075');
        $order = Mage::getModel('Mage_Sales_Model_Order')->loadByIncrementId('100000001');

        $items = $this->_collection->addFilterByOrder($order);

        $this->assertEquals(1, count($items));
        $this->assertEquals($order->getId(), $items->getFirstItem()->getOrderId());
    }

    /**
     * @magentoConfigFixture current_store design/theme/full_name mage_demo
     * @magentoDataFixture Saas/PrintedTemplate/_files/invoice.php
     */
    public function testFilterByInvoice()
    {
        $this->markTestIncomplete('MAGETWO-7075');
        $invoice = Mage::getModel('Mage_Sales_Model_Order_Invoice')->loadByIncrementId('100000001');

        $items = $this->_collection->addFilterByInvoice($invoice);

        $this->assertEquals(1, count($items));
    }

    /**
     * @magentoConfigFixture current_store design/theme/full_name mage_demo
     * @magentoDataFixture Saas/PrintedTemplate/_files/creditmemo.php
     */
    public function testFilterByCreditmemo()
    {
        $this->markTestIncomplete('MAGETWO-7075');
        $items = Mage::getModel('Mage_Sales_Model_Order_Creditmemo')->getResourceCollection()->getItems();
        $creditmemo = Mage::getModel('Mage_Sales_Model_Order')
                ->loadByIncrementId('100000001')
                ->getCreditmemosCollection()->getFirstItem();

        $items = $this->_collection->addFilterByCreditmemo($creditmemo);

        $this->assertEquals(1, count($items));
    }
}
