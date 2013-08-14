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
 * Renderer for SKU field
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Item_Renderer_Bundle_Column_Sku
    extends Magento_Backend_Block_Abstract
    implements Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Abstract
{
    /**
     * If order item of item has parent or item is a shipment item returns it's SKU
     * empty string otherwise.
     *
     * @see Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Abstract::getHtml()
     * @return string SKU or empty string
     */
    public function getHtml()
    {
        $item = $this->getItem();
        $hasSku = !$item->getOrderItem()->getParentItem()
            || $item->getItemType() == 'item_shipment';

        return $hasSku ? $item->getSku() : '';
    }
}
