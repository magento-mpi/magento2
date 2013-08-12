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
 * Default renderer for grid item
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default extends Magento_Backend_Block_Template
{
    /**
     * Template for renderer class
     *
     * @var string
     */
    const RENDERER_CLASS_NAME = 'Saas_PrintedTemplate_Block_Widget_Item_Renderer_%s_Column_%s';

    /**
     * Set template
     *
     * @see Magento_Core_Block_Template::_construct()
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::widget/items_grid/item/default.phtml');
    }

    /**
     * Render item property by property name
     *
     * @param string $property Property name
     * @return string|null
     */
    public function renderField($property)
    {
        $item = $this->getItem();
        $productType = (!$item->getOrderItem()->getParentItem())
            ? $item->getOrderItem()->getProductType()
            : $item->getOrderItem()->getParentItem()->getProductType();

        if ($renderer = $this->_getFieldRenderer($productType, $property)) {
            $result = $renderer
                ->setItemsGridBlock($this)
                ->setItem($item)
                ->getHtml();
        } else {
            $method = 'get' . uc_words($property, '');
            $result = $item->$method();
        }

        return $result;
    }

    /**
     * Get column renderer by product type and property name
     * if cannot find tries to load for type default
     *
     * @param string $productType
     * @param string $property
     * @return Magento_Core_Block_Abstract|false
     */
    protected function _getFieldRenderer($productType, $property)
    {
        $productType = ucfirst($productType);
        $property = ucfirst($property);
        $block = sprintf(self::RENDERER_CLASS_NAME, $productType, $property);
        if ($this->_isBlockExists($block)) {
            return $this->getLayout()->createBlock($block);
        }
        // Fallback to default
        if ($productType != 'Default') {
            return $this->_getFieldRenderer('default', $property);
        }

        return false;
    }

    /**
     * Check is renderer block exists
     *
     * @param string $block block name
     * @return bool
     */
    protected function _isBlockExists($block)
    {
        return $block && class_exists($block);
    }
}
