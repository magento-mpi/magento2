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
 * Adminhtml Report Customers Reviews renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Report\Grid\Column\Renderer;

class Customer
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $id   = $row->getCustomerId();

        if (!$id) {
            return __('Show Reviews');
        }

        return sprintf('<a href="%s">%s</a>',
            $this->getUrl('*/catalog_product_review', array('customerId' => $id)),
            __('Show Reviews')
        );
    }
}
