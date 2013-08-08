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
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_Configurable extends Mage_Sales_Model_Order_Item
{
    /**
     * Initialize order item with mock data
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData($this->_getMockData());

        $simple = $this->getModel('Mage_Sales_Model_Order_Item');
        $simple->setData($this->_getChildMockData());
        $this->addChildItem($simple);
    }

    /**
     * Returns model instance
     *
     * @param string $modelName
     * @return Magento_Core_Model_Abstract
     */
    public function getModel($modelName)
    {
        return Mage::getModel($modelName);
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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _getMockData()
    {
        return array (
            'item_id' => '51',
            'order_id' => '-1',
            'parent_item_id' => NULL,
            'quote_item_id' => '66',
            'store_id' => '1',
            'created_at' => '2011-04-28 11:38:02',
            'updated_at' => '2011-04-28 12:05:18',
            'product_id' => '108',
            'product_type' => 'configurable',
            'product_options' =>
                serialize(array (
                  'info_buyRequest' =>
                  array (
                    'uenc' => 'aHR0cDovL3JlZC5sb2NhbGhvc3QuY29tL2FwcGFyZWwvc2hvZXMvd29tZW5zL25pbmUtd2VzdC13b21l'
                        . 'bi1zLWx1Y2Vyby1wdW1wLmh0bWw_b3B0aW9ucz1jYXJ0',
                    'product' => '108',
                    'related_product' => '',
                    'super_attribute' =>
                    array (
                      502 => '45',
                    ),
                    'qty' => '1',
                  ),
                  'attributes_info' =>
                  array (
                    0 =>
                    array (
                      'label' => $this->_getHelper()->__('Shoe Size'),
                      'value' => '4',
                    ),
                  ),
                  'simple_name' => $this->_getHelper()->__("Nine West Women's Lucero Pump"),
                  'simple_sku' => 'nine_4',
                  'product_calculations' => 1,
                  'shipment_type' => 0,
                  'giftcard_lifetime' => NULL,
                  'giftcard_is_redeemable' => 0,
                  'giftcard_email_template' => NULL,
                  'giftcard_type' => NULL,
                )),
            'weight' => '2.0000',
            'is_virtual' => '0',
            'sku' => 'nine_4',
            'name' => $this->_getHelper()->__("Nine West Women's Lucero Pump"),
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
            'qty_refunded' => '1.0000',
            'qty_shipped' => '1.0000',
            'base_cost' => '29.9900',
            'price' => '179.9800',
            'base_price' => '89.9900',
            'original_price' => '179.9800',
            'base_original_price' => '89.9900',
            'tax_percent' => '8.8800',
            'tax_amount' => '15.9800',
            'base_tax_amount' => '7.9900',
            'tax_invoiced' => '15.9800',
            'base_tax_invoiced' => '7.9900',
            'discount_percent' => '10.0000',
            'discount_amount' => '18.0000',
            'base_discount_amount' => '9.0000',
            'discount_invoiced' => '18.0000',
            'base_discount_invoiced' => '9.0000',
            'amount_refunded' => '0.0000',
            'base_amount_refunded' => '0.0000',
            'row_total' => '179.9800',
            'base_row_total' => '89.9900',
            'row_invoiced' => '179.9800',
            'base_row_invoiced' => '89.9900',
            'row_weight' => '2.0000',
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
            'hidden_tax_refunded' => '0.0000',
            'base_hidden_tax_refunded' => NULL,
            'is_nominal' => '0',
            'tax_canceled' => NULL,
            'hidden_tax_canceled' => NULL,
            'tax_refunded' => '7.9900',
            'price_incl_tax' => '195.9600',
            'base_price_incl_tax' => '97.9800',
            'row_total_incl_tax' => '195.9600',
            'base_row_total_incl_tax' => '97.9800',
        );
    }

    /**
     * Returns data for child of configurable product
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _getChildMockData()
    {
        return  array (
            'item_id' => '52',
            'order_id' => '-1',
            'parent_item_id' => '51',
            'quote_item_id' => '67',
            'store_id' => '1',
            'created_at' => '2011-04-28 11:38:02',
            'updated_at' => '2011-04-28 12:05:18',
            'product_id' => '109',
            'product_type' => 'simple',
            'product_options' => 'a:5:{s:15:"info_buyRequest";a:5:{s:4:"uenc";s:124:"aHR0cDovL3JlZC5sb2NhbGhvc3QuY'
                . '29tL2FwcGFyZWwvc2hvZXMvd29tZW5zL25pbmUtd2VzdC13b21lbi1zLWx1Y2Vyby1wdW1wLmh0bWw_b3B0aW9ucz1jYXJ0";'
                . 's:7:"product";s:3:"108";s:15:"related_product";s:0:"";s:15:"super_attribute";a:1:{i:502;s:2:"45";}'
                . 's:3:"qty";s:1:"1";}s:17:"giftcard_lifetime";N;s:22:"giftcard_is_redeemable";i:0;'
                . 's:23:"giftcard_email_template";N;s:13:"giftcard_type";N;}',
            'weight' => '2.0000',
            'is_virtual' => '0',
            'sku' => 'nine_4',
            'name' => $this->_getHelper()->__("Nine West Women's Lucero Pump"),
            'description' => NULL,
            'applied_rule_ids' => NULL,
            'additional_data' => NULL,
            'free_shipping' => '0',
            'is_qty_decimal' => '0',
            'no_discount' => '0',
            'qty_backordered' => NULL,
            'qty_canceled' => '0.0000',
            'qty_invoiced' => '1.0000',
            'qty_ordered' => '1.0000',
            'qty_refunded' => '1.0000',
            'qty_shipped' => '1.0000',
            'base_cost' => '29.9900',
            'price' => '0.0000',
            'base_price' => '0.0000',
            'original_price' => '0.0000',
            'base_original_price' => NULL,
            'tax_percent' => '20.0000',
            'tax_amount' => '0.0000',
            'base_tax_amount' => '0.0000',
            'tax_invoiced' => '0.0000',
            'base_tax_invoiced' => '0.0000',
            'discount_percent' => '0.0000',
            'discount_amount' => '0.0000',
            'base_discount_amount' => '0.0000',
            'discount_invoiced' => '0.0000',
            'base_discount_invoiced' => '0.0000',
            'amount_refunded' => '0.0000',
            'base_amount_refunded' => '0.0000',
            'row_total' => '0.0000',
            'base_row_total' => '0.0000',
            'row_invoiced' => '0.0000',
            'base_row_invoiced' => '0.0000',
            'row_weight' => '0.0000',
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
            'hidden_tax_refunded' => '0.0000',
            'base_hidden_tax_refunded' => NULL,
            'is_nominal' => '0',
            'tax_canceled' => NULL,
            'hidden_tax_canceled' => NULL,
            'tax_refunded' => '0.0000',
            'price_incl_tax' => '0.0000',
            'base_price_incl_tax' => '0.0000',
            'row_total_incl_tax' => '0.0000',
            'base_row_total_incl_tax' => '0.0000',
        );
    }
}
