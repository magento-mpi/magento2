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
 * Mock object for shipment item model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment_Item_BundleDynamic
    extends Mage_Sales_Model_Order_Shipment_Item
{
    /**
     * Initialize shipment item with mock data
     */
    public function init()
    {
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
            $items[] = Mage::getModel('Mage_Sales_Model_Order_Shipment_Item')
                ->setData($data)
                ->setShipment($this->getShipment());
        }

        return $items;
    }

    /**
     * Returns data for the shipment item
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array (
            'entity_id' => '19',
            'parent_id' => '-1',
            'row_total' => NULL,
            'price' => '781.9600',
            'weight' => '2.0000',
            'qty' => '1.0000',
            'product_id' => '170',
            'order_item_id' => '53',
            'additional_data' => NULL,
            'description' => NULL,
            'name' => __('Bundle product dynamic price'),
            'sku' => 'dynamic price',
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
                'row_total' => NULL,
                'price' => '301.9800',
                'weight' => '1.0000',
                'qty' => '1.0000',
                'product_id' => '141',
                'order_item_id' => '54',
                'additional_data' => NULL,
                'description' => NULL,
                'name' => __('Crucial 1GB PC4200 DDR2 533MHz Memory'),
                'sku' => '1gbdimm',
            ),
            array (
                'entity_id' => '21',
                'parent_id' => '-1',
                'row_total' => NULL,
                'price' => '479.9800',
                'weight' => '1.0000',
                'qty' => '1.0000',
                'product_id' => '161',
                'order_item_id' => '55',
                'additional_data' => NULL,
                'description' => NULL,
                'name' => __('Logitech diNovo Edge Keyboard'),
                'sku' => 'logidinovo',
            ),
        );
    }
}
