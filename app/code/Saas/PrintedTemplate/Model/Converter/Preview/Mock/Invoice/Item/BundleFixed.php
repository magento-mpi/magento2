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
 * Mock object for invoice item model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Invoice_Item_BundleFixed
    extends Mage_Sales_Model_Order_Invoice_Item
{
    /**
     * @var Saas_PrintedTemplate_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize invoice item with mock data
     */
    public function init()
    {
        $this->_helper = Mage::helper('Saas_PrintedTemplate_Helper_Data');
        $this->setData($this->_getMockData());
    }

    /**
     * Returns initialized child items
     *
     * @return array Array of items
     */
    public function getChildrenItems()
    {
        $items = array();
        foreach ($this->_getChildrenMockData() as $data) {
            $items[] = Mage::getModel('Mage_Sales_Model_Order_Invoice_Item')
                ->setData($data)
                ->setInvoice($this->getInvoice());
        }

        return $items;
    }

    /**
     * Returns data for the invoice item
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array(
            'entity_id' => '55',
            'parent_id' => '-1',
            'base_price' => '670.0000',
            'base_weee_tax_row_disposition' => '0.0000',
            'weee_tax_applied_row_amount' => '0.0000',
            'base_weee_tax_applied_amount' => '0.0000',
            'tax_amount' => '268.0000',
            'base_row_total' => '670.0000',
            'discount_amount' => '134.0000',
            'row_total' => '1340.0000',
            'weee_tax_row_disposition' => '0.0000',
            'base_discount_amount' => '67.0000',
            'base_weee_tax_disposition' => '0.0000',
            'price_incl_tax' => '1608.0000',
            'weee_tax_applied_amount' => '0.0000',
            'base_tax_amount' => '134.0000',
            'base_price_incl_tax' => '804.0000',
            'qty' => '1.0000',
            'weee_tax_disposition' => '0.0000',
            'base_cost' => NULL,
            'base_weee_tax_applied_row_amount' => '0.0000',
            'price' => '1340.0000',
            'base_row_total_incl_tax' => '804.0000',
            'row_total_incl_tax' => '1608.0000',
            'product_id' => '169',
            'order_item_id' => '56',
            'additional_data' => NULL,
            'description' => NULL,
            'weee_tax_applied' => 'a:0:{}',
            'sku' => '23',
            'name' => __('Bundle product fixed price'),
            'shipment_id' => NULL,
            'hidden_tax_amount' => '0.0000',
            'base_hidden_tax_amount' => '0.0000',
        );
    }

    /**
     * Returns data for children of bundle product
     *
     * @return array
     */
    protected function _getChildrenMockData()
    {
        return array(
            array (
                'entity_id' => '56',
                'parent_id' => '-1',
                'base_price' => '0.0000',
                'base_weee_tax_row_disposition' => '0.0000',
                'weee_tax_applied_row_amount' => '110.0000',
                'base_weee_tax_applied_amount' => '55.0000',
                'tax_amount' => NULL,
                'base_row_total' => '0.0000',
                'discount_amount' => NULL,
                'row_total' => '0.0000',
                'weee_tax_row_disposition' => '0.0000',
                'base_discount_amount' => NULL,
                'base_weee_tax_disposition' => '0.0000',
                'price_incl_tax' => NULL,
                'weee_tax_applied_amount' => '110.0000',
                'base_tax_amount' => NULL,
                'base_price_incl_tax' => NULL,
                'qty' => '1.0000',
                'weee_tax_disposition' => '0.0000',
                'base_cost' => NULL,
                'base_weee_tax_applied_row_amount' => '55.0000',
                'price' => '0.0000',
                'base_row_total_incl_tax' => NULL,
                'row_total_incl_tax' => NULL,
                'product_id' => '168',
                'order_item_id' => '57',
                'additional_data' => NULL,
                'description' => NULL,
                'weee_tax_applied' => 'a:1:{i:0;a:9:{s:5:"title";s:4:"weee";s:11:"base_amount";s:7:"55.0000";'
                    . 's:6:"amount";d:110;s:10:"row_amount";d:110;s:15:"base_row_amount";d:55;'
                    . 's:20:"base_amount_incl_tax";s:7:"55.0000";s:15:"amount_incl_tax";d:110;'
                    . 's:19:"row_amount_incl_tax";d:110;s:24:"base_row_amount_incl_tax";d:55;}}',
                'sku' => '234222',
                'name' => __('Electronics product'),
                'shipment_id' => NULL,
                'hidden_tax_amount' => NULL,
                'base_hidden_tax_amount' => NULL,
            ),
            array (
                'entity_id' => '57',
                'parent_id' => '-1',
                'base_price' => '0.0000',
                'base_weee_tax_row_disposition' => '0.0000',
                'weee_tax_applied_row_amount' => '0.0000',
                'base_weee_tax_applied_amount' => '0.0000',
                'tax_amount' => NULL,
                'base_row_total' => '0.0000',
                'discount_amount' => NULL,
                'row_total' => '0.0000',
                'weee_tax_row_disposition' => '0.0000',
                'base_discount_amount' => NULL,
                'base_weee_tax_disposition' => '0.0000',
                'price_incl_tax' => '0.0000',
                'weee_tax_applied_amount' => '0.0000',
                'base_tax_amount' => NULL,
                'base_price_incl_tax' => '0.0000',
                'qty' => '1.0000',
                'weee_tax_disposition' => '0.0000',
                'base_cost' => NULL,
                'base_weee_tax_applied_row_amount' => '0.0000',
                'price' => '0.0000',
                'base_row_total_incl_tax' => '0.0000',
                'row_total_incl_tax' => '0.0000',
                'product_id' => '140',
                'order_item_id' => '58',
                'additional_data' => NULL,
                'description' => NULL,
                'weee_tax_applied' => 'a:0:{}',
                'sku' => '2gbdimm',
                'name' => __('Crucial 2GB PC4200 DDR2 533MHz Memory'),
                'shipment_id' => NULL,
                'hidden_tax_amount' => NULL,
                'base_hidden_tax_amount' => NULL,
            ),
        );
    }
}
