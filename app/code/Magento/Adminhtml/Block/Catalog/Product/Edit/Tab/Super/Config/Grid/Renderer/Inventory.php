<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Configurable product assocciated products grid in stock renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid_Renderer_Inventory extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column value
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $inStock = $this->_getValue($row);
        return $inStock ?
               __('In Stock')
               : __('Out of Stock');
    }

}
