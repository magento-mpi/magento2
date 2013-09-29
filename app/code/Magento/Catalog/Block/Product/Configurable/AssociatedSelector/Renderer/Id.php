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

namespace Magento\Catalog\Block\Product\Configurable\AssociatedSelector\Renderer;

class Id
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render grid row
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $imageUrl = $row->getImage() && $row->getImage() != 'no_selection'
            ? $this->escapeHtml($this->_helperFactory->get('Magento\Catalog\Helper\Product')->getImageUrl($row))
            : '';
        return $this->_getValue($row) . '<input type="hidden" data-role="image-url" value="' . $imageUrl . '"/>';
    }
}
