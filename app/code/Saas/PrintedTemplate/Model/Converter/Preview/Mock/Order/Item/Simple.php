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
 * Mock object for order item model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_Simple extends Mage_Sales_Model_Order_Item
{
    /**
     * Initialize order item with mock data
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData($this->_getMockData());
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
     * Returns data for the order item
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array (
            'item_id' => '59',
            'order_id' => '-1',
            'parent_item_id' => NULL,
            'quote_item_id' => '74',
            'store_id' => '1',
            'created_at' => '2011-04-28 11:38:02',
            'updated_at' => '2011-04-28 12:05:18',
            'product_id' => '166',
            'product_type' => 'simple',
            'product_options' => 'a:5:{s:15:"info_buyRequest";a:3:{s:4:"uenc";s:72:"aHR0cDovL3JlZC5sb2NhbGhvc3Qu'
                . 'Y29tL2VsZWN0cm9uaWNzL2NlbGwtcGhvbmVzLmh0bWw,";s:7:"product";s:3:"166";s:3:"qty";i:1;}'
                . 's:17:"giftcard_lifetime";N;s:22:"giftcard_is_redeemable";i:0;s:23:"giftcard_email_template";N;'
                . 's:13:"giftcard_type";N;}',
            'weight' => '0.3000',
            'is_virtual' => '0',
            'sku' => 'HTC Touch Diamond',
            'name' => __('HTC Touch Diamond'),
            'description' => NULL,
            'applied_rule_ids' => '1',
            'additional_data' => NULL,
            'free_shipping' => '0',
            'is_qty_decimal' => '0',
            'no_discount' => '0',
            'qty_backordered' => NULL,
            'qty_canceled' => '0.0000',
            'qty_invoiced' => '1.0000',
            'qty_ordered' => '1.0000',
            'qty_refunded' => '0.0000',
            'qty_shipped' => '0.0000',
            'base_cost' => NULL,
            'price' => '1500.0000',
            'base_price' => '750.0000',
            'original_price' => '1500.0000',
            'base_original_price' => '750.0000',
            'tax_percent' => '20.0000',
            'tax_amount' => '300.0000',
            'base_tax_amount' => '150.0000',
            'tax_invoiced' => '300.0000',
            'base_tax_invoiced' => '150.0000',
            'discount_percent' => '10.0000',
            'discount_amount' => '150.0000',
            'base_discount_amount' => '75.0000',
            'discount_invoiced' => '150.0000',
            'base_discount_invoiced' => '75.0000',
            'amount_refunded' => '0.0000',
            'base_amount_refunded' => '0.0000',
            'row_total' => '1500.0000',
            'base_row_total' => '750.0000',
            'row_invoiced' => '1500.0000',
            'base_row_invoiced' => '750.0000',
            'row_weight' => '0.3000',
            'gift_message_id' => NULL,
            'gift_message_available' => '2',
            'base_tax_before_discount' => NULL,
            'tax_before_discount' => NULL,
            'weee_tax_applied' => 'a:0:{}',
            'weee_tax_applied_amount' => '0.0000',
            'weee_tax_applied_row_amount' => '0.0000',
            'base_weee_tax_applied_amount' => '0.0000',
            'base_weee_tax_applied_row_amount' => '0.0000',
            'weee_tax_disposition' => '0.0000',
            'weee_tax_row_disposition' => '0.0000',
            'base_weee_tax_disposition' => '0.0000',
            'base_weee_tax_row_disposition' => '0.0000',
            'ext_order_item_id' => NULL,
            'locked_do_invoice' => NULL,
            'locked_do_ship' => NULL,
            'hidden_tax_amount' => '0.0000',
            'base_hidden_tax_amount' => '0.0000',
            'hidden_tax_invoiced' => '0.0000',
            'base_hidden_tax_invoiced' => '0.0000',
            'hidden_tax_refunded' => NULL,
            'base_hidden_tax_refunded' => NULL,
            'is_nominal' => '0',
            'tax_canceled' => NULL,
            'hidden_tax_canceled' => NULL,
            'tax_refunded' => NULL,
            'price_incl_tax' => '1800.0000',
            'base_price_incl_tax' => '900.0000',
            'row_total_incl_tax' => '1800.0000',
            'base_row_total_incl_tax' => '900.0000',
        );
    }
}
