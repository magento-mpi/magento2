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
 * Mock object for creditmemo item model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Creditmemo_Item_BundleFixed
    extends Mage_Sales_Model_Order_Creditmemo_Item
{
    /**
     * @var Saas_PrintedTemplate_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize creditmemo item with mock data
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
            $items[] = Mage::getModel('Mage_Sales_Model_Order_Creditmemo_Item')
                ->setData($data)
                ->setCreditmemo($this->getCreditmemo());
        }

        return $items;
    }

    /**
     * Returns data for the creditmemo item
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array (
            'entity_id' => '22',
            'parent_id' => '-1',
            'weee_tax_applied_row_amount' => '0.0000',
            'base_price' => '670.0000',
            'base_weee_tax_row_disposition' => '0.0000',
            'tax_amount' => '268.0000',
            'base_weee_tax_applied_amount' => '0.0000',
            'weee_tax_row_disposition' => '0.0000',
            'base_row_total' => '670.0000',
            'discount_amount' => '134.0000',
            'row_total' => '1340.0000',
            'weee_tax_applied_amount' => '0.0000',
            'base_discount_amount' => '67.0000',
            'base_weee_tax_disposition' => '0.0000',
            'price_incl_tax' => '1608.0000',
            'base_tax_amount' => '134.0000',
            'weee_tax_disposition' => '0.0000',
            'base_price_incl_tax' => '804.0000',
            'qty' => '1.0000',
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
            'name' => $this->_helper->__('Bundle product fixed price'),
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
                'entity_id' => '23',
                'parent_id' => '-1',
                'weee_tax_applied_row_amount' => '110.0000',
                'base_price' => '0.0000',
                'base_weee_tax_row_disposition' => '0.0000',
                'tax_amount' => NULL,
                'base_weee_tax_applied_amount' => '55.0000',
                'weee_tax_row_disposition' => '0.0000',
                'base_row_total' => '0.0000',
                'discount_amount' => NULL,
                'row_total' => '0.0000',
                'weee_tax_applied_amount' => '110.0000',
                'base_discount_amount' => NULL,
                'base_weee_tax_disposition' => '0.0000',
                'price_incl_tax' => NULL,
                'base_tax_amount' => NULL,
                'weee_tax_disposition' => '0.0000',
                'base_price_incl_tax' => NULL,
                'qty' => '1.0000',
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
                'name' => $this->_helper->__('Electronics product'),
                'hidden_tax_amount' => NULL,
                'base_hidden_tax_amount' => NULL,
            ),
            array (
                'entity_id' => '24',
                'parent_id' => '-1',
                'weee_tax_applied_row_amount' => '0.0000',
                'base_price' => '0.0000',
                'base_weee_tax_row_disposition' => '0.0000',
                'tax_amount' => NULL,
                'base_weee_tax_applied_amount' => '0.0000',
                'weee_tax_row_disposition' => '0.0000',
                'base_row_total' => '0.0000',
                'discount_amount' => NULL,
                'row_total' => '0.0000',
                'weee_tax_applied_amount' => '0.0000',
                'base_discount_amount' => NULL,
                'base_weee_tax_disposition' => '0.0000',
                'price_incl_tax' => '0.0000',
                'base_tax_amount' => NULL,
                'weee_tax_disposition' => '0.0000',
                'base_price_incl_tax' => '0.0000',
                'qty' => '1.0000',
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
                'name' => $this->_helper->__('Crucial 2GB PC4200 DDR2 533MHz Memory'),
                'hidden_tax_amount' => NULL,
                'base_hidden_tax_amount' => NULL,
            ),
        );
    }
}