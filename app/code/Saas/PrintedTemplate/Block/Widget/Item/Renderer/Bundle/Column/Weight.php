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
 * Renderer for Weight field of bundle product
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Item_Renderer_Bundle_Column_Weight
    extends Magento_Backend_Block_Abstract
    implements Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Abstract
{
    /**
     * Returns weight of the item if it has parent item and can be shipped sepparately
     * or doesn't have parent and cannot be shipped separately
     *
     * @see Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Abstract::getHtml()
     * @return string Weight or empty string
     */
    public function getHtml()
    {
        $parentBlock = $this->getParentBlock();
        $item = $this->getItem();
        $hasWeight = $parentBlock->isShipmentSeparately($item) && $item->getParentItem()
            || !$parentBlock->isShipmentSeparately($item) && !$item->getParentItem();

        return $hasWeight ? $item->getWeight() : '';
    }
}
