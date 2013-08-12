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
 * Renderer for Qty field of bundle product
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Item_Renderer_Bundle_Column_Qty
    extends Magento_Backend_Block_Abstract
    implements Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Abstract
{
    /**
     * Returns quantity of item if parent block can show price info
     * or item is a shipment item and item is a child item and parent block can be shipped separately
     * or item is a shipment item and item is not a child item and parent block cannot be shipped separately.
     *
     * @see Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Abstract::getHtml()
     * @return string Quantity of item or empty string
     */
    public function getHtml()
    {
        $parentBlock = $this->getItemsGridBlock();
        $item = $this->getItem();

        $hasQty = $item->getItemType() == 'item_shipment'
                ? $parentBlock->isShipmentSeparately($item) && $item->getParentItem()
                    || !$parentBlock->isShipmentSeparately($item) && !$item->getParentItem()
                : $parentBlock->canShowPriceInfo($item);

        return $hasQty ? $item->getQty() : '';
    }
}
