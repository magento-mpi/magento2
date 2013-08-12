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
 * Renderer for name field
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Item_Renderer_Bundle_Column_Name
    extends Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Name
{
    /**
     * Returns value HTML if corresponding order item has parent;
     * otherwise returns deafult column HTML
     *
     * @see Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Name::getHtml()
     * @return string HTML
     */
    public function getHtml()
    {
        return ($this->getItem()->getOrderItem()->getParentItem())
            ? $this->getValueHtml($this->getItem())
            : parent::getHtml();
    }

    /**
     * Retrieve Value HTML
     *
     * @param Magento_Sales_Order_Item $item
     * @return string
     */
    public function getValueHtml($item)
    {
        $parentBlock = $this->getItemsGridBlock();
        $result = strip_tags($item->getName());
        if (!$parentBlock->isShipmentSeparately($item)) {
            $attributes = $parentBlock->getSelectionAttributes($item);
            if ($attributes) {
                $result =  sprintf('%d', $attributes['qty']) . ' x ' . $result;
            }
        }
        if (!$parentBlock->isChildCalculated($item)) {
            $attributes = $parentBlock->getSelectionAttributes($item);
            if ($attributes) {
                $result .= " " . strip_tags($item->getOrderItem()->getOrder()->formatPrice($attributes['price']));
            }
        }

        return $result;
    }
}
