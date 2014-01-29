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
 * Adminhtml Report Customers Reviews renderer
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Block\Adminhtml\Grid\Column\Renderer;

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
