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
 * Renderer for attribute block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config;

class Attribute
    extends \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Config
{
    /**
     * Render block
     *
     * @param array $arguments
     * @return string
     */
    public function render(array $arguments)
    {
        $this->assign($arguments);
        return $this->toHtml();
    }
}
