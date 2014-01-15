<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Report Products Reviews renderer
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Grid\Column\Renderer;

class Product
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $id   = $row->getId();

        return sprintf('<a href="%s">%s</a>',
            $this->getUrl('catalog/product_review/', array('productId' => $id)),
            __('Show Reviews')
        );
    }
}
