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
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Creditmemo_Item_BundleDynamic
    extends Magento_Sales_Model_Order_Creditmemo_Item
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
            $items[] = Mage::getModel('Magento_Sales_Model_Order_Creditmemo_Item')
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
            'entity_id' => '19',
            'parent_id' => '-1',
            'weee_tax_applied_row_amount' => '0.0000',
            'base_price' => '390.9800',
            'base_weee_tax_row_disposition' => '0.0000',
            'tax_amount' => NULL,
            'base_weee_tax_applied_amount' => '0.0000',
            'weee_tax_row_disposition' => '0.0000',
            'base_row_total' => '390.9800',
            'discount_amount' => NULL,
            'row_total' => '781.9600',
            'weee_tax_applied_amount' => '0.0000',
            'base_discount_amount' => NULL,
            'base_weee_tax_disposition' => '0.0000',
            'price_incl_tax' => '842.3600',
            'base_tax_amount' => NULL,
            'weee_tax_disposition' => '0.0000',
            'base_price_incl_tax' => '421.1800',
            'qty' => '1.0000',
            'base_cost' => NULL,
            'base_weee_tax_applied_row_amount' => '0.0000',
            'price' => '781.9600',
            'base_row_total_incl_tax' => '421.1800',
            'row_total_incl_tax' => '842.3600',
            'product_id' => '170',
            'order_item_id' => '53',
            'additional_data' => NULL,
            'description' => NULL,
            'weee_tax_applied' => 'a:0:{}',
            'sku' => 'dynamic price',
            'name' => $this->_helper->__('Bundle product dynamic price'),
            'hidden_tax_amount' => NULL,
            'base_hidden_tax_amount' => NULL,
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
                'entity_id' => '20',
                'parent_id' => '-1',
                'weee_tax_applied_row_amount' => '0.0000',
                'base_price' => '150.9900',
                'base_weee_tax_row_disposition' => '0.0000',
                'tax_amount' => '60.4000',
                'base_weee_tax_applied_amount' => '0.0000',
                'weee_tax_row_disposition' => '0.0000',
                'base_row_total' => '150.9900',
                'discount_amount' => '30.2000',
                'row_total' => '301.9800',
                'weee_tax_applied_amount' => '0.0000',
                'base_discount_amount' => '15.1000',
                'base_weee_tax_disposition' => '0.0000',
                'price_incl_tax' => '362.3800',
                'base_tax_amount' => '30.2000',
                'weee_tax_disposition' => '0.0000',
                'base_price_incl_tax' => '181.1900',
                'qty' => '1.0000',
                'base_cost' => NULL,
                'base_weee_tax_applied_row_amount' => '0.0000',
                'price' => '301.9800',
                'base_row_total_incl_tax' => '181.1900',
                'row_total_incl_tax' => '362.3800',
                'product_id' => '141',
                'order_item_id' => '54',
                'additional_data' => NULL,
                'description' => NULL,
                'weee_tax_applied' => 'a:0:{}',
                'sku' => '1gbdimm',
                'name' => $this->_helper->__('Crucial 1GB PC4200 DDR2 533MHz Memory'),
                'hidden_tax_amount' => '0.0000',
                'base_hidden_tax_amount' => '0.0000',
            ),
            array (
                'entity_id' => '21',
                'parent_id' => '-1',
                'weee_tax_applied_row_amount' => '0.0000',
                'base_price' => '239.9900',
                'base_weee_tax_row_disposition' => '0.0000',
                'tax_amount' => '0.0000',
                'base_weee_tax_applied_amount' => '0.0000',
                'weee_tax_row_disposition' => '0.0000',
                'base_row_total' => '239.9900',
                'discount_amount' => '47.9900',
                'row_total' => '479.9800',
                'weee_tax_applied_amount' => '0.0000',
                'base_discount_amount' => '24.0000',
                'base_weee_tax_disposition' => '0.0000',
                'price_incl_tax' => '479.9800',
                'base_tax_amount' => '0.0000',
                'weee_tax_disposition' => '0.0000',
                'base_price_incl_tax' => '239.9900',
                'qty' => '1.0000',
                'base_cost' => NULL,
                'base_weee_tax_applied_row_amount' => '0.0000',
                'price' => '479.9800',
                'base_row_total_incl_tax' => '239.9900',
                'row_total_incl_tax' => '479.9800',
                'product_id' => '161',
                'order_item_id' => '55',
                'additional_data' => NULL,
                'description' => NULL,
                'weee_tax_applied' => 'a:0:{}',
                'sku' => 'logidinovo',
                'name' => $this->_helper->__('Logitech diNovo Edge Keyboard'),
                'hidden_tax_amount' => '0.0000',
                'base_hidden_tax_amount' => '0.0000',
            ),
        );
    }
}
