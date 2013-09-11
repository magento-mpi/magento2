<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Description renderer
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\Renderer;

class Html
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Return data "as is", don't escape HTML
     *
     * @param \Magento\Object $row
     * @return mixed
     */
    public function render(\Magento\Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}
