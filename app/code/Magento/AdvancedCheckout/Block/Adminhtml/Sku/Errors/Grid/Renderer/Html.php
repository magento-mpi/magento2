<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\Renderer;

/**
 * Description renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Html extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Return data "as is", don't escape HTML
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}
