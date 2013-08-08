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
 * Renderer for bundle grid item
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Item_Renderer_Bundle
    extends Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default
{
    /**
     * Set template for the block
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::widget/items_grid/item/bundle.phtml');
    }

    /**
     * Render field if it's not a price or if it price and we can show price
     *
     * @see Saas_PrintedTemplate_Block_Widget_Item_Renderer_Bundle::canShowPriceInfo()
     * @param string $property Field name
     * @return mixed
     */
    public function renderField($property)
    {
        if (!$this->getItemsGridBlock()->isPriceProperty($property)
            || ($this->getItemsGridBlock()->isPriceProperty($property) && $this->canShowPriceInfo($this->getItem()))) {

            return parent::renderField($property);
        }
    }

    /**
     * Check is price info can be shown for item
     *
     * @param $item
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        return $item->getOrderItem()->getParentItem() && $this->isChildCalculated($item)
                || !$item->getOrderItem()->getParentItem() && !$this->isChildCalculated($item);
    }

    /**
     * Returns is child calculated
     *
     * @param Magento_Object $item
     * @return bool
     */
    public function isChildCalculated($item)
    {
        if ($item->getOrderItem()) {
            $item = $item->getOrderItem();
        }

        $options = $item->getParentItem()
            ? $item->getParentItem()->getProductOptions()
            : $item->getProductOptions();

        return $options && isset($options['product_calculations'])
            && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD;
    }

    /**
     * Returns Selection attributes
     *
     * @param Magento_Object $item
     * @return array|null
     */
    public function getSelectionAttributes($item)
    {
        $options = ($item instanceof Mage_Sales_Model_Order_Item)
            ? $item->getProductOptions()
            : $item->getOrderItem()->getProductOptions();

        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }
    }

    /**
     * Retrieve is Shipment Separately flag for Item
     *
     * @param Magento_Object $item
     * @return bool
     */
    public function isShipmentSeparately($item)
    {
        if ($item->getOrderItem()) {
            $item = $item->getOrderItem();
        }

        $options = $item->getParentItem()
            ? $item->getParentItem()->getProductOptions()
            : $item->getProductOptions();

        $shipmentSeparately = $options && isset($options['shipment_type'])
            && $options['shipment_type'] == Mage_Catalog_Model_Product_Type_Abstract::SHIPMENT_SEPARATELY;

        return $item->getParentItem() ? $shipmentSeparately : !$shipmentSeparately;
    }

    /**
     * Get child items grouped by attribute
     *
     * @return array
     */
    public function getChildren()
    {
        $items = $this->getItem()->getChildren();
        $groupedChilds = array();
        foreach ($items as $item) {
            $attribute = $this->getSelectionAttributes($item);
            $optionId = (is_array($attribute)) ? $attribute['option_id'] : 0;
            if (!isset($groupedChilds[$optionId])) {
                $groupedChilds[$optionId]['attribute'] = $attribute;
                $groupedChilds[$optionId]['items'] = array();
            }

            $groupedChilds[$optionId]['items'][] = $item;
        }

        return $groupedChilds;
    }
}
