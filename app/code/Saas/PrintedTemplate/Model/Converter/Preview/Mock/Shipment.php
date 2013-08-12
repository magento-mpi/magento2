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
 * Mock object for shipment model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment extends Magento_Sales_Model_Order_Shipment
{
    /**
     * Initialize shipment with mock data
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        parent::setOrder($order);

        $this->setData($this->_getMockData());

        foreach ($this->_getMockItems() as $item) {
            $id = $item->getId();
            $item->unsetData('entity_id');
            $item->setShipment($this);
            $this->addItem($item);
            $item->setId($id);
        }

        $this->_tracks = $this->getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment_Track_Collection');
        return $this;
    }

    /**
     * Returns model instance
     *
     * @param string $className
     * @return mixed
     */
    public function getModel($className)
    {
        return Mage::getModel($className);
    }

    /**
     * Returns array of mock items
     *
     * @return array
     */
    protected function _getMockItems()
    {
        $bundleDynamic = $this->_createItemMock('bundleDynamic');

        $items = array(
            $this->_createItemMock('configurable'),
            $bundleDynamic,
        );
        $items = array_merge($items, $bundleDynamic->getChildrenItems());

        return $items;
    }

    /**
     * Create mock object for order items and for specified product type
     *
     * @param string $type
     */
    protected function _createItemMock($type)
    {
        $item = $this->getModel('Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment_Item_' . ucfirst($type));
        $item->init();
        $item->setShipment($this);

        return $item;
    }

    /**
     * Returns data for the shipment
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array (
            'entity_id' => '-1',
            'store_id' => '1',
            'total_weight' => NULL,
            'total_qty' => '5.0000',
            'email_sent' => NULL,
            'order_id' => '-1',
            'customer_id' => '4',
            'shipping_address_id' => '16',
            'billing_address_id' => '15',
            'shipment_status' => NULL,
            'increment_id' => '100000004',
            'created_at' => '2011-04-28 11:38:50',
            'updated_at' => '2011-05-16 08:03:03',
        );
    }
}
