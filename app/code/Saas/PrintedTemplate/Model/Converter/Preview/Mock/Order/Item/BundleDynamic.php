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
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Item_BundleDynamic extends Mage_Sales_Model_Order_Item
{
    /**
     * Initialize order item with mock data
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData($this->_getMockData());

        foreach ($this->_getChildrenMockData() as $data) {
            $this->getModel('Mage_Sales_Model_Order_Item')
                ->setData($data)
                ->setParentItem($this);
        }
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
            'item_id' => '53',
            'order_id' => '-1',
            'parent_item_id' => NULL,
            'quote_item_id' => '68',
            'store_id' => '1',
            'created_at' => '2011-04-28 11:38:02',
            'updated_at' => '2011-04-28 12:05:18',
            'product_id' => '170',
            'product_type' => 'bundle',
            'product_options' =>
                serialize(array (
                  'info_buyRequest' =>
                  array (
                    'uenc' => 'aHR0cDovL3JlZC5sb2NhbGhvc3QuY29tL2dpZnQtY2FyZHMvYnVuZGxlL'
                        . 'XByb2R1Y3QtZHluYW1pYy1wcmljZS5odG1sP29wdGlvbnM9Y2FydA,,',
                    'product' => '170',
                    'related_product' => '',
                    'qty' => '1',
                    'bundle_option_qty' =>
                    array (
                      25 => '1',
                      26 => '1',
                    ),
                    'bundle_option' =>
                    array (
                      25 => '68',
                      26 => '70',
                    ),
                  ),
                  'bundle_options' =>
                  array (
                    25 =>
                    array (
                      'option_id' => '25',
                      'label' => __('Memory'),
                      'value' =>
                      array (
                        0 =>
                        array (
                          'title' => __('Crucial 1GB PC4200 DDR2 533MHz Memory'),
                          'qty' => 1,
                          'price' => 301.98,
                        ),
                      ),
                    ),
                    26 =>
                    array (
                      'option_id' => '26',
                      'label' => __('Keyboard'),
                      'value' =>
                      array (
                        0 =>
                        array (
                          'title' => __('Logitech diNovo Edge Keyboard'),
                          'qty' => 1,
                          'price' => 479.98,
                        ),
                      ),
                    ),
                  ),
                  'product_calculations' => 0,
                  'shipment_type' => '1',
                  'giftcard_lifetime' => NULL,
                  'giftcard_is_redeemable' => 0,
                  'giftcard_email_template' => NULL,
                  'giftcard_type' => NULL,
                )),
            'weight' => '2.0000',
            'is_virtual' => '0',
            'sku' => 'dynamic price',
            'name' => __('Bundle product dynamic price'),
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
            'price' => '781.9600',
            'base_price' => '390.9800',
            'original_price' => '781.9600',
            'base_original_price' => '390.9800',
            'tax_percent' => '0.0000',
            'tax_amount' => '60.4000',
            'base_tax_amount' => '30.2000',
            'tax_invoiced' => '0.0000',
            'base_tax_invoiced' => '0.0000',
            'discount_percent' => '0.0000',
            'discount_amount' => '0.0000',
            'base_discount_amount' => '0.0000',
            'discount_invoiced' => '0.0000',
            'base_discount_invoiced' => '0.0000',
            'amount_refunded' => '0.0000',
            'base_amount_refunded' => '0.0000',
            'row_total' => '781.9600',
            'base_row_total' => '390.9800',
            'row_invoiced' => '781.9600',
            'base_row_invoiced' => '390.9800',
            'row_weight' => '1.0000',
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
            'hidden_tax_amount' => NULL,
            'base_hidden_tax_amount' => NULL,
            'hidden_tax_invoiced' => '0.0000',
            'base_hidden_tax_invoiced' => '0.0000',
            'hidden_tax_refunded' => '0.0000',
            'base_hidden_tax_refunded' => NULL,
            'is_nominal' => '0',
            'tax_canceled' => NULL,
            'hidden_tax_canceled' => NULL,
            'tax_refunded' => '30.2000',
            'price_incl_tax' => '842.3600',
            'base_price_incl_tax' => '421.1800',
            'row_total_incl_tax' => '842.3600',
            'base_row_total_incl_tax' => '421.1800',
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
                'item_id' => '54',
                'order_id' => '-1',
                'parent_item_id' => '53',
                'quote_item_id' => '69',
                'store_id' => '1',
                'created_at' => '2011-04-28 11:38:02',
                'updated_at' => '2011-04-28 12:05:18',
                'product_id' => '141',
                'product_type' => 'simple',
                'product_options' =>
                    serialize(array (
                      'info_buyRequest' =>
                      array (
                        'uenc' => 'aHR0cDovL3JlZC5sb2NhbGhvc3QuY29tL2dpZnQtY2FyZHMvYnVuZGxlLXByb'
                            . '2R1Y3QtZHluYW1pYy1wcmljZS5odG1sP29wdGlvbnM9Y2FydA,,',
                        'product' => '170',
                        'related_product' => '',
                        'qty' => '1',
                        'bundle_option_qty' =>
                        array (
                          25 => '1',
                          26 => '1',
                        ),
                        'bundle_option' =>
                        array (
                          25 => '68',
                          26 => '70',
                        ),
                      ),
                      'giftcard_lifetime' => NULL,
                      'giftcard_is_redeemable' => 0,
                      'giftcard_email_template' => NULL,
                      'giftcard_type' => NULL,
                      'bundle_selection_attributes' =>
                      serialize(array (
                        'price' => 301.98,
                        'qty' => '1',
                        'option_label' => __('Memory'),
                        'option_id' => '25',
                       )),
                    )),
                'weight' => '1.0000',
                'is_virtual' => '0',
                'sku' => '1gbdimm',
                'name' => __('Crucial 1GB PC4200 DDR2 533MHz Memory'),
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
                'base_cost' => NULL,
                'price' => '301.9800',
                'base_price' => '150.9900',
                'original_price' => '301.9800',
                'base_original_price' => '150.9900',
                'tax_percent' => '20.0000',
                'tax_amount' => '60.4000',
                'base_tax_amount' => '30.2000',
                'tax_invoiced' => '60.4000',
                'base_tax_invoiced' => '30.2000',
                'discount_percent' => '10.0000',
                'discount_amount' => '30.2000',
                'base_discount_amount' => '15.1000',
                'discount_invoiced' => '30.2000',
                'base_discount_invoiced' => '15.1000',
                'amount_refunded' => '0.0000',
                'base_amount_refunded' => '0.0000',
                'row_total' => '301.9800',
                'base_row_total' => '150.9900',
                'row_invoiced' => '301.9800',
                'base_row_invoiced' => '150.9900',
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
                'tax_refunded' => '30.2000',
                'price_incl_tax' => '362.3800',
                'base_price_incl_tax' => '181.1900',
                'row_total_incl_tax' => '362.3800',
                'base_row_total_incl_tax' => '181.1900',
            ),
            array (
                'item_id' => '55',
                'order_id' => '-1',
                'parent_item_id' => '53',
                'quote_item_id' => '70',
                'store_id' => '1',
                'created_at' => '2011-04-28 11:38:02',
                'updated_at' => '2011-04-28 12:05:18',
                'product_id' => '161',
                'product_type' => 'simple',
                'product_options' =>
                    serialize(array (
                      'info_buyRequest' =>
                      array (
                        'uenc' => 'aHR0cDovL3JlZC5sb2NhbGhvc3QuY29tL2dpZnQtY2FyZHMvYnVuZGxlLXBy'
                            . 'b2R1Y3QtZHluYW1pYy1wcmljZS5odG1sP29wdGlvbnM9Y2FydA,,',
                        'product' => '170',
                        'related_product' => '',
                        'qty' => '1',
                        'bundle_option_qty' =>
                        array (
                          25 => '1',
                          26 => '1',
                        ),
                        'bundle_option' =>
                        array (
                          25 => '68',
                          26 => '70',
                        ),
                      ),
                      'giftcard_lifetime' => NULL,
                      'giftcard_is_redeemable' => 0,
                      'giftcard_email_template' => NULL,
                      'giftcard_type' => NULL,
                      'bundle_selection_attributes' =>
                      serialize(array (
                          'price' => 479.98,
                          'qty' => '1',
                          'option_label' => __('Keyboard'),
                          'option_id' => '26',
                        )),
                    )),
                'weight' => '1.0000',
                'is_virtual' => '0',
                'sku' => 'logidinovo',
                'name' => __('Logitech diNovo Edge Keyboard'),
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
                'base_cost' => NULL,
                'price' => '479.9800',
                'base_price' => '239.9900',
                'original_price' => '479.9800',
                'base_original_price' => '239.9900',
                'tax_percent' => '0.0000',
                'tax_amount' => '0.0000',
                'base_tax_amount' => '0.0000',
                'tax_invoiced' => '0.0000',
                'base_tax_invoiced' => '0.0000',
                'discount_percent' => '10.0000',
                'discount_amount' => '47.9900',
                'base_discount_amount' => '24.0000',
                'discount_invoiced' => '47.9900',
                'base_discount_invoiced' => '24.0000',
                'amount_refunded' => '0.0000',
                'base_amount_refunded' => '0.0000',
                'row_total' => '479.9800',
                'base_row_total' => '239.9900',
                'row_invoiced' => '479.9800',
                'base_row_invoiced' => '239.9900',
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
                'price_incl_tax' => '479.9800',
                'base_price_incl_tax' => '239.9900',
                'row_total_incl_tax' => '479.9800',
                'base_row_total_incl_tax' => '239.9900',
            ),
        );
    }
}
