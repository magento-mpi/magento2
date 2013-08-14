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
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_BundleFixed extends Magento_Sales_Model_Order_Item
{
    /**
     * Initialize order item with mock data
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData($this->_getMockData());

        foreach ($this->_getChildrenMockData() as $data) {
            $this->getModel('Magento_Sales_Model_Order_Item')
                ->setData($data)
                ->setParentItem($this);
        }
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
            'item_id' => '56',
            'order_id' => '-1',
            'parent_item_id' => NULL,
            'quote_item_id' => '71',
            'store_id' => '1',
            'created_at' => '2011-04-28 11:38:02',
            'updated_at' => '2011-04-28 12:05:18',
            'product_id' => '169',
            'product_type' => 'bundle',
            'product_options' =>
                serialize(array (
                  'info_buyRequest' =>
                  array (
                    'uenc' => 'aHR0cDovL3JlZC5sb2NhbGhvc3QuY29tL2JhbmRsLXByb2R1'
                        . 'Y3QtZml4ZWQtcHJpY2UuaHRtbD9vcHRpb25zPWNhcnQ,',
                    'product' => '169',
                    'related_product' => '',
                    'qty' => '1',
                    'bundle_option_qty' =>
                    array (
                      24 => '1',
                      23 => '1',
                    ),
                    'bundle_option' =>
                    array (
                      24 => '65',
                      23 => '64',
                    ),
                  ),
                  'bundle_options' =>
                  array (
                    23 =>
                    array (
                      'option_id' => '23',
                      'label' => $this->_getHelper()->__('Memory'),
                      'value' =>
                      array (
                        0 =>
                        array (
                          'title' => $this->_getHelper()->__('Crucial 2GB PC4200 DDR2 533MHz Memory'),
                          'qty' => 1,
                          'price' => 300,
                        ),
                      ),
                    ),
                    24 =>
                    array (
                      'option_id' => '24',
                      'label' => $this->_getHelper()->__('Electronics'),
                      'value' =>
                      array (
                        0 =>
                        array (
                          'title' => $this->_getHelper()->__('Electronics product'),
                          'qty' => 1,
                          'price' => 40,
                        ),
                      ),
                    ),
                  ),
                  'product_calculations' => 1,
                  'shipment_type' => '1',
                  'giftcard_lifetime' => NULL,
                  'giftcard_is_redeemable' => 0,
                  'giftcard_email_template' => NULL,
                  'giftcard_type' => NULL,
                )),
            'weight' => '0.0000',
            'is_virtual' => '0',
            'sku' => '23',
            'name' => $this->_getHelper()->__('Bundle product fixed price'),
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
            'qty_shipped' => '0.0000',
            'base_cost' => NULL,
            'price' => '1340.0000',
            'base_price' => '670.0000',
            'original_price' => '1340.0000',
            'base_original_price' => '670.0000',
            'tax_percent' => '20.0000',
            'tax_amount' => '268.0000',
            'base_tax_amount' => '134.0000',
            'tax_invoiced' => '268.0000',
            'base_tax_invoiced' => '134.0000',
            'discount_percent' => '10.0000',
            'discount_amount' => '134.0000',
            'base_discount_amount' => '67.0000',
            'discount_invoiced' => '134.0000',
            'base_discount_invoiced' => '67.0000',
            'amount_refunded' => '0.0000',
            'base_amount_refunded' => '0.0000',
            'row_total' => '1340.0000',
            'base_row_total' => '670.0000',
            'row_invoiced' => '1340.0000',
            'base_row_invoiced' => '670.0000',
            'row_weight' => '0.0000',
            'gift_message_id' => NULL,
            'gift_message_available' => NULL,
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
            'tax_refunded' => '134.0000',
            'price_incl_tax' => '1608.0000',
            'base_price_incl_tax' => '804.0000',
            'row_total_incl_tax' => '1608.0000',
            'base_row_total_incl_tax' => '804.0000',
        );
    }

    /**
     * Returns data for children of bundle product
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _getChildrenMockData()
    {
        return array(
            array (
                'item_id' => '57',
                'order_id' => '-1',
                'parent_item_id' => '56',
                'quote_item_id' => '72',
                'store_id' => '1',
                'created_at' => '2011-04-28 11:38:02',
                'updated_at' => '2011-04-28 12:05:18',
                'product_id' => '168',
                'product_type' => 'simple',
                'product_options' =>
                    serialize(array (
                      'info_buyRequest' =>
                      array (
                        'uenc' => 'aHR0cDovL3JlZC5sb2NhbGhvc3QuY29tL2JhbmRsLXByb2R1'
                            . 'Y3QtZml4ZWQtcHJpY2UuaHRtbD9vcHRpb25zPWNhcnQ,',
                        'product' => '169',
                        'related_product' => '',
                        'qty' => '1',
                        'bundle_option_qty' =>
                        array (
                          24 => '1',
                          23 => '1',
                        ),
                        'bundle_option' =>
                        array (
                          24 => '65',
                          23 => '64',
                        ),
                      ),
                      'giftcard_lifetime' => NULL,
                      'giftcard_is_redeemable' => 0,
                      'giftcard_email_template' => NULL,
                      'giftcard_type' => NULL,
                      'bundle_selection_attributes' =>
                        serialize(array (
                          'price' => 40,
                          'qty' => '1',
                          'option_label' => $this->_getHelper()->__('Electronics'),
                          'option_id' => '24',
                        )),
                    )),
                'weight' => '22.0000',
                'is_virtual' => '0',
                'sku' => '234222',
                'name' => $this->_getHelper()->__('Electronics product'),
                'description' => NULL,
                'applied_rule_ids' => '',
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
                'base_cost' => NULL,
                'price' => '0.0000',
                'base_price' => '0.0000',
                'original_price' => '0.0000',
                'base_original_price' => NULL,
                'tax_percent' => '20.0000',
                'tax_amount' => '22.0000',
                'base_tax_amount' => '11.0000',
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
                'weee_tax_applied' => 'a:1:{i:0;a:9:{s:5:"title";s:4:"weee";s:11:"base_amount";s:7:"55.0000";'
                    . 's:6:"amount";d:110;s:10:"row_amount";d:110;s:15:"base_row_amount";d:55;'
                    . 's:20:"base_amount_incl_tax";s:7:"55.0000";s:15:"amount_incl_tax";d:110;'
                    . 's:19:"row_amount_incl_tax";d:110;s:24:"base_row_amount_incl_tax";d:55;}}',
                'weee_tax_applied_amount' => '110.0000',
                'weee_tax_applied_row_amount' => '110.0000',
                'base_weee_tax_applied_amount' => '55.0000',
                'base_weee_tax_applied_row_amount' => '55.0000',
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
                'tax_refunded' => '11.0000',
                'price_incl_tax' => NULL,
                'base_price_incl_tax' => NULL,
                'row_total_incl_tax' => NULL,
                'base_row_total_incl_tax' => NULL,
            ),
            array (
                'item_id' => '58',
                'order_id' => '-1',
                'parent_item_id' => '56',
                'quote_item_id' => '73',
                'store_id' => '1',
                'created_at' => '2011-04-28 11:38:02',
                'updated_at' => '2011-04-28 12:05:18',
                'product_id' => '140',
                'product_type' => 'simple',
                'product_options' =>
                    serialize(array (
                      'info_buyRequest' =>
                      array (
                        'uenc' => 'aHR0cDovL3JlZC5sb2NhbGhvc3QuY29tL2JhbmRsLXByb2R1Y3'
                            . 'QtZml4ZWQtcHJpY2UuaHRtbD9vcHRpb25zPWNhcnQ,',
                        'product' => '169',
                        'related_product' => '',
                        'qty' => '1',
                        'bundle_option_qty' =>
                        array (
                          24 => '1',
                          23 => '1',
                        ),
                        'bundle_option' =>
                        array (
                          24 => '65',
                          23 => '64',
                        ),
                      ),
                      'giftcard_lifetime' => NULL,
                      'giftcard_is_redeemable' => 0,
                      'giftcard_email_template' => NULL,
                      'giftcard_type' => NULL,
                      'bundle_selection_attributes' =>
                        serialize(array (
                          'price' => 300,
                          'qty' => '1',
                          'option_label' => $this->_getHelper()->__('Memory'),
                          'option_id' => '23',
                        )),
                    )),
                'weight' => '1.0000',
                'is_virtual' => '0',
                'sku' => '2gbdimm',
                'name' => $this->_getHelper()->__('Crucial 2GB PC4200 DDR2 533MHz Memory'),
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
                'base_cost' => NULL,
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
            ),
        );
    }
}
