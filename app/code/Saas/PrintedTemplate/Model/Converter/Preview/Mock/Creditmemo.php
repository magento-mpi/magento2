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
 * Mock object for creditmemo model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Creditmemo extends Magento_Sales_Model_Order_Creditmemo
{
    /**
     * Initialize creditmemo with mock data
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Saas_PrintedTemplate_Model_Converter_Preview_Mock_Creditmemo
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
            $item->setCreditmemo($this);
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
        $item = Mage::getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Creditmemo_Item_' . ucfirst($type));
        $item->init();
        $item->setCreditmemo($this);

        return $item;
    }

    /**
     * Returns data for the credit memo
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array (
            'entity_id' => '-1',
            'store_id' => '1',
            'adjustment_positive' => '0.0000',
            'base_shipping_tax_amount' => '6.0000',
            'store_to_order_rate' => '2.0000',
            'base_discount_amount' => '115.1000',
            'base_to_order_rate' => '2.0000',
            'grand_total' => '2488.1300',
            'base_adjustment_negative' => '0.0000',
            'base_subtotal_incl_tax' => '1323.1600',
            'shipping_amount' => '60.0000',
            'subtotal_incl_tax' => '2646.3200',
            'adjustment_negative' => '0.0000',
            'base_shipping_amount' => '30.0000',
            'store_to_base_rate' => '1.0000',
            'base_to_global_rate' => '1.0000',
            'base_adjustment' => '0.0000',
            'base_subtotal' => '1150.9700',
            'discount_amount' => '230.1900',
            'subtotal' => '2301.9400',
            'adjustment' => '0.0000',
            'base_grand_total' => '1244.0600',
            'base_adjustment_positive' => '0.0000',
            'base_tax_amount' => '178.1900',
            'shipping_tax_amount' => '12.0000',
            'tax_amount' => '356.3800',
            'order_id' => '-1',
            'email_sent' => NULL,
            'creditmemo_status' => NULL,
            'state' => '2',
            'shipping_address_id' => '16',
            'billing_address_id' => '15',
            'invoice_id' => NULL,
            'cybersource_token' => NULL,
            'store_currency_code' => 'USD',
            'order_currency_code' => (string) Mage::getStoreConfig(
                Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'base_currency_code' => 'USD',
            'global_currency_code' => 'USD',
            'transaction_id' => NULL,
            'increment_id' => '100000003',
            'created_at' => '2011-04-28 11:39:03',
            'updated_at' => '2011-04-28 11:39:03',
            'hidden_tax_amount' => '0.0000',
            'base_hidden_tax_amount' => '0.0000',
            'shipping_hidden_tax_amount' => NULL,
            'base_shipping_hidden_tax_amount' => NULL,
            'shipping_incl_tax' => '72.0000',
            'base_shipping_incl_tax' => '36.0000',
            'base_customer_balance_amount' => NULL,
            'customer_balance_amount' => NULL,
            'base_customer_balance_total_refunded' => '0.0000',
            'customer_balance_total_refunded' => '0.0000',
            'base_gift_cards_amount' => NULL,
            'gift_cards_amount' => NULL,
        );
    }
}
