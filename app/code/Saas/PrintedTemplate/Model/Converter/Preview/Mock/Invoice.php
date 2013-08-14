<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock object for invoice model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice extends Magento_Sales_Model_Order_Invoice
{
    /**
     * Initialize order with mock data
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        parent::setOrder($order);
        $cacheKey = Saas_PrintedTemplate_Model_Variable_Abstract_Entity::TAXES_GROUPED_BY_PERCENT_CACHE_KEY;
        $this->setData($this->_getMockData())
            ->setData($cacheKey, $order->getData($cacheKey));

        foreach ($this->_getMockItems() as $item) {
            $id = $item->getId();
            $item->unsetData('entity_id');
            $item->setInvoice($this);
            $this->addItem($item);
            $item->setId($id);
        }
        return $this;
    }

    /**
     * Returns array of mock items
     *
     * @return array
     */
    protected function _getMockItems()
    {
        $bundleDynamic = $this->_createItemMock('bundleDynamic');
        $bundleFixed = $this->_createItemMock('bundleFixed');

        $items = array(
            $this->_createItemMock('configurable'),
            $bundleDynamic,
            $bundleFixed,
        );
        $items = array_merge($items, $bundleDynamic->getChildrenItems(), $bundleFixed->getChildrenItems());

        return $items;
    }

    /**
     * Create mock object for order items and for specified product type
     *
     * @param string $type
     */
    protected function _createItemMock($type)
    {
        $item = Mage::getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice_Item_' . ucfirst($type));
        $item->init();
        $item->setInvoice($this);

        return $item;
    }

    /**
     * Returns data for the invoice
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array (
            'entity_id' => '-1',
            'store_id' => '1',
            'base_grand_total' => '1244.0600',
            'shipping_tax_amount' => '12.0000',
            'tax_amount' => '356.3800',
            'base_tax_amount' => '178.1900',
            'store_to_order_rate' => '2.0000',
            'base_shipping_tax_amount' => '6.0000',
            'base_discount_amount' => '115.1000',
            'base_to_order_rate' => '2.0000',
            'grand_total' => '2488.1300',
            'shipping_amount' => '60.0000',
            'subtotal_incl_tax' => '2646.3200',
            'base_subtotal_incl_tax' => '1323.1600',
            'store_to_base_rate' => '1.0000',
            'base_shipping_amount' => '30.0000',
            'total_qty' => '8.0000',
            'base_to_global_rate' => '1.0000',
            'subtotal' => '2301.9400',
            'base_subtotal' => '1150.9700',
            'discount_amount' => '230.1900',
            'billing_address_id' => '15',
            'is_used_for_refund' => NULL,
            'order_id' => '-1',
            'email_sent' => NULL,
            'can_void_flag' => '0',
            'state' => '2',
            'shipping_address_id' => '16',
            'cybersource_token' => NULL,
            'store_currency_code' => 'USD',
            'transaction_id' => NULL,
            'order_currency_code' => (string) Mage::getStoreConfig(
                Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'base_currency_code' => 'USD',
            'global_currency_code' => 'USD',
            'increment_id' => '100000008',
            'created_at' => '2011-04-28 11:38:34',
            'updated_at' => '2011-04-28 11:38:34',
            'invoice_type' => NULL,
            'customer_id' => '4',
            'real_order_id' => NULL,
            'invoice_status_id' => NULL,
            'is_virtual' => NULL,
            'total_paid' => NULL,
            'total_due' => NULL,
            'hidden_tax_amount' => '0.0000',
            'base_hidden_tax_amount' => '0.0000',
            'shipping_hidden_tax_amount' => '0.0000',
            'base_shipping_hidden_tax_amount' => '0.0000',
            'shipping_incl_tax' => '72.0000',
            'base_shipping_incl_tax' => '36.0000',
            'base_customer_balance_amount' => '0.0000',
            'customer_balance_amount' => '0.0000',
            'base_gift_cards_amount' => '0.0000',
            'gift_cards_amount' => '0.0000',
            'base_total_refunded' => NULL,
        );
    }
}
