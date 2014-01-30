<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Grid\Column\Renderer;

/**
 * Adminhtml Report Customers Reviews renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Customer
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $id   = $row->getCustomerId();

        if (!$id) {
            return __('Show Reviews');
        }

        return sprintf('<a href="%s">%s</a>',
            $this->getUrl('catalog/product_review/', array('customerId' => $id)),
            __('Show Reviews')
        );
    }
}
