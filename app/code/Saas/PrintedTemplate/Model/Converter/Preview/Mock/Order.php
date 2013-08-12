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
 * Mock object for order model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order extends Magento_Sales_Model_Order
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_initOrder();
    }

    /**
     * Returns data helper
     *
     * @return Saas_PrintedTemplate_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }

    /**
     * Initialize order with mock data
     */
    protected function _initOrder()
    {
        $this->setData($this->_getMockData())
            ->setData(
            Saas_PrintedTemplate_Model_Variable_Abstract_Entity::TAXES_GROUPED_BY_PERCENT_CACHE_KEY,
            $this->_getMockTaxes()
        );

        foreach ($this->_getMockItems() as $item) {
            $id = $item->getId();
            $item->unsetData('item_id');
            $item->setOrder($this);
            $this->addItem($item);
            $item->setId($id);
        }

        $this->addAddress($this->getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Address_Billing'));
        $this->addAddress($this->getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Address_Shipping'));
        $this->setPayment($this->getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Payment'));

        return $this;
    }

    /**
     * Returns model instance
     *
     * @param string $modelName
     * @return mixed
     */
    public function getModel($modelName)
    {
        return Mage::getModel($modelName);
    }

    /**
     * Get order item by its ID
     *
     * @param int $itemId
     * @return Magento_Sales_Model_Order_Item
     */
    public function getItemById($itemId)
    {
        foreach ($this->_items as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }
    }

    /**
     * Returns array of mock items
     *
     * @return array
     */
    protected function _getMockItems()
    {
        $configurable = $this->_createItemMock('configurable');
        $bundleDynamic = $this->_createItemMock('bundleDynamic');
        $bundleFixed = $this->_createItemMock('bundleFixed');

        $items = array(
            $this->_createItemMock('simple'),
            $configurable,
            $bundleDynamic,
            $bundleFixed,
        );

        $items = array_merge(
            $items,
            $configurable->getChildrenItems(),
            $bundleDynamic->getChildrenItems(),
            $bundleFixed->getChildrenItems()
        );

        return $items;
    }

    /**
     * Create mock object for order items and for specified product type
     *
     * @param string $type
     * @return Magento_Sales_Model_Order_Item
     */
    protected function _createItemMock($type = 'simple')
    {
        return $this->getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_' . ucfirst($type));
    }

    /**
     * Returns array of mock taxes
     *
     * @return array
     */
    protected function _getMockTaxes()
    {
        $itemsTaxes = $this->getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Tax_ItemCollection');
        foreach ($itemsTaxes as $itemTax) {
            $itemTax->setOrder($this);
        }

        $shippingTaxes
            = $this->getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Tax_ShippingCollection');
        foreach ($shippingTaxes as $shippingTax) {
            $shippingTax->setOrder($this);
        }

        return array('items_taxes' => $itemsTaxes, 'shipping_taxes' => $shippingTaxes);
    }

    /**
     * Get store config value
     *
     * @param string $path
     * @return string
     */
    protected function _getStoreConfig($path)
    {
        return (string)Mage::getStoreConfig($path);
    }

    /**
     * Returns data for the order
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _getMockData()
    {
        return array (
            'entity_id' => '-1',
            'state' => 'processing',
            'status' => 'processing',
            'coupon_code' => '111000',
            'protect_code' => '226910',
            'shipping_description' => $this->_getHelper()->__('Flat Rate - Fixed'),
            'is_virtual' => '0',
            'store_id' => '1',
            'customer_id' => '4',
            'base_discount_amount' => '-190.1000',
            'base_discount_canceled' => NULL,
            'base_discount_invoiced' => '190.1000',
            'base_discount_refunded' => '115.1000',
            'base_grand_total' => '2069.0600',
            'base_shipping_amount' => '30.0000',
            'base_shipping_canceled' => NULL,
            'base_shipping_invoiced' => '30.0000',
            'base_shipping_refunded' => '30.0000',
            'base_shipping_tax_amount' => '6.0000',
            'base_shipping_tax_refunded' => '6.0000',
            'base_subtotal' => '1900.9700',
            'base_subtotal_canceled' => NULL,
            'base_subtotal_invoiced' => '1900.9700',
            'base_subtotal_refunded' => '1150.9700',
            'base_tax_amount' => '328.1900',
            'base_tax_canceled' => NULL,
            'base_tax_invoiced' => '328.1900',
            'base_tax_refunded' => '178.1900',
            'base_to_global_rate' => '1.0000',
            'base_to_order_rate' => '2.0000',
            'base_total_canceled' => NULL,
            'base_total_invoiced' => '2069.0600',
            'base_total_invoiced_cost' => '0.0000',
            'base_total_offline_refunded' => '1244.0600',
            'base_total_online_refunded' => NULL,
            'base_total_paid' => '2069.0600',
            'base_total_qty_ordered' => NULL,
            'base_total_refunded' => '1244.0600',
            'discount_amount' => '-380.1900',
            'discount_canceled' => NULL,
            'discount_invoiced' => '380.1900',
            'discount_refunded' => '230.1900',
            'grand_total' => '4138.1300',
            'shipping_amount' => '60.0000',
            'shipping_canceled' => NULL,
            'shipping_invoiced' => '60.0000',
            'shipping_refunded' => '60.0000',
            'shipping_tax_amount' => '12.0000',
            'shipping_tax_refunded' => '12.0000',
            'store_to_base_rate' => '1.0000',
            'store_to_order_rate' => '2.0000',
            'subtotal' => '3801.9400',
            'subtotal_canceled' => NULL,
            'subtotal_invoiced' => '3801.9400',
            'subtotal_refunded' => '2301.9400',
            'tax_amount' => '656.3800',
            'tax_canceled' => NULL,
            'tax_invoiced' => '656.3800',
            'tax_refunded' => '356.3800',
            'total_canceled' => NULL,
            'total_invoiced' => '4138.1300',
            'total_offline_refunded' => '2488.1300',
            'total_online_refunded' => NULL,
            'total_paid' => '4138.1300',
            'total_qty_ordered' => '4.0000',
            'total_refunded' => '2488.1300',
            'can_ship_partially' => NULL,
            'can_ship_partially_item' => NULL,
            'customer_is_guest' => '0',
            'customer_note_notify' => '0',
            'billing_address_id' => '15',
            'customer_group_id' => '3',
            'edit_increment' => NULL,
            'email_sent' => '1',
            'forced_do_shipment_with_invoice' => NULL,
            'gift_message_id' => NULL,
            'payment_authorization_expiration' => NULL,
            'paypal_ipn_customer_notified' => NULL,
            'quote_address_id' => NULL,
            'quote_id' => '13',
            'shipping_address_id' => '16',
            'adjustment_negative' => '0.0000',
            'adjustment_positive' => '0.0000',
            'base_adjustment_negative' => '0.0000',
            'base_adjustment_positive' => '0.0000',
            'base_shipping_discount_amount' => '0.0000',
            'base_subtotal_incl_tax' => NULL,
            'base_total_due' => '0.0000',
            'payment_authorization_amount' => NULL,
            'shipping_discount_amount' => '0.0000',
            'subtotal_incl_tax' => NULL,
            'total_due' => '0.0000',
            'weight' => '4.3000',
            'customer_dob' => NULL,
            'increment_id' => '100000008',
            'applied_rule_ids' => '1',
            'base_currency_code' => 'USD',
            'customer_email' => 'RamonaKHill@teleworm.com',
            'customer_firstname' => $this->_getHelper()->__('Ramona'),
            'customer_lastname' => $this->_getHelper()->__('Hill'),
            'customer_middlename' => '',
            'customer_prefix' => $this->_getHelper()->__('Mrs.'),
            'customer_suffix' => $this->_getHelper()->__('K.'),
            'customer_taxvat' => '',
            'discount_description' => $this->_getHelper()->__('Your Coupon:'),
            'ext_customer_id' => NULL,
            'ext_order_id' => NULL,
            'global_currency_code' => 'USD',
            'hold_before_state' => NULL,
            'hold_before_status' => NULL,
            'order_currency_code' => $this->_getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'original_increment_id' => NULL,
            'relation_child_id' => NULL,
            'relation_child_real_id' => NULL,
            'relation_parent_id' => NULL,
            'relation_parent_real_id' => NULL,
            'remote_ip' => '192.168.60.94',
            'shipping_method' => 'flatrate_flatrate',
            'store_currency_code' => 'USD',
            'store_name' => $this->_getHelper()->__("Main Website\nMain Store\nEnglish"),
            'x_forwarded_for' => NULL,
            'customer_note' => NULL,
            'created_at' => '2011-04-28 11:38:02',
            'updated_at' => '2011-04-28 12:05:18',
            'total_item_count' => '4',
            'customer_gender' => NULL,
            'currency_rate' => NULL,
            'tax_percent' => NULL,
            'custbalance_amount' => NULL,
            'currency_base_id' => NULL,
            'real_order_id' => NULL,
            'currency_code' => NULL,
            'is_multi_payment' => NULL,
            'tracking_numbers' => NULL,
            'is_hold' => NULL,
            'base_custbalance_amount' => NULL,
            'hidden_tax_amount' => '0.0000',
            'base_hidden_tax_amount' => '0.0000',
            'shipping_hidden_tax_amount' => '0.0000',
            'base_shipping_hidden_tax_amount' => '0.0000',
            'hidden_tax_invoiced' => '0.0000',
            'base_hidden_tax_invoiced' => '0.0000',
            'hidden_tax_refunded' => '0.0000',
            'base_hidden_tax_refunded' => '0.0000',
            'shipping_incl_tax' => '72.0000',
            'base_shipping_incl_tax' => '36.0000',
            'base_customer_balance_amount' => '0.0000',
            'customer_balance_amount' => '0.0000',
            'base_customer_balance_invoiced' => NULL,
            'customer_balance_invoiced' => NULL,
            'base_customer_balance_refunded' => NULL,
            'customer_balance_refunded' => NULL,
            'base_customer_balance_total_refunded' => NULL,
            'customer_balance_total_refunded' => NULL,
            'gift_cards' => 'a:0:{}',
            'base_gift_cards_amount' => '0.0000',
            'gift_cards_amount' => '0.0000',
            'base_gift_cards_invoiced' => NULL,
            'gift_cards_invoiced' => NULL,
            'base_gift_cards_refunded' => NULL,
            'gift_cards_refunded' => NULL,
        );
    }
}
