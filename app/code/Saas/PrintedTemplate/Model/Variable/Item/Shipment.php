<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for Mage_Sales_Model_Order_Shipment_Item for shipment item variable
 *
 * Container that can restrict access to properties and method
 * with black list or white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Item_Shipment extends Saas_PrintedTemplate_Model_Variable_Item_Abstract
{
    /**
     * Item variable name
     *
     * @var string
     */
    protected $_itemType = 'item_shipment';

    /**
     * Retrieve item's parent entity
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _getParentEntity()
    {
        return $this->_value->getShipment();
    }

    /**
     * Get child items
     *
     * @return array
     */
    public function getChildren()
    {
        $items = $this->_getParentEntity()->getAllItems();

        $parentItemId = $this->_value->getOrderItem()->getId();
        $children = array();
        foreach ($items as $item) {
            $parentItem = $item->getOrderItem()->getParentItem();
            if ($parentItem && $parentItem->getId() == $parentItemId) {
                $children[$item->getOrderItemId()] = $this->_getVariableModel(array('value' => $item));
            } else if (!$parentItem && $item->getOrderItem()->getId() == $parentItemId) {
                $children[$item->getOrderItemId()] = $this->_getVariableModel(array('value' => $item));
                if ($item->getOrderItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                    foreach ($item->getOrderItem()->getChildrenItems() as $orderItem) {
                        $orderItem->setOrderItem($orderItem);
                        $children[$orderItem->getId()] =
                            $this->_getVariableModel(array('value' => $orderItem));
                    }
                }
            }
        }

        return $children;
    }

    /**
     * Formats currency using order formater
     *
     * @param float
     * @return string
     */
    public function formatCurrency($value)
    {
        if ($value === null) {
            return '';
        }

        if ($order = $this->_value->getOrder()) {
            return $order->formatPriceTxt($value);
        }

        return $this->_getParentEntity()->getOrder()->formatPriceTxt($value);
    }
}
