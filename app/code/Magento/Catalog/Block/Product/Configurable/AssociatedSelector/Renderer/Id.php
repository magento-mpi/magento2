<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ID column renderer, also contains image URL in hidden field
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Catalog_Block_Product_Configurable_AssociatedSelector_Renderer_Id
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render grid row
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $imageUrl = $row->getImage() && $row->getImage() != 'no_selection'
            ? $this->escapeHtml($this->_helperFactory->get('Magento_Catalog_Helper_Product')->getImageUrl($row))
            : '';
        return $this->_getValue($row) . '<input type="hidden" data-role="image-url" value="' . $imageUrl . '"/>';
    }
}
